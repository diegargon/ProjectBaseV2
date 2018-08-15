<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_AdminInit() {
    register_action("add_admin_menu", "SimpleACL_AdminMenu", 5);
}

function SimpleACL_AdminMenu($params) {
    //TODO A way to assign uniq numbers
    $tab_num = 102;
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SimpleACL_AdminContent");
        return "<li class='tab_active'><a href='admin&admtab=$tab_num'>SimpleACL</a></li>";
    } else {
        return "<li><a href='admin&admtab=$tab_num'>SimpleACL</a></li>";
    }
}

function SimpleACL_AdminContent($params) {
    global $tpl, $LNG;

    $msg = "";

    $tpl->getCSS_filePath("SimpleACL");

    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_ACL_ROLES'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_ACL_USER_ROLES'] . "</a></li>\n";

    $opt = S_GET_INT("opt");
    if ($opt == 1 || $opt == false) {
        $page_data['ADM_CONTENT_H2'] = $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'];
        $page_data['ADM_CONTENT'] = Admin_GetPluginState("SimpleACL");
    } else if ($opt == 2) {
        isset($_POST['btnNewRole']) ? $msg = SimpleACL_NewRole() : false;
        isset($_POST['btnRoleDelete']) ? $msg = SimpleACL_DeleteRole() : false;
        $page_data['ADM_CONTENT_H2'] = $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_ROLES'];
        $page_data['ADM_CONTENT'] = SimpleACL_ShowRoles($msg);
    } else if ($opt == 3) {
        $page_data['ADM_CONTENT_H2'] = $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_USER_ROLES'];
        $page_data['ADM_CONTENT'] = SimpleACL_UserRoles($msg);
    }

    return $tpl->getTPL_file("Admin", "admin_std_content", $page_data);
}

function SimpleACL_ShowRoles($msg) {
    global $db, $tpl;

    !empty($msg) ? $table['ACL_MSG'] = $msg : false;

    $all_roles = $db->select_all("acl_roles", null, "ORDER BY role_group, level");

    $table['ADM_TABLE_ROW'] = "";
    $group = "";
    foreach ($all_roles as $role) {
        if (!empty($group) && $role['role_group'] != $group) {
            $role['ACL_SPLIT'] = 1;
            $group = $role['role_group'];
        } else if (empty($group)) {
            $group = $role['role_group'];
        }
        $table['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SimpleACL", "acl_admin_roles_row", $role);
    }
    return $tpl->getTPL_file("SimpleACL", "acl_admin_roles", $table);
}

function SimpleACL_NewRole() {
    global $LNG, $db;
    $r_level = S_POST_INT("r_level", 2, 1);
    $r_group = S_POST_CHAR_AZ("r_group", 18, 1);
    $r_type = S_POST_CHAR_AZ("r_type", 14, 1);
    $r_name = S_POST_CHAR_AZ("r_name", 32, 1);
    $r_description = S_POST_TEXT_UTF8("r_description", 255);

    if (empty($r_level) || empty($r_group) || empty($r_type) || empty($r_name)) {
        return $msg = $LNG['L_ACL_E_EMPTY_NEWROLE'];
    }

    $insert_ary = array(
        "level" => "$r_level",
        "role_group" => "$r_group",
        "role_type" => "$r_type",
        "role_name" => "$r_name",
        "role_description" => $db->escape_strip($r_description)
    );

    $db->insert("acl_roles", $insert_ary);
    return $msg = $LNG['L_ACL_ROLE_SUBMIT_SUCCESFUL'];
}

function SimpleACL_DeleteRole() {
    global $db;
    $role_id = S_POST_INT("role_id");
    !empty($role_id) ? $db->delete("acl_roles", array("role_id" => "$role_id", "LIMIT 1")) : false;
}

function SimpleACL_UserRoles($msg) {
    global $tpl, $LNG, $sm, $acl_auth;

    $content = [];

    if (!empty($_POST['btnSearchUser']) || !empty($_POST['btnAddRole']) || !empty($_POST['btnDeleteRole'])) {
        $search_user = $sm->getUserByUsername(S_POST_STRICT_CHARS("username"));
        !empty($search_user) ? $content = array_merge($content, $search_user) : false;
    }
    !empty($_POST['btnAddRole']) && !empty($search_user) ? $msg = SimpleACL_AddRole($search_user) : false;
    !empty($_POST['btnDeleteRole']) && !empty($search_user) ? $msg = SimpleACL_DelRole($search_user) : false;

    $content['option_roles'] = "";

    if (!empty($search_user)) {
        $user_roles = $acl_auth->getUserRoles($search_user['uid']);
        if (!empty($user_roles)) {
            foreach ($user_roles as $user_role) {
                $role_data = $acl_auth->getRoleByID($user_role['role_id']);
                $content['option_roles'] .= "<option value='{$role_data['role_id']}'>{$role_data['role_group']}_{$role_data['role_type']}</option>";
            }
        } else {
            $msg = $LNG['L_ACL_USER_NOROLES'];
        }
        $roles = $acl_auth->retrieveRoles();
        if (!empty($roles)) {
            $content['roles'] = "";
            foreach ($roles as $role) {
                if (preg_match("/L_/", $role['role_name'])) {
                    $content['roles'] .= "<option value='{$role['role_id']}'>{$LNG[$role['role_name']]}</option>";
                } else {
                    $content['roles'] .= "<option value='{$role['role_id']}'>{$role['role_name']}</option>";
                }
            }
        } else {
            $msg = $LNG['L_ACL_INTERNAL_E_NOROLES'];
        }
    } else {
        $msg = $LNG['L_ACL_USER_NOTFOUND'];
    }
    !empty($msg) ? $content['ACL_MSG'] = $msg : false;
    return $tpl->getTPL_file("SimpleACL", "acl_user_roles", $content);
}

function SimpleACL_AddRole($user) {
    global $db, $LNG;

    $role = S_POST_INT("add_role_id");

    if (!empty($role)) {
        $role_ary = array(
            "uid" => $user['uid'],
            "role_id" => $role
        );
        $query = $db->select_all("acl_users", $role_ary, "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            return $LNG['L_ACL_USER_ALREADY_ROLE'];
        } else {
            $db->insert("acl_users", $role_ary);
            return $LNG['L_ACL_ADD_SUCCESSFUL'];
        }
    }
    return $LNG['L_ACL_E_ID'];
}

function SimpleACL_DelRole($user) {
    global $db, $LNG;

    $role = S_POST_INT("del_role_id");
    if (!empty($role)) {
        $db->delete("acl_users", array("uid" => "{$user['uid']}", "role_id" => "$role"));
        return $LNG['L_ACL_DEL_SUCCESSFUL'];
    }

    return $LNG['L_ACL_E_ID'];
}
