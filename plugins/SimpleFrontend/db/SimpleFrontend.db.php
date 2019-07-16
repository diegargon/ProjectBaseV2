<?php

/**
 *  SimpleFrontend - Database
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
/* INSTALL */

/* CONFIG */

$simplefrontend_db_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_nav_menu', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_header_menu_home', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_img_home', '" . $cfg['STATIC_SRV_URL'] . "plugins/SimpleFrontend/img/home.png');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_stats_query', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_theme', 'default');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_css_inline', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_css_optimize', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'index_plugin_layout', 'SimpleFrontend');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'index_layout', 'index_1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'display_section_menu', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'index_sections', '3');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'show_load_time', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'show_memory_usage', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'loading_screen_support', '1');"
];

$simplefrontend_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleFrontend'";
$simplefrontend_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleFrontend'";
