<?php

/**
 *  News - News Perm file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

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
    $perm['news_submit_new'] = $cfg['news_allow_submit_anon'] ? true : false;
    $perm['news_view'] = $cfg['news_view_anon'] ? true : false;

    if (defined('ACL')) {
        /* ACL */
        news_acl_perms($perm, $news_data);
    } else if (!defined('ACL') && !empty($user) && $user['isAdmin'] == 1) {
        /* NOACL ADMIN */
        news_noacl_admin_perms($perm, $news_data);
    } else {
        if (isset($user) && $user['uid'] > 0) {
            /* NOACL_USER */
            news_noacl_user($perm, $news_data);
        } else { //ANON
            /* NOACL_ANON */
            news_noacl_anon($perm, $news_data);
        }
    }
    /* BY RESOURCE */
    if ($resource == 'new_page' || $resource == 'news_new_lang') { //not related/resource in secondary pages
        $perm['news_add_related'] = false;
        $perm['news_add_source'] = false;
    }
    return $perm;
}

function news_acl_perms(&$perm, $news_data) {
    global $acl_auth, $cfg, $sm;

    $user = $sm->getSessionUser();
    //ACL must return true to all if isFounder, not if only isAdmin.

    $perm['news_feature'] = $acl_auth->acl_ask('news_feature');
    $perm['news_frontpage'] = $acl_auth->acl_ask('news_frontpage');
    /* ACL EDIT_NEWS */
    if (!($perm['news_edit'] = $acl_auth->acl_ask('news_edit_all'))) {
        if ($cfg['news_allow_users_edit_own_news'] && isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) {
            $perm['news_edit'] = $acl_auth->acl_ask('news_edit_own_news');
        }
    }
    /* SUBMIT NEW */
    !$cfg['news_allow_submit_anon'] ? $perm['news_submit_new'] = $acl_auth->acl_ask('news_submit_new') : $perm['news_submit_new'] = true;

    /* NEWS_TRANSLATE */
    if ($cfg['news_translate']) {
        if ($cfg['news_anon_translate']) {
            $perm['news_translate'] = true;
        } else if ($acl_auth->acl_ask('news_translate_all')) {
            $perm['news_translate'] = true;
        } else if (!empty($news_data) && !empty($user) && $news_data['author_id'] == $user['uid'] && $acl_auth->acl_ask('news_translate_own')) {
            $perm['news_translate'] = true;
        }
    }

    /* NEWS_VIEW */
    $perm['news_view'] = $acl_auth->acl_ask('news_view');

    /* DELETE NEWS */
    if (!($perm['news_delete'] = $acl_auth->acl_ask('news_delete_all'))) {
        if ($cfg['news_allow_users_delete_own_news'] && isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) {
            $perm['news_delete'] = $acl_auth->acl_ask('news_delete_own_news');
        }
    }
    /* ADMIN THINGS */
    $perm['news_moderation'] = $acl_auth->acl_ask('news_moderation');
    $perm['news_can_change_author'] = $acl_auth->acl_ask('news_can_change_author');

    /* RELATED / SOURCE */
    if ($cfg['display_news_related'] && $acl_auth->acl_ask('news_add_related')) {
        $perm['news_add_related'] = true;
    }
    if ($cfg['display_news_source'] && $acl_auth->acl_ask('news_add_source')) {
        $perm['news_add_source'] = true;
    }
    //$perm['news_'] = $acl_auth->acl_ask('news_');    

    return $perm;
}

function news_noacl_admin_perms(&$perm, $news_data) {
    global $cfg;

    $perm['news_feature'] = true;
    $perm['news_frontpage'] = true;
    $perm['news_edit'] = true;
    $perm['news_translate'] = true;
    $perm['news_delete'] = true;
    $perm['news_moderation'] = true;
    $perm['news_create_new_page'] = ($cfg['allow_multiple_pages']) ? true : false;
    $perm['news_can_change_author'] = true;
    $perm['news_submit_new'] = true;
    $perm['news_view'] = true;
}

function news_noacl_user(&$perm, $news_data) {
    global $cfg, $sm;

    $user = $sm->getSessionUser();

    /* TRANSLATE */
    if ($cfg['news_translate']) {
        if (($cfg['news_users_translate'] && $cfg['news_anon_translate'])) {
            $perm['news_translate'] = true;
        } else if ($cfg['news_users_own_translate'] && isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) {
            $perm['news_translate'] = true;
        }
    }
    /* EDIT_NEWS */
    if ($cfg['news_allow_users_edit_own_news'] && (
            (isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) ||
            (isset($news_data['translator_id']) && $news_data['translator_id'] == $user['uid'])
            )) {
        $perm['news_edit'] = true;
    }
    !$cfg['news_allow_submit_anon'] ? $perm['news_submit_new'] = $cfg['news_allow_submit_users'] : $perm['news_submit_new'] = true;
    /* VIEW_NEWS */
    if (!$perm['news_view']) {
        $perm['news_view'] = $cfg['news_view_user'] ? true : false;
    }

    if ($cfg['allow_multiple_pages']) {
        if (isset($news_data['author_id']) && $news_data['author_id'] == $user['uid']) {
            $perm['news_create_new_page'] = true;
        } else {
            $perm['news_create_new_page'] = false;
        }
    }
}

function news_noacl_anon(&$perm, $news_data) {
    global $cfg;

    /* SUBMIT */
    if ($perm['news_submit_new'] && $cfg['news_allow_submit_anon']) {
        $perm['news_submit_new'] = true;
    } else {
        $perm['news_submit_new'] = false;
    }

    $perm['news_create_new_page'] = false;

    /* TRANSLATE */
    if ($cfg['news_translate'] && $cfg['news_anon_translate']) {
        $perm['news_translate'] = true;
    } else {
        $perm['news_translate'] = false;
    }
}
