<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$blocks_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "blocks` (
  `blocks_id` int(8) NOT NULL,
  `uid` int(11) NOT NULL,
  `blockname` varchar(64) NOT NULL,
  `plugin` varchar(64) NOT NULL,
  `blockconf` varchar(256) NOT NULL,
  `page` varchar(32) NOT NULL,
  `section` int(8) NOT NULL,
  `weight` tinyint(2) NOT NULL DEFAULT '5',
  `canUserDisable` tinyint(1) NOT NULL DEFAULT '1
  `admin_block` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$blocks_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "blocks`
  ADD PRIMARY KEY (`blocks_id`),
  ADD UNIQUE KEY `blocks_id` (`blocks_id`),
  ADD KEY `blockname` (`blockname`);
";

$blocks_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "blocks`
  MODIFY `blocks_id` int(8) NOT NULL AUTO_INCREMENT;
";

$blocks_database_install[] = "
    INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('Blocks', 'blocks_debug', '1');
";
/* UNINSTALL */

$blocks_database_uninstall[] = "
DROP TABLE `" . DB_PREFIX . "blocks`
";

$blocks_database_uninstall[] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'Blocks'";
$blocks_database_uninstall[] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'Blocks'";
