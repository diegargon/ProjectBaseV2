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

/**
 * News Init func
 * @global SimpleFrontend $frontend
 * @global array $cfg
 * @global Plugins $plugins
 * @global SessionManager $sm
 * @global Database $db
 * @global Blocks $blocks
 * @return boolean
 */
function News_init() {
    define('NEWS', true);
    global $frontend, $cfg, $plugins, $sm;

    $plugins->expressStartProvider('SESSIONS');

    if (isset($cfg['acl_installed']) && !isset($cfg['news_acl_install'])) {
        global $db;
        require_once ('db/News.db.php');
        foreach ($news_acl_install as $query) {
            $db->query($query);
        }
    }

    $pages_array = [
        ['module' => 'News', 'page' => 'section', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'view_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'submit_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'edit_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'drafts', 'type' => 'virtual', 'func' => 'drafts_page'],
    ];
    if (!$frontend->registerPageArray($pages_array)) {
        die('Register pages fail on News module');
    }

    if (defined('BLOCKS')) {
        global $blocks;
        require_once ('includes/news_blocks.inc.php');

        $blocks->registerBlock('news_block', '', 'news_block', 'news_block_conf', null, 0);
    }
    (news_perm_ask('w_news_create||w_news_adm_all')) ? add_menu_submit_news() : null;

    if ($cfg['display_section_menu']) {
        $plugins->expressStartProvider('CATS');
        $frontend->addMenuItem('sections_menu', news_section_menu_elements());
        $frontend->addMenuItem('sections_sub_menu', news_section_menu_subelements());
    }
    if ($cfg['news_allow_user_drafts'] && $sm->getSessionUserId() > 0) {
        $frontend->addMenuItem('dropdown_menu', news_dropdown_items());
    }
    if (defined('MULTILANG')) {
        register_action('SMBasic_ProfileEdit', 'news_dropdown_profile_edit');
        register_action('SMBasic_ProfileChange', 'news_dropdown_profile_change');
    }

    return true;
}

/**
 * News insstall
 * @global Database $db
 * @return boolean
 */
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

/**
 * News preInstall
 * @return boolean
 */
function News_preInstall() {
    return true;
}

/**
 * News preInstall info
 * @return boolean
 */
function News_preInstall_info() {
    return true;
}

/**
 * News upgrade
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
function News_upgrade($version, $from_version) {
    /*
      global $db;
      require_once "db/News.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($news_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : null;
      }

     */
    return false;
}

/**
 * News uninstall
 * @global Database $db
 * @return boolean
 */
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
