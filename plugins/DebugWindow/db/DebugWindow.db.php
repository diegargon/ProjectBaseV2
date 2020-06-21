<?php

/**
 *  DebugWindow - Install DB file
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage DebugWindow
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

$debugWindow_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('DebugWindow', 'debugwindow_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('DebugWindow', 'debugwindow_only_root', '1');",    
];


/* UNINSTALL */

$debugWindow_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'DebugWindow'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'DebugWindow'"
];
