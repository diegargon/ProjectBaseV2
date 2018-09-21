<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$exampleTemplate_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "exampleTemplate` (
  `example_id` int(32) NOT NULL,
  `example_field` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

/* UNINSTALL */

$exampleTemplate_database_uninstall = [
    "DROP TABLE `" . DB_PREFIX . "exampletemplate`",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'ExampleTemplate'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'ExampleTemplate'"
];
/*
 * UPGRADE
 */
// Example upgrade something.
$exampleTemplate_database_upgrade_002_to_003[] = "
ALTER TABLE `" . DB_PREFIX . "exampleTemplate` ADD `test` INT(1) NOT NULL;    
";
