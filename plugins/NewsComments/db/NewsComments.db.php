<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$newsComments_database_install[] = "
";

/* Config */
$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'newscomments_debug', '0');";
//$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_max_comments_perpage', '10');";
//$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', 'nc_allow_anon_comments', '1');";
//$newsComments_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsComments', '', '');";
/* UNINSTALL */
$newsComments_database_uninstall = [
//    "DROP TABLE `" . DB_PREFIX . "NewsComments`",
//    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsComments'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsComments'"
];


/*
$cfg['NC_MAX_COMMENTS_PERPAGE'] = 10;
$cfg['NC_ALLOW_NEW_COMMENTS'] = 1;
$cfg['NC_ALLOW_ANON_COMMENTS'] = 0;

 */