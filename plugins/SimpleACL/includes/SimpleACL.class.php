<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * Fast Implementation, need some basic ACL now,  work on this later
 */
!defined('IN_WEB') ? exit : true;

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
        (empty($this->permissions) || $force) ? $this->setPerms() : false;

        return $this->permissions;
    }

    private function setPerms() {
        global $db;

        $query = $db->select_all("permissions", null, "ORDER BY perm_group, perm_level");
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->permissions[] = $row;
            }
        } else {
            $this->permissions = false;
        }
        $db->free($query);
    }

    function getUserPermsByUID($uid) {
        global $groups;

        $user_groups = $groups->getUserGroupsByUID($uid);

        $user_perms = [];
        if (count($user_groups > 0)) {
            foreach ($user_groups as $user_group_id) { //Recorremos los grupos del usuario
                foreach ($this->permissions as $this_perm) { //Recorremos los permisos
                    $perm_groups_id = explode(",", $this_perm['groups']); //Cogemos los grupos a los que afecta el permiso
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
            $perm_groups_ids = explode(",", $perm['groups']);

            foreach ($perm_groups_ids as $perm_group_id) {
                if ($group_id == $perm_group_id) {

                    $perm_groups[] = $perm;
                }
            }
        }
        return isset($perm_groups) ? $perm_groups : false;
    }

    function addGroupPerm($group_id, $perm_id) {
        global $db;
        $new_groups = "";
        foreach ($this->permissions as $permissions) {

            if ($permissions['perm_id'] == $perm_id) {
                if (!empty($permissions['groups'])) {
                    $new_groups = $permissions['groups'] . "," . $group_id;
                    break;
                } else {
                    $new_groups = $group_id;
                }
            }
        }
        if (!empty($new_groups)) {
            $db->update("permissions", ["groups" => $new_groups], ["perm_id" => $perm_id], "LIMIT 1");
        }
    }

    function deleteGroupPerm($group_id, $perm_id) {
        global $db;
        $new_groups = "";
        foreach ($this->permissions as $permissions) {

            if ($permissions['perm_id'] == $perm_id) {

                if (empty($permissions['groups'])) {

                    break;
                }
                $db_groups_ids = explode(",", $permissions['groups']);
                $first = 0;
                foreach ($db_groups_ids as $db_groups_id) {
                    if ($db_groups_id != $group_id) {
                        if ($first == 0) {
                            $first = 1;
                            $new_groups = $db_groups_id;
                        } else {
                            $new_groups .= "," . $db_groups_id;
                        }
                    }
                }
            }
        }
        $db->update("permissions", ["groups" => $new_groups], ["perm_id" => $perm_id], "LIMIT 1");
    }

    private function SetUserPerm() {
        global $sm;

        if (!($user = $sm->getSessionUser())) {
            return ($this->user_permissions = false);
        }

        $this->user_permissions = $this->getUserPermsByUID($user['uid']);
        if (!(count($this->user_permissions) > 0)) {
            return ($this->user_permissins = false);
        }

        return true;
    }

    function acl_ask($perms_demand, $resource = "ALL") {
        global $sm;

        $this->debug ? $this->debug->log("ACL_ASK-> $perms_demand", "SimpleACL", "DEBUG") : false;

        $user = $sm->getSessionUser();
        if (!$user) {
            return false;
        }

        if (empty($this->permissions) || empty($this->user_permissions)) {
            $this->SetPerms();
            $this->SetUserPerm();
        }
        if ($this->permissions == false) {
            $this->debug ? $this->debug->log("ACL permissions is false", "SimpleACL", "WARNING") : false;
            return false;
        }

        if ($this->user_permissions == false) {
            $this->debug ? $this->debug->log("ACL user permissions is false", "SimpleACL", "WARNING") : false;
            return false;
        }
        //remove all white spaces
        $perms_demand = preg_replace('/\s+/', '', $perms_demand);

        return $this->check_perms($perms_demand, $resource);
    }

    private function check_perms($perms_demand, $resource = "ALL") {

        if (preg_match("/\|\|/", $perms_demand)) {
            $or_split = preg_split("/\|\|/", $perms_demand);
        } else {
            $or_split[] = $perms_demand;
        }

        foreach ($or_split as $or_split_perm) {
            $auth = false;
            if (!preg_match("/\&\&/", $or_split_perm)) {
                $auth = $this->demanding_perm_check($or_split_perm, $resource);
                $this->debug ? $this->debug->log("ACL 1 {$or_split_perm} result->{$auth} resource->{$resource}", "SimpleACL", "DEBUG") : false;
                if ($auth) {
                    return true;
                } //first OR true, no need check the others
            } else { //&& check all except if any its false
                $and_split = preg_split("/\&\&/", $or_split_perm);

                foreach ($and_split as $and_split_perm) {
                    $auth = $this->demanding_perm_check($and_split_perm, $resource);
                    $this->debug ? $this->debug->log("ACL 3 -> \"$and_split_perm\" -> $auth  ", "SimpleACL", "DEBUG") : false;
                    if ($auth == false) {
                        $this->debug ? $this->debug->log("ACL 4 -> \"$and_split_perm\" -> Break", "SimpleACL", "DEBUG") : false;
                        break;
                    } //if any && perm its false, not check the next && perms
                }
                if ($auth == true) {
                    $this->debug ? $this->debug->log("ACL result->true", "SimpleACL", "DEBUG") : false;
                    return true;
                } //if auth = true at this point, this group of && perms are all true
            }
        }
        $this->debug ? $this->debug->log("ACL F result->false", "SimpleACL", "DEBUG") : false;
        return false;
    }

    private function demanding_perm_check($perm, $resource = "ALL") {
        $this->debug ? $this->debug->log("ACL Checking -> $perm", "SimpleACL", "DEBUG") : false;
        list($perm_group, $perm_type) = preg_split("/_/", $perm);

        if (!$asked_perm = $this->getPermDataByName($perm_group, $perm_type)) {
            return false;
        }

        foreach ($this->user_permissions as $user_perm_id) {
            if (!$user_perm_data = $this->getPermByPermID($user_perm_id)) {
                return false;
            }
            if (($user_perm_data['perm_id'] == $asked_perm['perm_id']) &&
                    ( ($user_perm_data['resource'] == $resource || $user_perm_data['resource'] == "ALL") )
            ) {
                $this->debug ? $this->debug->log("Exact perm found", "SimpleACL", "DEBUG") : false;
                return true; //its the exact perm
            }
            //Look if perm its upper level
            if (( $asked_perm['perm_group'] == $user_perm_data['perm_group'] ) &&
                    ( $asked_perm['perm_level'] >= $user_perm_data['perm_level'] )
            ) {
                $this->debug ? $this->debug->log("Group up found", "SimpleACL", "DEBUG") : false;
                return true;
            }
        }
        return false;
    }

    private function getPermDataByName($perm_group, $perm_type) {
        foreach ($this->permissions as $perm) {
            if (($perm['perm_group'] == $perm_group) && ($perm['perm_type'] == $perm_type)) {
                return $perm;
            }
        }
        return false;
    }

    function getPermByPermID($perm_id) {
        empty($this->permissions) ? $this->getPerms() : false;

        foreach ($this->permissions as $perm) {
            if (($perm['perm_id'] == $perm_id)) {
                return $perm;
            }
        }
        return false;
    }

    /*


      function newRole($perm) {
      global $LNG, $db;

      if (empty($perm['level']) || empty($perm['group']) || empty($perm['type']) || empty($perm['name'])) {
      return $msg = $LNG['L_ACL_E_EMPTY_NEWROLE'];
      }

      $insert_ary = array(
      "level" => $perm['level'],
      "perm_group" => $perm['group'],
      "perm_type" => $perm['type'],
      "perm_name" => $perm['name'],
      "perm_description" => $db->escape_strip($perm['description'])
      );

      $db->insert("acl_perms", $insert_ary);
      return $LNG['L_ACL_ROLE_SUBMIT_SUCCESFUL'];
      }

      function deleteRole($perm_id) {
      global $db, $LNG;

      if (!empty($perm_id)) {
      $db->delete("acl_perms", ["perm_id" => "$perm_id"], "LIMIT 1");
      return $LNG['L_ACL_ROLE_DELETE_SUCCESFUL'];
      } else {
      return $LNG['L_ACL_E_ID'];
      }
      }

      function addUserRole($uid, $perm_id) {
      global $db, $LNG;



      $actual_user_perms = $this->getUserRolesByUID($uid);
      $new_perms = "";
      $first = 1;

      if (count($actual_user_perms) > 0) {
      foreach ($actual_user_perms as $actual_perm_id) {
      if ($actual_perm_id != $perm_id) {
      if ($first) {
      $first = 0;
      $new_perms .= $actual_perm_id;
      } else {
      $new_perms .= "," . $actual_perm_id;
      }
      } else {
      return $LNG['L_ACL_USER_ALREADY_ROLE'];
      }
      }
      }
      ($first) ? $new_perms .= $perm_id : $new_perms .= "," . $perm_id;
      $db->update("users", ["perms" => $new_perms], ["uid" => $uid]);
      return $LNG['L_ACL_ADD_SUCCESSFUL'];
      }

      function deleteUserRole($uid, $perm_id) {
      global $db, $LNG;

      $actual_user_perms = $this->getUserRolesByUID($uid);
      $new_perms = "";
      $first = 1;

      if (count($actual_user_perms) > 0) {
      foreach ($actual_user_perms as $actual_perm_id) {
      if ($actual_perm_id != $perm_id) {
      if ($first) {
      $first = 0;
      $new_perms .= $actual_perm_id;
      } else {
      $new_perms .= "," . $actual_perm_id;
      }
      }
      }
      $db->update("users", ["perms" => $new_perms], ["uid" => $uid]);
      return $LNG['L_ACL_DEL_SUCCESSFUL'];
      }

      return $LNG['L_ACL_E_ID'];
      }




     */
    /*




      function get_perms_select($acl_group = null, $selected = null) {
      global $LNG, $db;

      $select = "<select name='{$acl_group}_acl' id='{$acl_group}_acl'>";
      if ($selected == null) {
      $select .= "<option selected value=''>{$LNG['L_ACL_NONE']}</option>";
      } else {
      $select .= "<option value=''>{$LNG['L_ACL_NONE']}</option>";
      }

      $query = $this->get_perms_query($acl_group);
      while ($row = $db->fetch($query)) {
      $full_perm = $row['perm_group'] . "_" . $row['perm_type'];
      if ($full_perm != $selected) {
      $select .= "<option value='$full_perm'>{$LNG[$row['perm_name']]}</option>";
      } else {
      $select .= "<option selected value='$full_perm'>{$LNG[$row['perm_name']]}</option>";
      }
      }
      $select .= "</select>";
      return $select;
      }






      private function get_perms_query($acl_group = null) {
      global $db;

      if (!empty($acl_group)) {
      $query = $db->select_all("acl_perms", array("perm_group" => "$acl_group"));
      } else {
      $query = $db->select_all("acl_perms");
      }

      return $query;
      }


     */
}
