<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

/* INSTALL */
$google_analytics_db_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('GoogleAnalytics', 'GOOGLE_ANALYTICS_CODE', 'UA-XXXXXX-X');"
];

/* UNINSTALL */

$google_analytics_db_uninstall[] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'GoogleAnalytics'";
