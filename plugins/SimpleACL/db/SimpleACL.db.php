<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

/* ROLES */

$simpleacl_database_install [] = "CREATE TABLE `" . DB_PREFIX . "acl_roles` (
  `role_id` int(11) NOT NULL,
  `level` int(4) NOT NULL,
  `role_group` varchar(16) NOT NULL,
  `role_type` varchar(8) NOT NULL,
  `role_name` varchar(32) NOT NULL,  
  `role_description` varchar(32) NOT NULL,
  `resource` varchar(11) NOT NULL DEFAULT 'ALL'
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simpleacl_database_install [] = "ALTER TABLE `pb_acl_roles` ADD PRIMARY KEY (`role_id`), ADD UNIQUE KEY `role_id` (`role_id`);";

$simpleacl_database_install [] = "ALTER TABLE `pb_acl_roles` MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;";

$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "acl_roles` (`level`, `role_group`, `role_type`,  `role_name`, `role_description`, `resource`) VALUES
(1, 'admin', 'all', 'L_ADMIN_ALL', 'L_ADMIN_ALL_DESC','ALL'),
(2, 'admin', 'write',  'L_ADMIN_WRITE', 'L_ADMIN_WRITE_DESC', 'ALL'),
(3, 'admin', 'append', 'L_ADMIN_APPEND', 'L_ADMIN_APPEND_DESC', 'ALL'),
(4, 'admin', 'read', 'L_ADMIN_READ', 'L_ADMIN_READ_DESC', 'ALL')
";

$simpleacl_database_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleACL', 'simpleacl_debug', '0');"
;
/* USERS */

$simpleacl_database_install [] = "ALTER TABLE `" . DB_PREFIX . "users` ADD `roles` VARCHAR(64) DEFAULT NULL";
$simpleacl_database_install [] = "UPDATE `" . DB_PREFIX . "users` SET roles = '1' WHERE isAdmin = '1'";

/* UNINSTALL */

$simpleacl_database_uninstall [] = "DROP TABLE `" . DB_PREFIX . "acl_roles`";
$simpleacl_database_uninstall [] = "ALTER TABLE `" . DB_PREFIX . "users` DROP `roles`;";
$simpleacl_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleACL'";
