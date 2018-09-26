<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$StdRatings_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "rating` (
  `rid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '0',
  `section` varchar(255) NOT NULL,
  `resource_id` int(10) UNSIGNED NOT NULL,
  `vote_value` float(2,1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$StdRatings_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "rating`
  ADD PRIMARY KEY (`rid`),
  ADD UNIQUE KEY `rid` (`rid`);
";

$StdRatings_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "rating`
  MODIFY `rid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
";

$StdRatings_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('StdRatings', 'one_ip_one_vote', '1');";
$StdRatings_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('StdRatings', 'allow_anonymous_vote', '1');";
$StdRatings_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('StdRatings', 'dflt_vote_visuals_url', '/plugins/NewsComments/tpl/img/stars.png');";
//$StdRatings_database_install[] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('StdRatings', '', '');";

/* UNINSTALL */

$StdRatings_database_uninstall = [
    "DROP TABLE `" . DB_PREFIX . "rating`",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'StdRatings'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'StdRatings'"
];

