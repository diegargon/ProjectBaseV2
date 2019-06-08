<?php

/**
 *  News - Main file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function News_init() {
    define('NEWS', true);
    global $frontend, $cfg, $plugins;

    $plugins->expressStartProvider('SESSIONS');

    $news_perms = get_news_perms('init');
    if ($news_perms['news_submit_new']) {
        register_action('header_menu_element', 'submit_news_menu');
    }

    $pages_array = [
        ['module' => 'News', 'page' => 'section', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'view_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'submit_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'edit_news', 'type' => 'disk']
    ];
    if (!$frontend->registerPageArray($pages_array)) {
        die('Register pages fail on News module');
    }

    if (defined('BLOCKS')) {
        global $blocks;
        require_once ('includes/news_blocks.inc.php');

        $blocks->registerBlock('news_block', '', 'news_block', 'news_block_conf', null, 0);
    }
    if ($cfg['display_section_menu']) {
        $plugins->expressStartProvider('CATS');
        register_action('section_nav_element', 'news_section_nav_elements');
        register_action('section_nav_subelement', 'news_section_nav_subelements');
    }
    if (defined('MULTILANG')) {
        register_action('SMBasic_ProfileEdit', 'news_dropdown_profile_edit');
        register_action('SMBasic_ProfileChange', 'news_dropdown_profile_change');
    }
}

function News_install() {
    global $db;
    require_once ('db/News.db.php');
    foreach ($news_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function News_preInstall() {
    return true;
}

function News_preInstall_info() {
    return true;
}

function News_upgrade($version, $from_version) {
    /*
      global $db;
      require_once "db/News.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($news_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : false;
      }

     */
    return false;
}

function News_uninstall() {
    global $db;
    require_once ('db/News.db.php');
    foreach ($news_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
