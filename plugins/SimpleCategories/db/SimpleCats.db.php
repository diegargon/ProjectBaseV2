<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */


/* INSTALL */

/* CONFIG */

$simplecats_db_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleCategories', 'simplecats_debug', '1');";
/* MAIN */

$simplecats_db_install [] = "
CREATE TABLE `" . DB_PREFIX . "categories` (
  `cid` int(11) NOT NULL,
  `plugin` varchar(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `father` smallint(3) NOT NULL DEFAULT '0',
  `weight` smallint(3) NOT NULL DEFAULT '0',
  `acl` varchar(32) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$simplecats_db_install [] = "ALTER TABLE `" . DB_PREFIX . "categories` ADD PRIMARY KEY (`cid`,`plugin`,`lang_id`);";




/* UNINSTALL */


$simplecats_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleCategories'";
$simplecats_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleCategories'";
$simplecats_db_uninstall [] = "DROP TABLE `" . DB_PREFIX . "categories`";
