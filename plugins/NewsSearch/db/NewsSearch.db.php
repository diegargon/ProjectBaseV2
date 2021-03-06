<?php

/**
 *  NewsSearch database file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

$newsSearch_database_install = [
    "ALTER TABLE `" . DB_PREFIX . "news` ADD `tags` VARCHAR(255) DEFAULT NULL;",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_allow_search', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_max_s_text', '50');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_min_s_text', '3');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_result_limit', '10');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_tag_support', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_disable_by_stress', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_allow_anon', '1');",
    //"INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', '', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsSearch', 'ns_tag_size_limit', '256');"
];

/* UNINSTALL */

$newsSearch_database_uninstall = [
    "ALTER TABLE `" . DB_PREFIX . "news` DROP `tags`;",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsSearch'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsSearch'"
];

