<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class Groups {

    private $debug;
    private $groups;
    private $user_groups;

    public function __construct() {
        $this->SetUserGroups();
    }

    function getUserGroups() {
        return $this->$user_groups;
    }

    function getGroups() {
        (empty($this->groups)) ? $this->setGroups() : false;

        return $this->groups;
    }

    function deleteGroup($group_id) {
        global $db, $LNG, $filter;

        if (!empty($group_id)) {
            $db->delete("groups", ["group_id" => "$group_id"], "LIMIT 1");
            return $LNG['L_GROUP_DELETE_SUCCESFUL'];
        } else {
            return $LNG['L_E_GROUP_ID'];
        }
    }

    function newGroup($group) {
        global $LNG, $db;

        if (empty($group['group_name'])) {
            return $msg = $LNG['L_E_EMPTY_GROUP_NAME'];
        }

        $query = $db->select("groups", "group_id", ["group_name" => $group['group_name']]);
        if ($db->num_rows($query) > 0) {
            return $LNG['L_GROUP_DUPLICATE'];
        } else {
            $insert_ary = [
                "group_name" => $group['group_name'],
                "group_desc" => $db->escape_strip($group['group_desc']),
                "plugin" => $group['plugin'],
            ];

            $db->insert("groups", $insert_ary);
        }
        return $LNG['L_GROUP_SUBMIT_SUCCESFUL'];
    }

    function getUserGroupsByUID($uid) {
        global $db;

        $q = $db->select("users", "groups", ["uid" => "$uid"], "LIMIT 1");

        if ($db->num_rows($q) > 0) {
            $row = $db->fetch($q);
            if (empty($row['groups'])) {
                return false;
            } else {
                $user_groups = explode(",", $row['groups']);
            }
        }

        $db->free($q);
        return $user_groups;
    }

    function getGroupByGroupID($group_id) {
        empty($this->groups) ? $this->getGroups() : false;

        foreach ($this->groups as $group) {
            if (($group['group_id'] == $group_id)) {
                return $group;
            }
        }
        return false;
    }

    function addUserGroup($uid, $group_id) {
        global $db, $LNG;



        $actual_user_groups = $this->getUserGroupsByUID($uid);
        $new_groups = "";
        $first = 1;

        if (count($actual_user_groups) > 0) {
            foreach ($actual_user_groups as $actual_group_id) {
                if ($actual_group_id != $group_id) {
                    if ($first) {
                        $first = 0;
                        $new_groups .= $actual_group_id;
                    } else {
                        $new_groups .= "," . $actual_group_id;
                    }
                } else {
                    return $LNG['L_USER_ALREADY_GROUP'];
                }
            }
        }
        ($first) ? $new_groups .= $group_id : $new_groups .= "," . $group_id;
        $db->update("users", ["groups" => $new_groups], ["uid" => $uid]);
        return $LNG['L_GROUPS_ADD_SUCCESSFUL'];
    }

    function deleteUserGroup($uid, $group_id) {
        global $db, $LNG;

        $actual_user_groups = $this->getUserGroupsByUID($uid);
        $new_groups = "";
        $first = 1;

        if (count($actual_user_groups) > 0) {
            foreach ($actual_user_groups as $actual_group_id) {
                if ($actual_group_id != $group_id) {
                    if ($first) {
                        $first = 0;
                        $new_groups .= $actual_group_id;
                    } else {
                        $new_groups .= "," . $actual_group_id;
                    }
                }
            }
            $db->update("users", ["groups" => $new_groups], ["uid" => $uid]);
            return $LNG['L_DEL_SUCCESSFUL'];
        }

        return $LNG['L_E_ID'];
    }

    private function setGroups() {
        global $db;

        $query = $db->select_all('groups');
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->groups[] = $row;
            }
        } else {
            $this->groups = false;
        }
        $db->free($query);
    }

    private function SetUserGroups() {
        global $sm;

        if (!($user = $sm->getSessionUser())) {
            return ($this->user_groups = false);
        }

        $this->user_groups = $this->getUserGroupsByUID($user['uid']);
        if (!(count($this->user_groups) > 0)) {
            return ($this->user_groups = false);
        }

        return true;
    }

}
