<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_AdminInit() {
    global $acl_auth, $plugins;
    
    !isset($acl_auth) ? $plugins->express_start("SimpleACL") : null;    
    register_action("add_admin_menu", "SimpleACL_AdminMenu", 5);    
}

function SimpleACL_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("SimpleACL");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "SimpleACL_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "SimpleACL_AdminContent", $params);
        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>SimpleACL</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>SimpleACL</a></li>";
    }
}

function SimpleACL_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_ACL_ROLES'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_ACL_USER_ROLES'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SimpleACL_AdminContent($params) {
    global $LNG, $tpl;

    $tpl->getCSS_filePath("SimpleACL");
    $msg = "";
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SimpleACL");
    } else if ($params['opt'] == 2) {
        isset($_POST['btnNewRole']) ? $msg = SimpleACL_NewRole() : false;
        isset($_POST['btnRoleDelete']) ? $msg = SimpleACL_DeleteRole() : false;
        $page_data = $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_ROLES'];
        $page_data .= SimpleACL_ShowRoles($msg);
    } else if ($params['opt'] == 3) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_USER_ROLES'] . "</h1>";
        $page_data .= SimpleACL_UserRoles($msg);
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SimpleACL");
    }

    return $page_data;
}

function SimpleACL_ShowRoles($msg) {
    global $tpl, $acl_auth;

    $roles = $acl_auth->getRoles();
    $counter = 1;
    $count = count($roles);
    $content = "";
    $group = "";

    foreach ($roles as $role) {
        ($counter == $count) ? $role['TPL_CTRL'] = 0 : $role['TPL_CTRL'] = $counter++;

        (!empty($msg) && $counter == 1) ? $role['ACL_MSG'] = $msg : false;
        $role['ACL_MSG'] = $msg;

        if (!empty($group) && $role['role_group'] != $group) {
            $role['ACL_SPLIT'] = 1;
            $group = $role['role_group'];
        } else if (empty($group)) {
            $group = $role['role_group'];
        }
        $content .= $tpl->getTPL_file("SimpleACL", "acl_admin_roles", $role);
    }

    return $content;
}

function SimpleACL_UserRoles($msg) {
    global $tpl, $LNG, $sm, $acl_auth, $filter;

    $page_data = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty(($search_user = $sm->getUserByUsername($filter->post_strict_chars("username"))))) {

            !empty($_POST['btnAddRole']) && !empty($search_user) ? $msg = SimpleACL_AddUserRole($search_user) : false;
            !empty($_POST['btnDeleteRole']) && !empty($search_user) ? $msg = SimpleACL_DeleteUserRole($search_user) : false;
            $page_data = array_merge($page_data, $search_user);
            $user_roles = $acl_auth->getUserRolesByUID($search_user['uid']);

            if ($user_roles !== false) {
                $page_data['option_roles'] = "";
                foreach ($user_roles as $user_role_id) {
                    $role_data = $acl_auth->getRoleByRoleID($user_role_id);
                    $page_data['option_roles'] .= "<option value='{$role_data['role_id']}'>{$role_data['role_group']}_{$role_data['role_type']}</option>";
                }
            } else {
                $msg = $LNG['L_ACL_USER_NOROLES'];
            }
            $roles = $acl_auth->getRoles();

            if (!empty($roles)) {
                $page_data['roles'] = "";
                foreach ($roles as $role) {
                    if (preg_match("/L_/", $role['role_name'])) {
                        $page_data['roles'] .= "<option value='{$role['role_id']}'>{$LNG[$role['role_name']]}</option>";
                    } else {
                        $page_data['roles'] .= "<option value='{$role['role_id']}'>{$role['role_name']}</option>";
                    }
                }
            } else {
                $msg = $LNG['L_ACL_INTERNAL_E_NOROLES'];
            }
        } else {
            $msg = $LNG['L_ACL_USER_NOTFOUND'];
        }
    }
    !empty($msg) ? $page_data['ACL_MSG'] = $msg : false;
    return $tpl->getTPL_file("SimpleACL", "acl_user_roles", $page_data);
}

function SimpleACL_NewRole() {
    global $filter, $acl_auth;

    $role['level'] = $filter->post_int("r_level", 2, 1);
    $role['group'] = $filter->post_AZChar("r_group", 18, 1);
    $role['type'] = $filter->post_strict_chars("r_type", 14, 1);
    $role['name'] = $filter->post_strict_chars("r_name", 32, 1);
    $role['description'] = $filter->post_UTF8_txt("r_description", 255);

    return $acl_auth->newRole($role);
}

function SimpleACL_DeleteRole() {
    global $filter, $acl_auth;
    $role_id = $filter->post_int("role_id");

    return $acl_auth->deleteRole($role_id);
}

function SimpleACL_AddUserRole($user) {
    global $filter, $acl_auth;

    $role = $filter->post_int("add_role_id");

    return $acl_auth->addUserRole($user['uid'], $role);
}

function SimpleACL_DeleteUserRole($user) {
    global $filter, $acl_auth;

    $role_id = $filter->post_int("del_role_id");

    return $acl_auth->deleteUserRole($user['uid'], $role_id);
}
