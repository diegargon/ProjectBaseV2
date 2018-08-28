<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $cfg;

$tplbasic_database = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_nav_menu', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_header_menu_home', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_img_home', '" . $cfg['STATIC_SRV_URL'] . "plugins/tplBasic/img/home.png');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_css_optimize', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_css_inline', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_stats_query', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_theme', 'default');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('tplBasic', 'tplbasic_gzip', '1');"
];

$tplbasic_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'tplBasic'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'tplBasic'"
];
