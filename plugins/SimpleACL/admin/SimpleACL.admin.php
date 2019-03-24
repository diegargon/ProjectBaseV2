<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_AdminInit() {
    global $plugins;

    $plugins->express_start("SimpleACL") ? register_action("add_admin_menu", "SimpleACL_AdminMenu", 5) : null;
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

    return "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_ACL_PERM_GROUPS'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SimpleACL_AdminContent($params) {
    global $LNG, $tpl;

    $tpl->getCssFile("SimpleACL");
    $msg = "";
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SimpleACL");
    } else if ($params['opt'] == 2) {
        isset($_POST['btnDelPerm']) ? $msg = SimpleACL_DelPerm() : false;
        isset($_POST['btnNewPerm']) ? $msg = SimpleACL_AddPerm() : false;
        $page_data = $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_PERM_GROUPS'];
        $page_data .= SimpleACL_ShowPermGroups($msg);
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SimpleACL");
    }

    return $page_data;
}

function SimpleACL_ShowPermGroups($msg) {
    global $tpl, $acl_auth, $groups, $filter, $LNG;

    $group_selected = $filter->post_int("group_selected");


    $db_groups = $groups->getGroups();
    empty($group_selected) ? $group_selected = $db_groups[0]['group_id'] : false;

    $content = "";
    $select_groups = "";
    $select_group_perms = "";
    $select_perms = "";

    foreach ($db_groups as $db_group) {
        (preg_match("/L_/", $db_group['group_name'])) ? $db_group['group_name'] = $LNG[$db_group['group_name']] : false;
        (preg_match("/L_/", $db_group['group_desc'])) ? $db_group['group_desc'] = $LNG[$db_group['group_desc']] : false;
        ($db_group['group_id'] == $group_selected) ? $selected = "selected" : $selected = "";
        $select_groups .= "<option $selected value='{$db_group['group_id']}'>{$db_group['group_name']} - {$db_group['group_desc']}</option>";
    }

    $group_perms = $acl_auth->getGroupPerms($group_selected);
    if (!empty($group_perms)) {
        foreach ($group_perms as $group_perm) {
            (preg_match("/L_/", $group_perm['perm_desc'])) ? $group_perm['perm_desc'] = $LNG[$group_perm['perm_desc']] : false;
            $select_group_perms .= "<option value='{$group_perm['perm_id']}'>{$group_perm['perm_desc']} - ({$group_perm['perm_name']})</option>";
        }
    }

    foreach ($acl_auth->getPerms() as $perm) {
        $coincidence = 0;
        if (!empty($group_perms)) {
            foreach ($group_perms as $group_perm) {
                if ($perm['perm_id'] == $group_perm['perm_id']) {
                    $coincidence = 1;
                }
            }
        }
        if (!$coincidence) {
            (preg_match("/L_/", $perm['perm_desc'])) ? $perm['perm_desc'] = $LNG[$perm['perm_desc']] : false;
            $select_perms .= "<option value='{$perm['perm_id']}'>{$perm['perm_desc']} - {$perm['perm_name']}</option>";
        }
    }
    $page_data['group_selection'] = $group_selected;
    $page_data['select_groups'] = $select_groups;
    $page_data['select_perms'] = $select_perms;
    $page_data['select_group_perms'] = $select_group_perms;
    $content .= $tpl->getTplFile("SimpleACL", "acl_perm_groups", $page_data);

    return $content;
}

function SimpleACL_DelPerm() {
    global $filter, $acl_auth;
    $perm_id = $filter->post_int("perm_id");
    $group_id = $filter->post_int("group_id");
    return $acl_auth->deleteGroupPerm($group_id, $perm_id);
}

function SimpleACL_AddPerm() {
    global $filter, $acl_auth;

    $perm_id = $filter->post_int("perm_id");
    $group_id = $filter->post_int("group_id");

    return $acl_auth->addGroupPerm($group_id, $perm_id);
}
