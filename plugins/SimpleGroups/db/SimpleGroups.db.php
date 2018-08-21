<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */


/* CONFIG */
$simplegroups_database_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleGroups', 'simplegroups_debug', '1');"
;

/* GROUPS */

$simplegroups_database_install[] = "CREATE TABLE `" . DB_PREFIX . "groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(18) NOT NULL, 
  `group_desc` varchar(255) NULL,
  `group_type` varchar(64) NOT NULL,
  `plugin` varchar(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simplegroups_database_install [] = "ALTER TABLE `" . DB_PREFIX . "groups` ADD PRIMARY KEY (`group_id`), ADD UNIQUE KEY `group_id` (`group_id`);";
$simplegroups_database_install [] = "ALTER TABLE `" . DB_PREFIX . "groups` MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;";

/* GROUPS_INSERTS */
$simplegroups_database_install_insert_admin_group = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_desc`, `group_type`, `plugin`) VALUES
('L_ADMINISTRATOR', 'L_ADMIN_DESC', 'USER', 'GROUPS')
";

$simplegroups_database_install_insert_groups = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_desc`, `group_type`, `plugin`) VALUES
('L_REGISTER_USERS', 'L_REGISTER_DESC', 'USER', 'GROUPS'),
('L_ANONYMOUS', 'L_ANONYMOUS_DESC', 'USER', 'GROUPS')
";

/* USERS */
$simplegroups_database_install [] = "ALTER TABLE `" . DB_PREFIX . "users` ADD `groups` VARCHAR(64) DEFAULT NULL";

/* USERS INSTALL */

/* UNINSTALL */

$simplegroups_database_uninstall[] = "DROP TABLE `" . DB_PREFIX . "groups`";
$simplegroups_database_uninstall[] = "ALTER TABLE `" . DB_PREFIX . "users` DROP `groups`";
$simplegroups_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleGroups'";



