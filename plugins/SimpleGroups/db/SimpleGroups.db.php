<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$simplegroups_database_install[] = 
"CREATE TABLE `" . DB_PREFIX . "groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(16) NOT NULL, 
  `group_desc` varchar(32) NOT NULL,
  `plugin` varchar(32) NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";"
;

$simplegroups_database_install [] = "ALTER TABLE `". DB_PREFIX ."groups` ADD PRIMARY KEY (`group_id`), ADD UNIQUE KEY `group_id` (`group_id`);";
$simplegroups_database_install [] = "ALTER TABLE `". DB_PREFIX ."groups` MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;";

$simplegroups_database_install_insert_admin_group = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_desc`) VALUES
('Admin', 'L_ADMIN_DESC')
";

$simplegroups_database_install_insert_groups = "INSERT INTO `" . DB_PREFIX . "groups` (`group_name`, `group_desc`) VALUES
('L_REGISTER_USERS', 'L_REGISTER_DESC'),
('Anonymous', 'L_ANONYMOUS_DESC')
";
/* USERS */
$simplegroups_database_install [] = "ALTER TABLE `" . DB_PREFIX . "users` ADD `groups` VARCHAR(64) DEFAULT NULL";


/* UNINSTALL */

$simplegroups_database_uninstall[] = "DROP TABLE `" . DB_PREFIX . "groups`";
$simplegroups_database_uninstall[] = "ALTER TABLE `" . DB_PREFIX . "users` DROP `groups`";




