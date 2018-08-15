<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * 
 * Fast Implementation, need some basic ACL now,  work on this later
 */
!defined('IN_WEB') ? exit : true;

class ACL {

    private $roles, $user_roles;

    function __construct() {
        
    }

    function acl_ask($roles_demand, $resource = null) {
        global $sm;

        print_debug("ACL_ASK-> $roles_demand", "ACL_DEBUG");

        $user = $sm->getSessionUser();
        if (!$user) {
            return false;
        } 
        
        if (empty($this->roles) || empty($this->user_roles)) {
            $this->SetRoles();
            $this->SetUserRoles();
        }
        if ($this->roles == false) {
            return false;
        }

        if ($this->user_roles == false) { //No user_roles in DB for that user
            return false;
        }
        //remove all white spaces since in next step when split not want check agains "admin_all " instead of "admin_all"
        $roles_demand = preg_replace('/\s+/', '', $roles_demand);

        return $this->check_demanding_roles($roles_demand);
    }

    function get_roles_select($acl_group = null, $selected = null) {
        global $LNG, $db;

        $select = "<select name='{$acl_group}_acl' id='{$acl_group}_acl'>";
        if ($selected == null) {
            $select .= "<option selected value=''>{$LNG['L_ACL_NONE']}</option>";
        } else {
            $select .= "<option value=''>{$LNG['L_ACL_NONE']}</option>";
        }

        $query = $this->get_roles_query($acl_group);
        while ($row = $db->fetch($query)) {
            $full_role = $row['role_group'] . "_" . $row['role_type'];
            if ($full_role != $selected) {
                $select .= "<option value='$full_role'>{$LNG[$row['role_name']]}</option>";
            } else {
                $select .= "<option selected value='$full_role'>{$LNG[$row['role_name']]}</option>";
            }
        }
        $select .= "</select>";
        return $select;
    }

    function getUserRoles($uid) {
        global $db;

        $query = $db->select_all("acl_users", array("uid" => "$uid"));
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $user_roles[] = $row;
            }
        } else {
            $user_roles = false;
        }
        $db->free($query);
        return $user_roles;
    }

    function getRoleByID($role_id) {
        empty($this->roles) ? $this->getRoles() : false;

        foreach ($this->roles as $role) {
            if (($role['role_id'] == $role_id)) {
                return $role;
            }
        }
        return false;
    }

    function retrieveRoles() {
        return $this->roles;
    }

    private function checkUserPerms($role_group, $role_type, $resource = "ALL") {
        if (!$asked_role = $this->getRoleDataByName($role_group, $role_type)) {
            return false;
        }

        foreach ($this->user_roles as $user_role) {
            if (!$user_role_data = $this->getRoleByID($user_role['role_id'])) {
                return false;
            }
            if (($user_role_data['role_id'] == $asked_role['role_id']) &&
                    ($user_role_data['resource'] == $resource) //Used later                     
            ) {
                print_debug("Exact role found", "ACL_DEBUG");
                return true; //its the exact role
            }
            //Look if role its upper level
            if (( $asked_role['role_group'] == $user_role_data['role_group'] ) &&
                    ( $asked_role['level'] > $user_role_data['level'] ) &&
                    ( $user_role_data['resource'] == $resource) //Used later 
            ) {
                print_debug("Role up found", "ACL_DEBUG");
                return true;
            }
        }
        return false;
    }

    private function getRoleDataByName($role_group, $role_type) {
        foreach ($this->roles as $rol) {
            if (($rol['role_group'] == $role_group) && ($rol['role_type'] == $role_type)) {
                return $rol;
            }
        }
        return false;
    }

    private function SetRoles() {
        global $db;

        $query = $db->select_all("acl_roles");
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->roles[] = $row;
            }
        } else {
            $this->roles = false;
        }
        $db->free($query);
    }

    private function SetUserRoles() {
        global $db, $sm;

        if (!($user = $sm->getSessionUser())) {
            return false;
        }

        $query = $db->select_all("acl_users", array("uid" => "{$user['uid']}"));
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->user_roles[] = $row;
            }
        } else {
            $this->user_roles = false;
        }
        $db->free($query);
    }

    private function get_roles_query($acl_group = null) {
        global $db;

        if (!empty($acl_group)) {
            $query = $db->select_all("acl_roles", array("role_group" => "$acl_group"));
        } else {
            $query = $db->select_all("acl_roles");
        }

        return $query;
    }

    private function check_demanding_roles($roles_demand) {

        if (preg_match("/\|\|/", $roles_demand)) {
            $or_split = preg_split("/\|\|/", $roles_demand);
        } else {
            $or_split[] = $roles_demand;
        }

        foreach ($or_split as $or_split_role) {
            $auth = false;
            if (!preg_match("/\&\&/", $or_split_role)) {
                $auth = $this->demanding_role_process($or_split_role);
                print_debug("ACL 1 \"$or_split_role\" result->$auth", "ACL_DEBUG");
                if ($auth) {
                    return true;
                } //first OR true, no need check the others
            } else { //&& check all except if any its false
                $and_split = preg_split("/\&\&/", $or_split_role);

                foreach ($and_split as $and_split_role) {
                    $auth = $this->demanding_role_process($and_split_role);
                    print_debug("ACL 3 -> \"$and_split_role\" -> $auth  ", "ACL_DEBUG");
                    if ($auth == false) {
                        print_debug("ACL 4 -> \"$and_split_role\" -> Break", "ACL_DEBUG");
                        break;
                    } //if any && role its false, not check the next && roles
                }
                if ($auth == true) {
                    print_debug("ACL result->true", "ACL_DEBUG");
                    return true;
                } //if auth = true at this point, this group of && roles are all true
            }
        }
        print_debug("ACL F result->false", "ACL_DEBUG");
        return false;
    }

    private function demanding_role_process($role) {
        print_debug("ACL Checking -> $role", "ACL_DEBUG");
        list($role_group, $role_type) = preg_split("/_/", $role);

        return !$this->checkUserPerms($role_group, $role_type) ? false : true;
    }

}
