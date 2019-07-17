<?php

/**
 *  StdComments - Database file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage StdComments
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
$stdComments_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "comments` (
  `cid` int(10) UNSIGNED NOT NULL,
  `plugin` char(255) NOT NULL,
  `resource_id` int(10) UNSIGNED NOT NULL,
  `lang_id` tinyint(2) NOT NULL,
  `comment` text NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
  `shadow_ban` tinyint(1) NOT NULL DEFAULT '0',
  `reported` int(10) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

/* CONFIG */
//$stdComments_database_install = [
//   "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('stdComments', 'comments_moderation', '0');",
//];
$stdComments_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "comments`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `cid` (`cid`);
";

$stdComments_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "comments`
  MODIFY `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
";

/* UNINSTALL */

$stdComments_database_uninstall = [
    "DROP TABLE `" . DB_PREFIX . "comments`",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'StdComments'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'StdComments'"
];

