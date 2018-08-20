<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

/* ROLES */

$simpleacl_database_install [] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(16) NOT NULL, 
  `group_desc` varchar(32) NOT NULL,
  `uids` varchar(32) NOT NULL,
  `plugins_using` int(2) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleACL', 'simpleacl_debug', '1');"
;

$simpleacl_database_first_install [] = "ALTER TABLE `". DB_PREFIX ."groups` ADD PRIMARY KEY (`group_id`), ADD UNIQUE KEY `group_id` (`group_id`);";
$simpleacl_database_first_install [] = "ALTER TABLE `". DB_PREFIX ."groups` MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;";

$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "acl_roles` (`level`, `role_group`, `role_type`,  `role_name`, `role_description`, `resource`) VALUES
(1, 'admin', 'all', 'L_ADMIN_ALL', 'L_ADMIN_ALL_DESC','ALL'),
(2, 'admin', 'write',  'L_ADMIN_WRITE', 'L_ADMIN_WRITE_DESC', 'ALL'),
(3, 'admin', 'append', 'L_ADMIN_APPEND', 'L_ADMIN_APPEND_DESC', 'ALL'),
(4, 'admin', 'read', 'L_ADMIN_READ', 'L_ADMIN_READ_DESC', 'ALL')
";


/* USERS */

$simpleacl_database_first_install [] = "ALTER TABLE `" . DB_PREFIX . "users` ADD `groups` VARCHAR(64) DEFAULT NULL";
$simpleacl_database_install [] = "UPDATE `" . DB_PREFIX . "users` SET groups = '1' WHERE isAdmin = '1'";

/* UNINSTALL */

//$simpleacl_database_uninstall [] = "ALTER TABLE `" . DB_PREFIX . "users` DROP `roles`;";
$simpleacl_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleACL'";
$simpleacl_database_uninstall_lasted [] = "DROP TABLE `" . DB_PREFIX . "groups`";