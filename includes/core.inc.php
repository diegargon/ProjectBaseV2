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
    $debug = new Debug();
}

require_once "includes/core.func.php";
require_once "includes/core.utils.php";
require_once "includes/actions.inc.php";
require_once "includes/" . FILTER . ".class.php";

$filter = new SecureFilter();

/* SQL */
if (!defined('SQL')) {
    exit("ERROR: Database ins't configured, please read config.default.php");
} else {
    core_setup_database();
}

/* GET CONFIG */
core_set_config();

/* PLUGINS */
require_once "includes/plugins.class.php";
$plugins = new Plugins();

/* CHECK FOR INSTALL */
core_check_install();

//SET CORE VERSIONS
$plugins->setDepend("CORE", CORE_VERSION);
$plugins->setDepend("DEBUG", CORE_VERSION);
$plugins->setDepend("SQL", CORE_VERSION);

/* TIME UTILS */
require_once "includes/time-utils.inc.php";

if (mobileDetect()) {
    $cfg['ITS_MOBIL'] = 1;
    $cfg['img_selector'] = "mobil";
} else {
    $cfg['ITS_MOBIL'] = 0;
    $cfg['img_selector'] = "desktop";
}
$cfg['ITS_BOT'] = botDetect();

$plugins->Init();
do_action("init_core");

/*
 * FIN LOAD
 */
$module = $filter->get_strict_chars("module");
$page = $filter->get_strict_chars("page");

if (!empty($module) && !empty($page)) {
    if (!core_check_module($module)) {
        $frontend->message_box(['msg' => 'L_E_PL_CANTEXPRESS']);
    } else {
        if (!($request_page = $frontend->getPage($module, $page))) {
            $frontend->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
        } else if ($request_page['type'] == 'virtual') {
            $frontend->vpage($request_page);
        } else if ($request_page['type'] == 'disk') {
            $path = "plugins/{$request_page['module']}/{$request_page['page']}.php";
            if (!file_exists($path)) {
                $frontend->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            } else {
                do_action("preload_{$request_page['module']}_{$request_page['page']}");
                require_once($path);
            }
        }
    }
} else {
    $frontend->index_page();
}
$frontend->send_page();

do_action("finalize");
