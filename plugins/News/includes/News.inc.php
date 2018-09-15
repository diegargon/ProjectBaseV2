<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function submit_news_menu() {
    global $LNG, $cfg;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($cfg['FRIENDLY_URL']) {
        $data .= "{$cfg['WEB_LANG']}/submit_news";
    } else {
        $data .= "{$cfg['CON_FILE']}?module=News&page=submit_news&lang={$cfg['WEB_LANG']}";
    }
    $data .= "'>" . $LNG['L_SUBMIT_NEWS'] . "</a>";
    $data .= "</li>";

    return $data;
}

function get_news_perms($resource, $news_data = null) {
    global $sm, $cfg;

    $user = $sm->getSessionUser();

    //DEFAULTS
    $perm['news_feature'] = false;
    $perm['news_frontpage'] = false;
    $perm['news_edit'] = false;
    $perm['news_translate'] = false;
    $perm['news_delete'] = false;
    $perm['news_moderation'] = false;
    $perm['news_create_new_page'] = false;
    $perm['news_can_change_author'] = false;
    $perm['news_add_related'] = $cfg['display_news_related'] ? true : false;
    $perm['news_add_source'] = $cfg['display_news_source'] ? true : false;

    if (defined('ACL')) {
        /* ACL */
        news_acl_perms($perm);
    } else if (!defined('ACL') && !empty($user) && $user['isAdmin'] == 1) {
        /* NOACL ADMIN */
        news_noacl_admin_perms($perm);
    } else {
        if (isset($user) && $user['uid'] > 0) {
            /* NOACL_USER */
            news_noacl_user($perm);
        } else { //ANON
            /* NOACL_ANON */
            news_no_acl_anon($perm);
        }
    }
    /* BY RESOURCE */
    if ($resource == "new_page" || $resource == "news_new_lang") { //not related/resource in secondary pages
        $perm['news_add_related'] = false;
        $perm['news_add_source'] = false;
    }
    return $perm;
}

function news_acl_perms(&$perm) {
    global $acl_auth;
    $perm['news_feature'] = $acl_auth->acl_ask("news_feature");
    $perm['news_frontpage'] = $acl_auth->acl_ask("news_frontpage");
    $perm['news_edit'] = $acl_auth->acl_ask("news_edit_all");
    if ($cfg['news_translate']) {
        if ($cfg['news_anon_translate']) {
            $perm['news_translate'] = true;
        } else if ($acl_auth->acl_ask("news_translate_all")) {
            $perm['news_translate'] = true;
        } else if (!empty($news_data) && !empty($user) && $news_data['author_id'] == $user['uid'] && $acl_auth->acl_ask("news_translate_own")) {
            $perm['news_translate'] = true;
        }
    }

    $perm['news_delete'] = $acl_auth->acl_ask("news_delete_all");
    $perm['news_moderation'] = $acl_auth->acl_ask("news_moderation");
    $perm['news_create_new_page'] = $acl_auth->acl_ask("news_create_new_page");
    $perm['news_can_change_author'] = $acl_auth->acl_ask("news_can_change_author");

    if ($cfg['display_news_related'] && $acl_auth->acl_ask("news_add_related")) {
        $perm['news_add_related'] = true;
    }
    if ($cfg['display_news_source'] && $acl_auth->acl_ask("news_add_source")) {
        $perm['news_add_source'] = true;
    }

    //$perm['news_'] = $acl_auth->acl_ask("news_");    

    return $perms;
}

function news_noacl_admin_perms(&$perm) {
    $perm['news_feature'] = true;
    $perm['news_frontpage'] = true;
    $perm['news_edit'] = true;
    $perm['news_translate'] = true;
    $perm['news_delete'] = true;
    $perm['news_moderation'] = true;
    $perm['news_create_new_page'] = true;
    $perm['news_can_change_author'] = true;
}

function news_noacl_user(&$perm) {
    global $cfg;

    if ($cfg['news_translate']) {
        if (($cfg['news_users_translate'] || $cfg['news_anon_translate'])) {
            $perm['news_translate'] = true;
        } else if ($cfg['news_users_own_translate'] && isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) {
            $perm['news_translate'] = true;
        }
    }
}

function news_noacl_anon(&$perm) {
    global $cfg;

    if ($cfg['news_translate'] && $cfg['news_anon_translate']) {
        $perm['news_translate'] = true;
    }
}
