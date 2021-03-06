<?php

/**
 *  SimpleACL - Install database file
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleACL
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/* CONFIG */

$simpleacl_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleACL', 'simpleacl_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleACL', 'acl_installed', '1');"
];

/* PERMISSIONS */

$simpleacl_database_install[] = "CREATE TABLE `" . DB_PREFIX . "permissions` (
  `perm_id` int(10) UNSIGNED NOT NULL,
  `perm_name` char(255) NOT NULL,
  `perm_desc` varchar(255) NULL,
  `groups` char(255) NULL,
  `plugin` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simpleacl_database_install [] = "ALTER TABLE `" . DB_PREFIX . "permissions` ADD PRIMARY KEY (`perm_id`), ADD UNIQUE KEY `perm_id` (`perm_id`),  ADD UNIQUE KEY `perm_name` (`perm_name`);";
$simpleacl_database_install [] = "ALTER TABLE `" . DB_PREFIX . "permissions` MODIFY `perm_id` int(11) NOT NULL AUTO_INCREMENT;";

/* UNINSTALL */


$simpleacl_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleACL'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleACL'",
    "DROP TABLE `" . DB_PREFIX . "permissions`"
];
