<?php

/**
 *  SimpleGroups Install database
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleGroups
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
/* CONFIG */
$simplegroups_database_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleGroups', 'simplegroups_debug', '1');"
;

/* GROUPS */

$simplegroups_database_install[] = "CREATE TABLE `" . DB_PREFIX . "groups` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `group_name` char(255) NOT NULL, 
  `group_father` smallint(5) UNSIGNED NOT NULL,
  `group_desc` varchar(255) NULL,
  `group_type` varchar(64) NOT NULL,
  `plugin` char(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simplegroups_database_install = [
    "ALTER TABLE `" . DB_PREFIX . "groups` ADD PRIMARY KEY (`group_id`), ADD UNIQUE KEY `group_id` (`group_id`);",
    "ALTER TABLE `" . DB_PREFIX . "groups` MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;"
];

/* GROUPS_INSERTS */
$simplegroups_database_install_insert_admin_group = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_father`, `group_desc`, `group_type`, `plugin`) VALUES
('L_ADMINISTRATOR', '0', 'L_ADMIN_DESC', 'USER', 'GROUPS')
";

$simplegroups_database_install_insert_admin_limited_group = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_father`, `group_desc`, `group_type`, `plugin`) VALUES
('L_ADMIN_LIMITED', '0', 'L_ADMIN_DESC', 'USER', 'GROUPS')
";

$simplegroups_database_install_insert_registered_group = "INSERT INTO `" . DB_PREFIX . "groups` ( `group_name`, `group_father`, `group_desc`, `group_type`, `plugin`) VALUES
('L_REGISTER_USERS', '0', 'L_REGISTER_DESC', 'USER', 'GROUPS')
";

$simplegroups_database_install_anon_group = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_father`, `group_desc`, `group_type`, `plugin`) VALUES
('L_ANONYMOUS_GROUP', '0', 'L_ANONYMOUS_DESC', 'USER', 'GROUPS')
";

/* USERS */
$simplegroups_database_install [] = "ALTER TABLE `" . DB_PREFIX . "users` ADD `groups` VARCHAR(64) DEFAULT NULL";

/* USERS INSTALL */

/* UNINSTALL */

$simplegroups_database_uninstall = [
    "DROP TABLE `" . DB_PREFIX . "groups`",
    "ALTER TABLE `" . DB_PREFIX . "users` DROP `groups`",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleGroups'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleGroups'"
];


