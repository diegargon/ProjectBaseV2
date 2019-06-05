<?php

/**
 *  Newscomments database file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Newscomments
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/* */
$newsComments_database_install = [
    "ALTER TABLE `" . DB_PREFIX . "news` ADD `comments_disabled` TINYINT(1) NOT NULL DEFAULT '0';"
];

$newsComments_database_install[] = "
    ALTER TABLE `" . DB_PREFIX . "comments` ADD `rating_close` tinyint(1) NOT NULL DEFAULT '0';
";
/* Config */
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'newscomments_debug', '0');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_disable_by_stress', '0');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_max_comments_perpage', '10');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_anon_comments', '1');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_vote_comments', '1');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_vote_comments', '1');";
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_new_comments', '1');";
$stdComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('stdComments', 'stdcomm_allow_author_delete', '0');";
$stdComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('stdComments', 'stdcomm_allow_author_softdelete', '1');";
$stdComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('stdComments', 'stdcomm_allow_author_shadowban', '0');";
$stdComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('stdComments', 'stdcomm_allow_comm_report', '1');";

/* UNINSTALL */
$newsComments_database_uninstall = [
    "ALTER TABLE `" . DB_PREFIX . "news` DROP `comments_disabled`;",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsComments'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsComments'"
];
