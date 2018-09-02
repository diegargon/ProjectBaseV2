<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
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
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleFrontend', 'simplefrontend_css_optimize', '1');"
];
/* TABLE */
/*
  $simplefrontend_database[] = "
  CREATE TABLE `" . DB_PREFIX . "simplefrontend` (
  `example_id` int(32) NOT NULL,
  `example_field` varchar(32) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
  ";
 */
/* UNINSTALL */

//$simplefrontend_db_uninstall[] = "DROP TABLE `" . DB_PREFIX . "simplefrontend`";

$simplefrontend_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleFrontend'";
$simplefrontend_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleFrontend'";

/*
 * UPGRADE
 */
// Example upgrade something.
/*
$simplefrontend_database_upgrade_002_to_003[] = "
ALTER TABLE `" . DB_PREFIX . "simplefrontend` ADD `test` INT(1) NOT NULL AFTER `example_field`;    
";
*/