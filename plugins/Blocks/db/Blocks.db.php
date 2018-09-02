<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$blocks_database[] = "
CREATE TABLE `" . DB_PREFIX . "blocks` (
  `block_id` int(32) NOT NULL,
  `block_func` varchar(32) NOT NULL,
  `block_desc` varchar(255) NOT NULL,
  `plugin` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

/* UNINSTALL */

$blocks_database_uninstall[] = "
DROP TABLE `" . DB_PREFIX . "blocks`
";

$blocks_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'blocks'";

