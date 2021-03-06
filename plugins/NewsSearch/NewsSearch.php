<?php

/**
 *  NewsSearch main entry file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function NewsSearch_init() {
    global $cfg, $sm, $tpl, $frontend;

    if ($cfg['ns_disable_by_stress'] && is_server_stressed()) {
        return false;
    }
    define('NEWS_SEARCH', true);

    $user = $sm->getSessionUser();

    if (!$cfg['ns_allow_search'] || ( (empty($user) || $user['uid'] <= 0) && (!$cfg['ns_allow_anon']) )) {
        return false;
    }

    $tpl->getCssFile('NewsSearch');
    NS_setTopNavSearchbox();

    $frontend->registerPage(['module' => 'NewsSearch', 'page' => 'search', 'type' => 'disk']);
    /* TAGS */
    if ($cfg['ns_tag_support']) {
        register_action('news_new_form_add', 'NS_tag_add_form');
        register_action('news_submit_insert', 'NS_news_mod_insert');
        register_action('news_show_page', 'NS_news_tag_show_page');
        register_action('news_edit_form_add', 'NS_tags_edit_form_add');
        register_action('news_form_update_set', 'NS_news_edit_set_tag');
    }

    return true;
}

function NewsSearch_install() {
    global $db;
    require_once ('db/NewsSearch.db.php');
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
    require_once ('db/NewsSearch.db.php');
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
    require_once ('db/NewsSearch.db.php');
    foreach ($newsSearch_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
