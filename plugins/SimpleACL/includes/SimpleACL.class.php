<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * Fast Implementation, need some basic ACL now,  work on this later
 */
!defined('IN_WEB') ? exit : true;

class ACL {

    public $debug;
    private $roles;
    private $user_roles;

    function __construct() {
        global $cfg, $debug;
        (defined(DEBUG) && $cfg['simpleacl_debug']) ? $this->debug = & $debug : $this->debug = false;
    }

    function getRoles() {
        (empty($this->roles)) ? $this->setRoles() : false;

        return $this->roles;
    }

    private function setRoles() {
        global $db;

        $query = $db->select_all("acl_roles", null, "ORDER BY role_group, level");
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->roles[] = $row;
            }
        } else {
            $this->roles = false;
        }
        $db->free($query);
    }

    function getUserRolesByUID($uid) {
        global $db, $sm;

        $q = $db->select("users", "roles", ["uid" => "$uid"], "LIMIT 1");

        if ($db->num_rows($q) > 0) {
            $row = $db->fetch($q);
            if (empty($row['roles'])) {
                return false;
            } else {
                $user_roles = explode(",", $row['roles']);
            }
        }

        $db->free($q);
        return $user_roles;
    }

    function getRoleByRoleID($role_id) {
        empty($this->roles) ? $this->getRoles() : false;

        foreach ($this->roles as $role) {
            if (($role['role_id'] == $role_id)) {
                return $role;
            }
        }
        return false;
    }

    function newRole($role) {
        global $LNG, $db;

        if (empty($role['level']) || empty($role['group']) || empty($role['type']) || empty($role['name'])) {
            return $msg = $LNG['L_ACL_E_EMPTY_NEWROLE'];
        }

        $insert_ary = array(
            "level" => $role['level'],
            "role_group" => $role['group'],
            "role_type" => $role['type'],
            "role_name" => $role['name'],
            "role_description" => $db->escape_strip($role['description'])
        );

        $db->insert("acl_roles", $insert_ary);
        return $LNG['L_ACL_ROLE_SUBMIT_SUCCESFUL'];
    }

    function deleteRole($role_id) {
        global $db, $LNG;

        if (!empty($role_id)) {
            $db->delete("acl_roles", ["role_id" => "$role_id"], "LIMIT 1");
            return $LNG['L_ACL_ROLE_DELETE_SUCCESFUL'];
        } else {
            return $LNG['L_ACL_E_ID'];
        }
    }

    function addUserRole($uid, $role_id) {
        global $db, $LNG;



        $actual_user_roles = $this->getUserRolesByUID($uid);
        $new_roles = "";
        $first = 1;

        if (count($actual_user_roles) > 0) {
            foreach ($actual_user_roles as $actual_role_id) {
                if ($actual_role_id != $role_id) {
                    if ($first) {
                        $first = 0;
                        echo "ENTRA 1";
                        $new_roles .= $actual_role_id;
                    } else {
                        echo "ENTRA 2";
                        $new_roles .= "," . $actual_role_id;
                    }
                } else {
                    return $LNG['L_ACL_USER_ALREADY_ROLE'];
                }
            }
        }
        ($first) ? $new_roles .= $role_id : $new_roles .= "," . $role_id;
        $db->update("users", ["roles" => $new_roles], ["uid" => $uid]);
        return $LNG['L_ACL_ADD_SUCCESSFUL'];
    }

    function deleteUserRole($uid, $role_id) {
        global $db, $LNG;

        $actual_user_roles = $this->getUserRolesByUID($uid);
        $new_roles = "";
        $first = 1;

        if (count($actual_user_roles) > 0) {
            foreach ($actual_user_roles as $actual_role_id) {
                if ($actual_role_id != $role_id) {
                    if ($first) {
                        $first = 0;
                        $new_roles .= $actual_role_id;
                    } else {
                        $new_roles .= "," . $actual_role_id;
                    }
                }
            }
            $db->update("users", ["roles" => $new_roles], ["uid" => $uid]);
            return $LNG['L_ACL_DEL_SUCCESSFUL'];
        }

        return $LNG['L_ACL_E_ID'];
    }

    /*

      function acl_ask($roles_demand, $resource = null) {
      global $sm;

      $this->debug ? $this->debug->log("ACL_ASK-> $roles_demand", "SimpleACL", "DEBUG") : false;

      $this->debug ? $this->debug->log("ACL_ASK-> $roles_demand", "SimpleACL", "DEBUG") : false;
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
      $this->debug ? $this->debug->log("Exact role found", "SimpleACL", "DEBUG") : false;
      return true; //its the exact role
      }
      //Look if role its upper level
      if (( $asked_role['role_group'] == $user_role_data['role_group'] ) &&
      ( $asked_role['level'] > $user_role_data['level'] ) &&
      ( $user_role_data['resource'] == $resource) //Used later
      ) {
      $this->debug ? $this->debug->log("Role up found", "SimpleACL", "DEBUG") : false;
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
      $this->debug ? $this->debug->log("ACL 1 \"$or_split_role\" result->$auth", "SimpleACL", "DEBUG") : false;
      if ($auth) {
      return true;
      } //first OR true, no need check the others
      } else { //&& check all except if any its false
      $and_split = preg_split("/\&\&/", $or_split_role);

      foreach ($and_split as $and_split_role) {
      $auth = $this->demanding_role_process($and_split_role);
      $this->debug ? $this->debug->log("ACL 3 -> \"$and_split_role\" -> $auth  ", "SimpleACL", "DEBUG") : false;
      if ($auth == false) {
      $this->debug ? $this->debug->log("ACL 4 -> \"$and_split_role\" -> Break", "SimpleACL", "DEBUG") : false;
      break;
      } //if any && role its false, not check the next && roles
      }
      if ($auth == true) {
      $this->debug ? $this->debug->log("ACL result->true", "SimpleACL", "DEBUG") : false;
      return true;
      } //if auth = true at this point, this group of && roles are all true
      }
      }
      $this->debug ? $this->debug->log("ACL F result->false", "SimpleACL", "DEBUG") : false;
      return false;
      }

      private function demanding_role_process($role) {
      $this->debug ? $this->debug->log("ACL Checking -> $role", "SimpleACL", "DEBUG") : false;
      list($role_group, $role_type) = preg_split("/_/", $role);

      return !$this->checkUserPerms($role_group, $role_type) ? false : true;
      }
     */
}
