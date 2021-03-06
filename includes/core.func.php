<?php

/**
 *  Core Functions
 * 
 *  Core functions
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Retrieve from the database the config to populate $cfg
 * @global db $db
 * @global array $cfg
 */
function core_set_config() {
    global $db, $cfg;

    $db->silent(true);
    $result = $db->select('config', 'cfg_key, cfg_value');
    $db->silent(false);

    if ($result) {
        $config = $db->fetchAll($result);
        foreach ($config as $conf) {
            $cfg[$conf['cfg_key']] = $conf['cfg_value'];
        }
        return true;
    }

    return false;
}

/**
 * Setup database
 * @global db $db
 */
function core_setup_database() {
    global $db;

    require_once ('includes/' . DB_TYPE . '.class.php');

    $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
    $db->setPrefix(DB_PREFIX);
    $db->setMinCharSearch(DB_MINCHAR_SEARCH);
    $db->connect();
    $db->setCharset(DB_CHARSET);
}

/**
 * Check the module request (url) exists, enabled, started, will express 
 * start if enabled and not autostart.
 * @global plugins $plugins
 * @param  string $module
 * @return boolean
 */
function core_check_module($module) {
    global $plugins;

    !$plugins->checkEnabled($module) ? exit('Error plugin ins\'t enabled') : null;

    if (!$plugins->checkStarted($module)) {
        if (!$plugins->expressStart($module)) {
            return false;
        }
    }
    return true;
}

/**
 * Check if core is installed or need upgrade.
 * @global array $cfg
 * @global debug $debug
 * @global plugins $plugins
 */
function core_check_install() {
    global $cfg, $debug, $plugins;

    if (!isset($cfg['CORE_INSTALLED']) || $cfg['CORE_INSTALLED'] != 1) {
        $debug->log('Software ins\'t intalled', 'CORE', 'WARNING');

        $plugins->scanDir();
        do_action('install_core');
        require_once ('includes/install.inc.php');
        //$debug->$debug->log();
        exit();
    }
    /* CHECK VERSION */

    if (CORE_VERSION != (float) $cfg['CORE_VERSION']) {
        die('Core need upgrade');
    }
}
