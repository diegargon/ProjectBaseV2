<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

/* */
$newsComments_database_install[] = "
    ALTER TABLE `" . DB_PREFIX . "news` ADD `comments_disabled` TINYINT(1) NOT NULL DEFAULT '1';
";
/* Config */
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'newscomments_debug', '0');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_max_comments_perpage', '10');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_anon_comments', '1');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_new_comments', '1');";
/* UNINSTALL */
$newsComments_database_uninstall = [
    "ALTER TABLE `" . DB_PREFIX . "news` DROP `comments_disabled`;",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsComments'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsComments'"
];