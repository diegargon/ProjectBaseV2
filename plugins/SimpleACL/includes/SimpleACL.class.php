<?php

/**
 *  SimpleACL - Class ACL File
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleACL
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 *  ACL class
 */
class ACL {

    private $debug;
    private $user_permissions;
    private $permissions;

    function __construct() {
        global $cfg, $debug;
        defined('DEBUG') && $cfg['simpleacl_debug'] ? $this->debug = & $debug : $this->debug = false;
        $this->setPerms();
    }

    function getPerms($force = 0) {
        (empty($this->permissions) || $force) ? $this->setPerms() : null;

        return $this->permissions;
    }

    private function setPerms() {
        global $db;

        $this->permissions = [];

        $query = $db->selectAll('permissions', null, 'ORDER BY plugin');
        if ($db->numRows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->permissions[] = $row;
            }
        } else {
            $this->permissions = false;
        }
        $db->free($query);
    }

    function getSessionUserPerms() {
        global $groups;

        $user_groups = $groups->getUserGroups();
        $user_perms = [];
        if ($user_groups !== false && count($user_groups) > 0) {
            foreach ($user_groups as $user_group_id) { //Recorremos los grupos del usuario
                foreach ($this->permissions as $this_perm) { //Recorremos los permisos
                    $perm_groups_id = explode(',', $this_perm['groups']); //Cogemos los grupos a los que afecta el permiso
                    foreach ($perm_groups_id as $perm_group_id) { //Si coincide el grupo del usuario con el grupo que afecta el permiso añadimos el permiso
                        if ($perm_group_id == $user_group_id) {
                            $user_perms[] = $this_perm['perm_id'];
                            break;
                        }
                    }
                }
            }
        } else {
            $user_perms = false;
        }

        return $user_perms;
    }

    function getGroupPerms($group_id) {

        foreach ($this->permissions as $perm) {
            $perm_groups_ids = explode(',', $perm['groups']);

            foreach ($perm_groups_ids as $perm_group_id) {
                if ($group_id == $perm_group_id) {

                    $perm_groups[] = $perm;
                }
            }
        }
        return isset($perm_groups) ? $perm_groups : null;
    }

    function addGroupPerm($group_id, $perm_id) {
        global $db;
        $new_groups = '';
        foreach ($this->permissions as $permissions) {

            if ($permissions['perm_id'] == $perm_id) {
                if (!empty($permissions['groups'])) {
                    $new_groups = $permissions['groups'] . ',' . $group_id;
                    break;
                } else {
                    $new_groups = $group_id;
                }
            }
        }
        if (!empty($new_groups)) {
            $db->update('permissions', ['groups' => $new_groups], ['perm_id' => $perm_id], 'LIMIT 1');
        }
    }

    function deleteGroupPerm($group_id, $perm_id) {
        global $db;
        $new_groups = '';
        foreach ($this->permissions as $permissions) {

            if ($permissions['perm_id'] == $perm_id) {

                if (empty($permissions['groups'])) {

                    break;
                }
                $db_groups_ids = explode(',', $permissions['groups']);
                $first = 0;
                foreach ($db_groups_ids as $db_groups_id) {
                    if ($db_groups_id != $group_id) {
                        if ($first == 0) {
                            $first = 1;
                            $new_groups = $db_groups_id;
                        } else {
                            $new_groups .= ',' . $db_groups_id;
                        }
                    }
                }
            }
        }
        $db->update('permissions', ['groups' => $new_groups], ['perm_id' => $perm_id], 'LIMIT 1');
    }

    private function SetUserPerm() {
        global $sm;

        $user = $sm->getSessionUser();

        if ($this->user_permissions !== false) {
            $this->user_permissions = $this->getSessionUserPerms();
            if (!(count($this->user_permissions) > 0)) {
                return ($this->user_permissions = false);
            }
        }
        return true;
    }

    function acl_ask($perms_demand) {
        global $sm;

        $this->debug ? $this->debug->log('ACL_ASK-> ' . $perms_demand, 'SimpleACL', 'DEBUG') : null;

        $user = $sm->getSessionUser();

        empty($this->permissions) ? $this->SetPerms() : null;
        empty($this->user_permissions) ? $this->SetUserPerm() : null;

        if ($this->permissions == false) {
            $this->debug ? $this->debug->log('ACL permissions is false', 'SimpleACL', 'WARNING') : null;
            return false;
        }

        if ($this->user_permissions == false) {
            $this->debug ? $this->debug->log('ACL user permissions is false', 'SimpleACL', 'WARNING') : null;
            return false;
        }

        //remove/trim white spaces
        $perms_demand = preg_replace('/\s+/', '', $perms_demand);

        return $this->check_perms($perms_demand);
    }

    private function check_perms($perms_demand) {

        if (preg_match("/\|\|/", $perms_demand)) {
            $or_split = preg_split("/\|\|/", $perms_demand);
        } else {
            $or_split[] = $perms_demand;
        }

        foreach ($or_split as $or_split_perm) {
            $auth = false;
            if (!preg_match("/\&\&/", $or_split_perm)) {
                $auth = $this->demanding_perm_check($or_split_perm);
                $this->debug ? $this->debug->log("ACL 1 {$or_split_perm} result->{$auth} ", 'SimpleACL', 'DEBUG') : null;
                if ($auth) {
                    $this->debug ? $this->debug->log('ACL result OR ->true', 'SimpleACL', 'NOTICE') : null;
                    return true;
                } //first OR true, no need check the others
            } else { //&& check all except if any its false
                $and_split = preg_split("/\&\&/", $or_split_perm);

                foreach ($and_split as $and_split_perm) {
                    $auth = $this->demanding_perm_check($and_split_perm);
                    $this->debug ? $this->debug->log("ACL 3 -> \"$and_split_perm\" -> $auth  ", 'SimpleACL', 'DEBUG') : null;
                    if ($auth == false) {
                        $this->debug ? $this->debug->log("ACL 4 -> \"$and_split_perm\" -> Break", 'SimpleACL', 'DEBUG') : null;
                        break; //if any && perm its false, not check the next perms are false
                    }
                }
            }

            if ($auth == true) {
                $this->debug ? $this->debug->log('ACL result AND->true ', 'SimpleACL', 'NOTICE') : null;
                return true;
            } else {
                $this->debug ? $this->debug->log('ACL F result->false', 'SimpleACL', 'DEBUG') : null;
            }
        }
        return false;
    }

    private function demanding_perm_check($perm) {
        $this->debug ? $this->debug->log('ACL Checking ->' . $perm, 'SimpleACL', 'DEBUG') : null;
        //list($perm_group, $perm_type) = preg_split("/_/", $perm);

        if (!($asked_perm = $this->getPermIDByName($perm))) {
            return false;
        }

        foreach ($this->user_permissions as $user_perm_id) {
            if (!($user_perm_data = $this->getPermByPermID($user_perm_id))) {
                return false;
            }
            if (($user_perm_data['perm_id'] == $asked_perm)) {
                $this->debug ? $this->debug->log('Perm found', 'SimpleACL', 'NOTICE') : null;
                return true; //its the exact perm
            }
        }
        return false;
    }

    private function getPermIDByName($perm_name) {
        foreach ($this->permissions as $perm) {
            if ($perm['perm_name'] == $perm_name) {
                return $perm['perm_id'];
            }
        }
        return false;
    }

    function getPermByPermID($perm_id) {
        empty($this->permissions) ? $this->getPerms() : null;

        foreach ($this->permissions as $perm) {
            if (($perm['perm_id'] == $perm_id)) {
                return $perm;
            }
        }
        return false;
    }

}
