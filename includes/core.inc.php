<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * actions: "index_page" "finalize" "preload_$module_$page"
 * 
 */
!defined('IN_WEB') ? exit : true;

define('CORE_VERSION', 0.2);


require_once "config.default.php";
file_exists("config/config.inc.php") ? require_once "config/config.inc.php" : null; //rewrite config


if (defined('DEBUG')) {
    require_once "includes/" . DEBUG_CORE . ".class.php";
    global $debug;
    $debug = new Debug();
}

require_once "includes/core.func.php";
require_once "includes/actions.inc.php";


require_once "includes/" . FILTER . ".class.php";
$filter = new SecureFilter();

/* SQL */
if (!defined('SQL')) {
    exit("ERROR: Database ins't configured, please read config.default.php");
}
require_once "includes/" . DB_TYPE . ".class.php";
global $db;
$db = new Database(DB_HOST, DB, DB_USER, DB_PASSWORD);
$db->set_charset(DB_CHARSET);
$db->set_prefix(DB_PREFIX);
$db->set_minchar_search(DB_MINCHAR_SEARCH);
$db->connect();


/* GET CONFIG */
$db->silent(true);
$result = $db->select_all("config");
$db->silent(false);

if ($result) {
    $config = $db->fetch_all($result);
    foreach ($config as $conf) {
        $cfg[$conf['cfg_key']] = $conf['cfg_value'];
    }
}

if (CORE_VERSION != (float) $cfg["CORE_VERSION"]) {
    die("Core need upgrade");
}

/* PLUGINS */

require_once "includes/plugins.class.php";
global $plugins;

$plugins = new Plugins();

$plugins->setDepend("CORE", CORE_VERSION);
$plugins->setDepend("DEBUG", CORE_VERSION);
$plugins->setDepend("SQL", CORE_VERSION);



/* CHECK FOR INSTALL */
if (!isset($cfg['CORE_INSTALLED']) || $cfg['CORE_INSTALLED'] != 1) {
    $debug->log("Software ins't intalled", "CORE", "WARNING");

    $plugins->scanDir();
    do_action("install");
    require_once "includes/install.inc.php";
    //$debug->$debug->log();
    exit();
}

/* TIME UTILS */
require_once "includes/time-utils.inc.php";
global $tUtil;
$tUtil = new TimeUtils($cfg, $db);

mobileDetect() ? $cfg['ITS_MOBIL'] = 1 : $cfg['ITS_MOBIL'] = 0;
$cfg['ITS_BOT'] = botDetect();

$plugins->Init();

do_action("init_core");

/*
 * FIN LOAD
 */

$module = $filter->get_strict_chars("module");
$page = $filter->get_strict_chars("page");

if (empty($module) || empty($page)) {
    //exit("Error module or page missed");
    do_action("index_page");
} else {
    !$plugins->check_enabled($module) ? exit("Error plugin ins't enabled") : null;

    $path = "plugins/$module/$page.php";
    if (!file_exists($path)) {
        $msgbox['msg'] = "L_E_PLUGPAGE_NOEXISTS";
        $msgbox['backlink'] = "/";
        $msgbox['backlink_title'] = "L_HOME";
        do_action("message_page", $msgbox);
    } else {
        do_action("preload_" . $module . "_" . $page);
        require_once($path);
    }
}



if (defined('DEBUG')) {
    
    ($cfg['smbasic_debug']) ? setSessionDebugDetails() : null;
    
    $q_history = $db->get_query_history();
    foreach ($q_history as $key => $value) {
        $debug->log($value, "MYSQL");
    }

    $tpl->addto_tplvar("ADD_TO_FOOTER", $debug->print_debug());
    $tpl->addto_tplvar("ADD_TO_FOOTER", formatBytes(memory_get_usage()));
    $tpl->addto_tplvar("ADD_TO_FOOTER", formatBytes(memory_get_peak_usage()));
}
$tpl->build_page();

do_action("finalize");
