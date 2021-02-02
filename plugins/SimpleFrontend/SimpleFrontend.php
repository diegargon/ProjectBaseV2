<?php

/**
 *  SimpleFrontend
 * 
 *  responsible for displaying the content
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 * 
 * @global SimpleFrontend $frontend
 */
function SimpleFrontend_init() {
    global $frontend, $tpl, $cfg, $plugins;

    !defined('FRONTEND') ? $frontend = new SimpleFrontend() : null;
    define('FRONTEND', true);

    if ($cfg['tplbasic_header_menu_home']) {
        $plugins->expressStart('Multilang');
        if ($cfg['FRIENDLY_URL']) {
            $home_link['home_url'] = $cfg['REL_PATH'] . $cfg['WEB_LANG'];
        } else {
            $home_link['home_url'] = $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?lang=' . $cfg['WEB_LANG'];
        }
        $frontend->addMenuItem('top_menu_left', $tpl->getTplFile('SimpleFrontend', 'home_menu_opt', $home_link), 1);
    }
    
    /* Default SimpleFrontend layouts */
    $frontend->registerLayout(['name' => 'Index 3 Colums', 'page' => 'index', 'plugin' => 'SimpleFrontend', 'file' => 'index_3', 'sections' => 3]);
    $frontend->registerLayout(['name' => 'Index 2 Colums', 'page' => 'index', 'plugin' => 'SimpleFrontend', 'file' => 'index_2', 'sections' => 2]);
    $frontend->registerLayout(['name' => 'Index 1 Colums', 'page' => 'index', 'plugin' => 'SimpleFrontend', 'file' => 'index_1', 'sections' => 2]);

    $frontend->registerPage(['module' => 'SimpleFrontend', 'page' => 'index', 'type' => 'virtual', 'func' => [$frontend,'index_page']]);
    return true;
}

/**
 * Install function
 * 
 * @global db $db
 * @global array $cfg
 * @return boolean
 */
function SimpleFrontend_install() {
    global $db, $cfg;

    require_once ('db/SimpleFrontend.db.php'); // need $cfg
    foreach ($simplefrontend_db_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

/**
 * preInstall function
 * 
 * @return boolean
 */
function SimpleFrontend_preInstall() {
    return true;
}

/**
 * preInstall info function
 * 
 * @return boolean
 */
function SimpleFrontend_preInstall_info() {
    return true;
}

/**
 * Upgrade function
 * 
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
function SimpleFrontend_upgrade($version, $from_version) {
    return true;
    /*
      global $db;
      require_once "db/SimpleFrontend.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($exampleTemplate_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : null;
      }
      return false;
     * 
     */
}

/**
 * Uninstall function
 * 
 * @global db $db
 * @global array $cfg
 * @return boolean
 */
function SimpleFrontend_uninstall() {
    global $db, $cfg;
    require_once ('db/SimpleFrontend.db.php'); //need $cfg
    foreach ($simplefrontend_db_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
