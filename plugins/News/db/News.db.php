<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$news_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "news` (
  `nid` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `lead` text,
  `text` longtext NOT NULL,
  `acl` varchar(11) NOT NULL,
  `author` varchar(32) NOT NULL,
  `author_id` int(11) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `category` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `featured_date` timestamp NULL DEFAULT NULL,
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
  `visits` int(32) NOT NULL DEFAULT '0',
  `translator` varchar(32) DEFAULT NULL,
  `translator_id` int(11) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `tags` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$news_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "news`
  ADD PRIMARY KEY (`nid`,`lang_id`,`page`),
  ADD UNIQUE KEY `nid` (`nid`,`lang_id`,`page`);
        ";

$news_database_inserts = [
    /* CONFIG */
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_list_moderation_limits', '200');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_source', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_related', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_send_main_cats', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_link_min_length', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_link_max_length', '256');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_lead_min_length', '30');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_lead_max_length', '256');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_title_min_length', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_title_max_length', '256');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_text_min_length', '20');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_text_max_length', '10000');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'display_news_related', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'display_news_source', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_moderation, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'allow_multiple_pages, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_stats, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_adv_stats, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_meta_opengraph, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_anon_translate, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_page_sidenews', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_breadcrum, '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_breadcrum_separator, '');"
    
];

$news_database_install = array_merge($news_database_install, $news_database_inserts);

/* UNINSTALL */

$news_database_uninstall[] = "
DROP TABLE `" . DB_PREFIX . "news`
";

$news_database_uinstall = [
    "DROP TABLE `" . DB_PREFIX . "news`",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'News'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'News'"
];
