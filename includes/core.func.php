<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function core_set_config() {
    global $db, $cfg;
    
    $db->silent(true);
    $result = $db->select_all("config");
    $db->silent(false);

    if ($result) {
        $config = $db->fetch_all($result);
        foreach ($config as $conf) {
            $cfg[$conf['cfg_key']] = $conf['cfg_value'];
        }
    } else {
        die("Setup core config error");
    }
    $db->free($result);
}

function core_setup_database() {
    global $db;

    require_once "includes/" . DB_TYPE . ".class.php";

    $db = new Database(DB_HOST, DB, DB_USER, DB_PASSWORD);
    $db->set_charset(DB_CHARSET);
    $db->set_prefix(DB_PREFIX);
    $db->set_minchar_search(DB_MINCHAR_SEARCH);
    $db->connect();
}

function core_check_module($module) {
    global $plugins;

    !$plugins->check_enabled($module) ? exit("Error plugin ins't enabled") : null;

    if (!$plugins->check_started($module)) {
        if (!$plugins->express_start($module)) {
            return false;
        }
    }
    return true;
}

function core_check_install() {
    global $cfg, $debug, $plugins;

    if (!isset($cfg['CORE_INSTALLED']) || $cfg['CORE_INSTALLED'] != 1) {
        $debug->log("Software ins't intalled", "CORE", "WARNING");

        $plugins->scanDir();
        do_action("install");
        require_once "includes/install.inc.php";
        //$debug->$debug->log();
        exit();
    }
    /* CHECK VERSION */

    if (CORE_VERSION != (float) $cfg["CORE_VERSION"]) {
        die("Core need upgrade");
    }
}
