<?php

/**
 *  SimpleGroups - Main admin file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleGroups
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function SimpleGroups_AdminInit() {
    global $plugins;

    $plugins->expressStart("SimpleGroups") ? register_action("add_admin_menu", "SimpleGroups_AdminMenu", 5) : null;
}

function SimpleGroups_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("SimpleGroups");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "SimpleGroups_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "SimpleGroups_AdminContent", $params);
        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>SimpleGroups</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>SimpleGroups</a></li>";
    }
}

function SimpleGroups_AdminAside($params) {
    global $LNG;

    return "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_GROUPS'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_USER_GROUPS'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SimpleGroups_AdminContent($params) {
    global $LNG, $tpl;

    $tpl->getCssFile("SimpleGroups");
    $msg = "";
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SimpleGroups");
    } else if ($params['opt'] == 2) {
        isset($_POST['btnNewGroup']) ? $msg = SimpleGroups_NewGroup() : null;
        isset($_POST['btnDeleteGroup']) ? $msg = SimpleGroups_DeleteGroup() : null;
        $page_data = $LNG['L_GENERAL'] . ": " . $LNG['L_GROUPS'];
        $page_data .= SimpleGroups_ShowGroups($msg);
    } else if ($params['opt'] == 3) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_USER_GROUPS'] . "</h1>";
        $page_data .= SimpleGroups_UserGroups($msg);
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SimpleGroups");
    }

    return $page_data;
}

function SimpleGroups_ShowGroups($msg) {
    global $LNG, $groups, $tpl;

    $groups = $groups->getGroups();
    if(empty($groups)) {
        return false;
    }
    $counter = 1;    
    $count = count($groups);
    $content = '';

    foreach ($groups as $group) {
        $group['TPL_CTRL'] = $counter;
        ($counter == $count) ? $group['TPL_FOOT'] = 1 : $group['TPL_FOOT'] = 0;
        (!empty($msg) && $counter == 1) ? $group['ACL_MSG'] = $msg : null;
        $group['MSG'] = $msg;

        (preg_match("/L_/", $group['group_name'])) ? $group['group_name'] = $LNG[$group['group_name']] : null;
        (preg_match("/L_/", $group['group_desc'])) ? $group['group_desc'] = $LNG[$group['group_desc']] : null;

        $content .= $tpl->getTplFile("SimpleGroups", "admin_groups", $group);
        $counter++;
    }

    return $content;
}

function SimpleGroups_NewGroup() {
    global $filter, $groups;

    $group['group_name'] = $filter->postAZChar("group_name", 255, 1);
    $group['group_desc'] = $filter->postUtf8Txt("group_desc", 255, 1);
    $group['plugin'] = "USER";

    return $groups->newGroup($group);
}

function SimpleGroups_DeleteGroup() {
    global $filter, $groups;

    return $groups->deleteGroup($filter->postInt("group_id"));
}

function SimpleGroups_UserGroups($msg) {
    global $LNG, $sm, $filter, $groups, $tpl;

    $page_data = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty(($search_user = $sm->getUserByUsername($filter->postUsername("username", 255, 1))))) {

            !empty($_POST['btnAddGroup']) && !empty($search_user) ? $msg = SimpleGroups_AddUserGroup($search_user) : null;
            !empty($_POST['btnDeleteGroup']) && !empty($search_user) ? $msg = SimpleGroups_DeleteUserGroup($search_user) : null;
            $page_data = array_merge($page_data, $search_user);
            $user_groups = $groups->getUserGroupsByUID($search_user['uid']);

            if ($user_groups !== false) {
                $page_data['option_groups'] = "";
                foreach ($user_groups as $user_group_id) {
                    $group_data = $groups->getGroupByGroupID($user_group_id);
                    if ($group_data['group_type'] == "USER") {
                        if (preg_match("/L_/", $group_data['group_name'])) {
                            $page_data['option_groups'] .= "<option value='{$group_data['group_id']}'>{$LNG[$group_data['group_name']]}</option>";
                        } else {
                            $page_data['option_groups'] .= "<option value='{$group_data['group_id']}'>{$group_data['group_name']}</option>";
                        }
                    }
                }
            } else {
                $msg = $LNG['L_NO_GROUPS_FOUND'];
            }
            $groups = $groups->getGroups();

            if (!empty($groups)) {
                $page_data['groups'] = "";
                foreach ($groups as $group) {
                    if (preg_match("/L_/", $group['group_name'])) {
                        $page_data['groups'] .= "<option value='{$group['group_id']}'>{$LNG[$group['group_name']]}</option>";
                    } else {
                        $page_data['groups'] .= "<option value='{$group['group_id']}'>{$group['group_name']}</option>";
                    }
                }
            } else {
                $msg = $LNG['L_E_INTERNAL_NOGROUPS'];
            }
        } else {
            $msg = $LNG['L_USER_NOTFOUND'];
        }
    }
    !empty($msg) ? $page_data['MSG'] = $msg : null;
    return $tpl->getTplFile("SimpleGroups", "user_groups", $page_data);
}

function SimpleGroups_AddUserGroup($user) {
    global $filter, $groups;

    $group_id = $filter->postInt("add_group_id");

    return $groups->addUserGroup($user['uid'], $group_id);
}

function SimpleGroups_DeleteUserGroup($user) {
    global $filter, $groups;

    $group_id = $filter->postInt("del_group_id");

    return $groups->deleteUserGroup($user['uid'], $group_id);
}
