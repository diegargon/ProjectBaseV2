<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsSearch_init() {
    global $cfg, $sm, $tpl, $frontend;

    define('NEWS_SEARCH', true);

    $user = $sm->getSessionUser();

    if (!$cfg['ns_allow_search'] || ( (empty($user) || $user['uid'] <= 0) && (!$cfg['ns_allow_anon']) )) {
        return false;
    }

    $tpl->getCSS_filePath("NewsSearch");
    register_action("header_menu_element", "NS_basicSearchbox", 5);

    $frontend->register_page(['module' => 'NewsSearch', 'page' => 'search', 'type' => 'disk']);
    /* TAGS */
    if ($cfg['ns_tag_support']) {
        register_action("news_new_form_add", "NS_tag_add_form");
        register_action("news_mod_submit_insert", "NS_news_mod_insert");
        register_action("news_show_page", "NS_news_tag_show_page");
        register_action("news_edit_form_add", "NS_tags_edit_form_add");
        register_action("news_fulledit_mod_set", "NS_news_edit_set_tag");
        register_action("news_limitededit_mod_set", "NS_news_edit_set_tag");
    }
}

function NewsSearch_install() {
    global $db;
    require_once "db/NewsSearch.db.php";
    foreach ($newsSearch_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function NewsSearch_preInstall() {
    return true;
}

function NewsSearch_preInstall_info() {
    return true;
}

function NewsSearch_upgrade($version, $from_version) {
    global $db;
    require_once "db/NewsSearch.db.php";
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($newsSearch_database_upgrade_002_to_003 as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function NewsSearch_uninstall() {
    global $db;
    require_once "db/NewsSearch.db.php";
    foreach ($newsSearch_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
