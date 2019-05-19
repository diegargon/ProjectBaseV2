<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 * 
 * actions: "index_page" "finalize" "preload_$module_$page"
 * 
 */
!defined('IN_WEB') ? exit : true;

define('CORE_VERSION', 0.2);

require_once ('includes/core.utils.php');
$start_time = set_load_time();
require_once ('includes/core.func.php');
require_once ('includes/actions.inc.php');
require_once ('config.default.php');
file_exists('config/config.inc.php') ? require_once ('config/config.inc.php') : null; //rewrite config

if (defined('DEBUG')) {
    require_once 'includes/' . DEBUG_CORE . '.class.php';
    $debug = new Debug();
}

/* SQL */
if (!defined('SQL')) {
    exit('ERROR: Database ins\'t configured, please read config.default.php');
} else {
    global $db;
    core_setup_database();
}

/* PLUGINS */
require_once ('includes/plugins.class.php');
$plugins = new Plugins();

/* GET CONFIG */
global $cfg;
core_set_config();

/* CHECK FOR INSTALL */
core_check_install();

/* FILTER */
require_once ('includes/' . FILTER . '.class.php');
$filter = new SecureFilter();

//SET CORE VERSIONS
$plugins->setDepend('CORE', CORE_VERSION);
$plugins->setDepend('DEBUG', CORE_VERSION);
$plugins->setDepend('SQL', CORE_VERSION);

/* TIME UTILS */
require_once ('includes/TimeUtil.class.php');
$timeUtil = new TimeUtil();
$timeUtil->configTime();

if (mobileDetect()) {
    $cfg['ITS_MOBIL'] = 1;
    $cfg['img_selector'] = 'mobil';
} else {
    $cfg['ITS_MOBIL'] = 0;
    $cfg['img_selector'] = 'desktop';
}
$cfg['ITS_BOT'] = botDetect();

/* INIT PLUGINS */
$plugins->Init();

do_action('init_core');

$frontend->setStartTime($start_time);

/*
 * FIN LOAD
 */
$module = $filter->get_strict_chars('module', 255, 1);
$page = $filter->get_strict_chars('page', 255, 1);

if (!empty($module) && !empty($page)) {
    if (!core_check_module($module)) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
    } else {
        if (!($request_page = $frontend->getPage($module, $page))) {
            $frontend->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
        } else if ($request_page['type'] == 'virtual') {
            $frontend->vPage($request_page);
        } else if ($request_page['type'] == 'disk') {
            $path = "plugins/{$request_page['module']}/{$request_page['page']}.php";
            if (!file_exists($path)) {
                $frontend->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            } else {
                do_action("preload_{$request_page['module']}_{$request_page['page']}");
                require_once($path);
            }
        }
    }
} else {
    $frontend->indexPage();
}
$frontend->sendPage();

do_action('finalize');
