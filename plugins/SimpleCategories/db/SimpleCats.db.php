<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */

/* INSTALL */

/* CONFIG */

$simplecats_db_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleCategories', 'simplecats_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SimpleCategories', 'categories_separator', '/');"
];

/* MAIN */

$simplecats_db_install [] = "
CREATE TABLE `" . DB_PREFIX . "categories` (
  `cid` int(10) UNSIGNED NOT NULL,
  `plugin` char(255) NOT NULL,
  `lang_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `father` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `weight` tinyint(3) UNSIGNED NOT NULL DEFAULT '3',
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$simplecats_db_install [] = "ALTER TABLE `" . DB_PREFIX . "categories` ADD PRIMARY KEY (`cid`,`plugin`,`lang_id`);";

/* EXAMPLE CATS */
$simplecats_db_install [] = "INSERT INTO `pb_categories` (`cid`, `plugin`, `lang_id`, `name`, `father`, `weight`, `views`) VALUES
(1, 'News', 1, 'Noticias', 0, 0, 0),
(1, 'News', 2, 'News', 0, 0, 0),
(2, 'News', 1, 'Politica', 1, 0, 0),
(2, 'News', 2, 'Politics', 1, 0, 0),
(3, 'News', 1, 'Mundo', 1, 0, 0),
(3, 'News', 2, 'World', 1, 0, 0),
(4, 'News', 1, 'Tecnologia', 1, 0, 0),
(4, 'News', 2, 'Tech', 1, 0, 0),
(5, 'News', 1, 'Opinion', 0, 0, 0),
(5, 'News', 2, 'Opinion', 0, 0, 0),
(6, 'News', 1, 'Actualidad', 5, 0, 0),
(6, 'News', 2, 'Present', 5, 0, 0),
(7, 'News', 1, 'Politica', 5, 0, 0),
(7, 'News', 2, 'Politics', 5, 0, 0);
";

/* UNINSTALL */

$simplecats_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SimpleCategories'";
$simplecats_db_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SimpleCategories'";
$simplecats_db_uninstall [] = "DROP TABLE `" . DB_PREFIX . "categories`";
