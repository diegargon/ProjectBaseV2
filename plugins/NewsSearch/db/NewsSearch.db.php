<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$newsSearch_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_allow_search', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_max_s_text', '50');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_min_s_text', '3');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_result_limit', '10');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_tag_support', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_tag_size_limit', '256');",
        //"INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', '', '');",
];

/* UNINSTALL */

$newsSearch_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsSearch'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsSearch'"
];

