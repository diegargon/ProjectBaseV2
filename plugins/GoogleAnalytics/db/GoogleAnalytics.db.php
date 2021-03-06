<?php

/**
 *  GoogleAnalytics Database file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage GoogleAnalytics
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
/* INSTALL */
$google_analytics_db_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('GoogleAnalytics', 'GOOGLE_ANALYTICS_CODE', 'UA-XXXXXX-X');"
];

/* UNINSTALL */

$google_analytics_db_uninstall[] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'GoogleAnalytics'";
$google_analytics_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'GoogleAnalytcs'";
