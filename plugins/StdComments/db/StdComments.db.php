<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$stdComments_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "comments` (
  `cid` int(10) UNSIGNED NOT NULL,
  `plugin` char(255) NOT NULL,
  `resource_id` int(10) UNSIGNED NOT NULL,
  `lang_id` tinyint(2) NOT NULL,
  `message` longtext NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

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
