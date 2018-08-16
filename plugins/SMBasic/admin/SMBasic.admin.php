<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_AdminInit() {
    register_action("add_admin_menu", "SMBasic_AdminMenu", "5");
}

function SMBasic_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("SMBasic");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "SMBasic_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "SMBasic_admin_content", $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>SMBasic</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>SMBasic</a></li>";
    }
}

function SMBasic_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_SM_SEARCH_USER'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_SM_USERS_LIST'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SMBasic_admin_content($params) {
    global $LNG;
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SMBasic");
    } else if ($params['opt'] == 2) {
        $page_data = "<h2>" . $LNG['L_SM_SEARCH_USER'] . "</h2>";
        $page_data .= $LNG['L_SM_USERS_DESC'] . SMBasic_UserSearch();
    } else if ($params['opt'] == 3) {
        $page_data = $LNG['L_SM_USERS_LIST'];
        $page_data .= $LNG['L_SM_USERS_LIST_DESC'] . SMBasic_UserList();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SMBasic");
    }
    return $page_data;
}

function SMBasic_UserSearch() {
    global $cfg, $LNG, $tpl, $sm, $filter;

    if (isset($_POST['btnDeleteSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        SMBasic_DeleteUser($member_id);
    }
    if (isset($_POST['btnActivateSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        SMBasic_ActivateUser($member_id);
    }
    if (isset($_POST['btnDisableSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        $disable_state = $filter->post_int("member_disable", 1, 1);
        SMBasic_DisableUser($member_id, $disable_state);
    }

    $content = $tpl->getTPL_file("SMBasic", "sm_adm_usersearch_form");

    isset($_POST['posted_glob']) ? $glob = 1 : $glob = 0;
    isset($_POST['posted_email']) ? $email = 1 : $email = 0;
    $s_string = $filter->post_strict_chars("search_user", 32, 1);

    if (!empty($_POST['btnSearchUser']) && !empty($s_string)) {
        if (($users_ary = $sm->searchUser($s_string, $email, $glob))) {

            $table['ADM_TABLE_ROW'] = "";
            foreach ($users_ary as $user_match) {
                if ($cfg['FRIENDLY_URL']) {
                    $user_match['profile_url'] = "/{$cfg['WEB_LANG']}/profile?viewprofile={$user_match['uid']}";
                } else {
                    $user_match['profile_url'] = "/{$cfg['CON_FILE']}?module=SMBasic&page=profile?lang={$cfg['WEB_LANG']}&viewprofile={$user_match['uid']}";
                }
                $table['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user_match);
            }
            $content .= $tpl->getTPL_file("SMBasic", "memberlist", $table);
        }
    }
    return $content;
}

function SMBasic_UserList() {
    global $cfg, $LNG, $tpl, $sm;

    if (isset($_POST['btnDeleteSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        SMBasic_DeleteUser($member_id);
    }
    if (isset($_POST['btnActivateSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        SMBasic_ActivateUser($member_id);
    }
    if (isset($_POST['btnDisableSubmit']) && ( ($member_id = $filter->post_int("member_uid") )) > 0) {
        $disable_state = $filter->post_int("member_disable", 1, 1);
        SMBasic_DisableUser($member_id, $disable_state);
    }

    $users_list = $sm->getAllUsersArray();

    $active['ADM_TABLE_ROW'] = $inactive['ADM_TABLE_ROW'] = $disable['ADM_TABLE_ROW'] = "";

    foreach ($users_list as $user) {
        if ($cfg['FRIENDLY_URL']) {
            $user['profile_url'] = "/{$cfg['WEB_LANG']}/profile?viewprofile={$user['uid']}";
        } else {
            $user['profile_url'] = "/{$cfg['CON_FILE']}?module=SMBasic&page=profile?lang={$cfg['WEB_LANG']}&viewprofile={$user['uid']}";
        }
        if ($user['active'] == 0 && !$user['disable']) {

            $active['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        } else if ($user['active'] > 0 && !$user['disable']) {
            $inactive['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        } else if ($user['disable']) {
            $disable['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        }
    }

    $active['ADM_TABLE_TITLE'] = $LNG['L_SM_USERS_ACTIVE'];
    $inactive['ADM_TABLE_TITLE'] = $LNG['L_SM_USERS_INACTIVE'];
    $disable['ADM_TABLE_TITLE'] = $LNG['L_SM_USERS_DISABLE'];

    $content = $tpl->getTPL_file("SMBasic", "memberlist", $active);
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", $inactive);
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", $disable);

    return $content;
}

function SMBasic_DeleteUser($uid) {
    global $db;
    $db->delete("users", array("uid" => $uid), "LIMIT 1");
}

function SMBasic_DisableUser($uid, $state) {
    global $db;
    empty($state) ? $new_state = 1 : $new_state = 0;
    $db->update("users", array("disable" => $new_state), array("uid" => $uid), "LIMIT 1");
}
