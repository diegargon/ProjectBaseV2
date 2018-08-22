<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

/* CONFIG */

$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleACL', 'simpleacl_debug', '1');"
;

/* PERMISSIONS */

$simpleacl_database_install[] = "CREATE TABLE `" . DB_PREFIX . "permissions` (
  `perm_id` int(11) NOT NULL,
  `perm_group` varchar(64) NOT NULL,
  `perm_type` varchar(64) NOT NULL,
  `perm_level` int(1) NOT NULL,
  `perm_desc` varchar(255) NULL,
  `groups` varchar(128) NULL,
  `plugin` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"        
;

$simpleacl_database_install [] = "ALTER TABLE `" . DB_PREFIX . "permissions` ADD PRIMARY KEY (`perm_id`), ADD UNIQUE KEY `perm_id` (`perm_id`);";
$simpleacl_database_install [] = "ALTER TABLE `" . DB_PREFIX . "permissions` MODIFY `perm_id` int(11) NOT NULL AUTO_INCREMENT;";


$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "permissions` (`perm_group`, `perm_type`,  `perm_level`, `perm_desc`, `plugin`) VALUES
('admin', 'all', '1', 'L_ADMIN_ALL_DESC','SimpleACL'),
('admin', 'append', '2', 'L_ADMIN_APPEND_DESC','SimpleACL'),
('admin', 'read', '3', 'L_ADMIN_READ_DESC','SimpleACL')
";

/* UNINSTALL */


$simpleacl_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleACL'";
$simpleacl_database_uninstall [] =  "DROP TABLE `" . DB_PREFIX . "permissions`";


