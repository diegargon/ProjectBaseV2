<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$news_database_install[] = "
CREATE TABLE `" . DB_PREFIX . "news` (
  `nid` int(10) UNSIGNED NOT NULL,
  `lang_id` int(10) UNSIGNED NOT NULL,
  `page` smallint(8) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `lead` text,
  `text` longtext NOT NULL,
  `acl` char(255) DEFAULT NULL,
  `author` char(255) NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `lang` char(255) NOT NULL,
  `category` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `featured_date` timestamp NULL DEFAULT NULL,
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
  `visits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `translator` char(255) DEFAULT NULL,
  `translator_id` int(10) UNSIGNED DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$news_database_install[] = "
ALTER TABLE `" . DB_PREFIX . "news`
  ADD PRIMARY KEY (`nid`,`lang_id`,`page`),
  ADD UNIQUE KEY `nid` (`nid`,`lang_id`,`page`);
        ";

/* EXAMPLE NEWS */
$news_database_install[] = "
INSERT INTO `pb_news` (`nid`, `lang_id`, `page`, `title`, `lead`, `text`, `acl`, `author`, `author_id`, `lang`, `category`, `created`, `date`, `last_edited`, `frontpage`, `featured`, `featured_date`, `moderation`, `visits`, `translator`, `translator_id`, `disabled`) VALUES
(1, 1, 1, 'Donec hendrerit euismod ultricies.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam non accumsan neque. Sed vel viverra justo, nec dapibus tellus. Vivamus eu iaculis diam, ut rutrum nisl. Quisque vulputate felis id mauris laoreet lacinia. Morbi ac magna lectus. Vestibulum ne', ' Donec hendrerit euismod ultricies. Sed eget ex eu tortor vestibulum commodo. Nullam tincidunt sit amet purus vestibulum consequat. In congue suscipit nibh, sed auctor sem fermentum quis. Etiam rutrum nisl nisl, eu blandit lorem interdum vel. Nunc placerat quam sapien, vel lacinia neque pretium at. Integer porttitor suscipit ligula. Proin vel ligula a nisl pulvinar condimentum non ut enim. Mauris molestie convallis sodales.\r\n\r\nAliquam sit amet nisi lectus. Mauris viverra eu libero sit amet mollis. Ut dictum scelerisque nunc, sed finibus magna dignissim vitae. Pellentesque nec placerat sem. Ut in mi eleifend purus fringilla pulvinar. Integer interdum purus ante, eu dictum arcu dapibus eget. In hac habitasse platea dictumst.\r\n\r\nInterdum et malesuada fames ac ante ipsum primis in faucibus. Nulla facilisi. Vivamus id sem nec augue dignissim tincidunt quis congue nisi. Integer rutrum sodales est ac aliquam. Nunc sodales maximus ultrices. Vestibulum vel luctus odio. Nullam in felis maximus, ultrices lorem vitae, tincidunt sem. Nam eu lectus dapibus, malesuada arcu vel, rutrum lacus.\r\n\r\nProin non accumsan augue, non laoreet arcu. Proin nec diam tellus. Donec ut congue lorem. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at venenatis ex, a posuere neque. Vivamus venenatis libero risus. Ut lobortis turpis in efficitur euismod. Mauris aliquam, ante eu accumsan gravida, velit ante euismod orci, quis sollicitudin lorem nunc ac erat. Sed efficitur egestas sollicitudin. Maecenas ac dolor eget sapien vulputate sodales a vel lorem. Vestibulum suscipit efficitur iaculis. Vivamus luctus massa eu ante euismod, consectetur faucibus magna faucibus. Aliquam fermentum eros id urna ultricies tristique. Nulla eget commodo lorem. Fusce commodo augue nec leo euismod ultricies at pulvinar diam. Nunc et nunc vestibulum, eleifend metus in, porttitor orci. ', NULL, 'admin', 1, 'es', 2, '2018-09-18 15:07:07', '2018-09-18 15:07:07', '2018-09-18 15:07:07', 0, 0, NULL, 0, 0, NULL, NULL, 0),
(2, 2, 1, 'Nulla facilisi. Proin ut risus imperdiet (En ingles)', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam non accumsan neque. Sed vel viverra justo, nec dapibus tellus. Vivamus eu iaculis diam, ut rutrum nisl. Quisque vulputate felis id mauris laoreet lacinia. Morbi ac magna lectus. Vestibulum ne', ' Donec hendrerit euismod ultricies. Sed eget ex eu tortor vestibulum commodo. Nullam tincidunt sit amet purus vestibulum consequat. In congue suscipit nibh, sed auctor sem fermentum quis. Etiam rutrum nisl nisl, eu blandit lorem interdum vel. Nunc placerat quam sapien, vel lacinia neque pretium at. Integer porttitor suscipit ligula. Proin vel ligula a nisl pulvinar condimentum non ut enim. Mauris molestie convallis sodales.\r\n\r\nAliquam sit amet nisi lectus. Mauris viverra eu libero sit amet mollis. Ut dictum scelerisque nunc, sed finibus magna dignissim vitae. Pellentesque nec placerat sem. Ut in mi eleifend purus fringilla pulvinar. Integer interdum purus ante, eu dictum arcu dapibus eget. In hac habitasse platea dictumst.\r\n\r\nInterdum et malesuada fames ac ante ipsum primis in faucibus. Nulla facilisi. Vivamus id sem nec augue dignissim tincidunt quis congue nisi. Integer rutrum sodales est ac aliquam. Nunc sodales maximus ultrices. Vestibulum vel luctus odio. Nullam in felis maximus, ultrices lorem vitae, tincidunt sem. Nam eu lectus dapibus, malesuada arcu vel, rutrum lacus.\r\n\r\nProin non accumsan augue, non laoreet arcu. Proin nec diam tellus. Donec ut congue lorem. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at venenatis ex, a posuere neque. Vivamus venenatis libero risus. Ut lobortis turpis in efficitur euismod. Mauris aliquam, ante eu accumsan gravida, velit ante euismod orci, quis sollicitudin lorem nunc ac erat. Sed efficitur egestas sollicitudin. Maecenas ac dolor eget sapien vulputate sodales a vel lorem. Vestibulum suscipit efficitur iaculis. Vivamus luctus massa eu ante euismod, consectetur faucibus magna faucibus. Aliquam fermentum eros id urna ultricies tristique. Nulla eget commodo lorem. Fusce commodo augue nec leo euismod ultricies at pulvinar diam. Nunc et nunc vestibulum, eleifend metus in, porttitor orci. ', NULL, 'admin', 1, 'en', 2, '2018-09-18 15:07:43', '2018-09-18 15:07:43', '2018-09-18 15:07:43', 0, 0, NULL, 0, 0, NULL, NULL, 0),
(3, 1, 1, 'Ut laoreet ut libero et bibendum. ', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut pellentesque, felis eu eleifend ultricies, arcu mauris iaculis ligula, at ultricies lectus purus quis dolor. Sed ut velit gravida, ullamcorper ligula id, porttitor dolor. Vestibulum tristique magn', '\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Ut pellentesque, felis eu eleifend ultricies, arcu mauris iaculis ligula, at ultricies lectus purus quis dolor. Sed ut velit gravida, ullamcorper ligula id, porttitor dolor. Vestibulum tristique magna libero, nec vehicula lorem hendrerit vel. Vivamus lobortis ligula ut tellus commodo vehicula. Suspendisse non pulvinar felis. Nam arcu odio, dapibus lacinia ipsum vitae, efficitur semper nisl. Phasellus sed massa lorem. Vestibulum viverra gravida dapibus. In id mi semper, pellentesque purus ut, accumsan leo. Etiam sed felis eget dui suscipit eleifend non in nulla. Integer congue eget dui ornare gravida. Ut ligula ante, ultrices eget massa in, ultrices porttitor turpis. Curabitur et volutpat est.\r\n\r\nUt laoreet ut libero et bibendum. Maecenas sed metus eu sem venenatis porttitor et ut odio. Etiam posuere vel tellus sit amet tincidunt. Cras egestas lorem eget ipsum commodo scelerisque. Suspendisse sit amet sapien orci. Praesent at nisi non justo tincidunt euismod vel tincidunt turpis. Aenean id tempus tellus. Nam condimentum felis non justo feugiat, vel faucibus nisl elementum. Aenean molestie tellus sit amet dolor fermentum, id semper velit faucibus. Fusce accumsan orci nec lacus venenatis, at ullamcorper orci ultrices. Cras dictum orci ex, sed sagittis turpis finibus ultricies. Mauris in libero consequat, rutrum tortor id, aliquam velit. Sed laoreet justo augue, suscipit rutrum est lobortis accumsan. Curabitur ut libero vestibulum, efficitur mauris vitae, malesuada urna.\r\n\r\nMauris eu purus et arcu tempor feugiat. Maecenas sit amet dolor sed sem consectetur bibendum ut quis turpis. Fusce malesuada sapien nisi, tristique commodo lorem vulputate vitae. Praesent molestie consectetur luctus. Fusce vel fermentum turpis. Aenean quis nisl vitae neque tristique feugiat at at mi. Aliquam erat volutpat. Proin quis lacus ultricies, malesuada dui a, feugiat augue.\r\n\r\nSed in odio mattis nulla molestie rutrum nec at nisl. Cras non malesuada leo. Vestibulum dui nulla, dapibus ut metus sed, congue luctus velit. Morbi a iaculis sapien. Fusce auctor iaculis facilisis. Donec ultricies ultricies purus, sit amet aliquam turpis ullamcorper et. Vestibulum nec lobortis eros. In consectetur pulvinar placerat. In ut est at sem congue ultricies. Donec pharetra libero in dui sodales, ut vehicula ante commodo. Proin vitae turpis et lorem dictum pharetra sit amet at dui.\r\n\r\nVestibulum maximus nulla ex, et aliquam nulla volutpat quis. Aenean eget mauris aliquam mauris tristique pharetra. Mauris et elementum ex, eu bibendum ligula. Integer ullamcorper nulla non tristique ultrices. Aenean malesuada scelerisque augue. Etiam ultrices fringilla consequat. Nunc non vehicula velit, vel semper est. In et augue id lectus elementum aliquet ut non diam. ', NULL, 'admin', 1, 'es', 3, '2018-09-18 15:11:05', '2018-09-18 15:11:05', '2018-09-18 15:11:05', 0, 0, NULL, 0, 0, NULL, NULL, 0),
(4, 1, 1, 'Faucibus in ornare quam viverra orci sagittis.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Tortor posuere ac ut consequat semper viverra. Odio facilisis mauris sit amet massa vitae tortor. Eu sem integer vitae justo eget ma', 'Faucibus in ornare quam viverra orci sagittis. Id cursus metus aliquam eleifend mi in nulla. A diam maecenas sed enim. Diam sit amet nisl suscipit adipiscing bibendum est ultricies. Sed enim ut sem viverra aliquet eget sit. Bibendum enim facilisis gravida neque convallis a. Maecenas volutpat blandit aliquam etiam erat. Pellentesque elit ullamcorper dignissim cras tincidunt lobortis feugiat. Vitae congue mauris rhoncus aenean vel elit scelerisque. Ac auctor augue mauris augue neque gravida. Pellentesque id nibh tortor id aliquet. Quis eleifend quam adipiscing vitae proin. Sit amet aliquam id diam maecenas. Malesuada bibendum arcu vitae elementum. Nisi est sit amet facilisis magna etiam tempor orci eu.\r\n\r\nArcu risus quis varius quam. Aenean et tortor at risus viverra adipiscing at in tellus. Non enim praesent elementum facilisis leo vel fringilla est. Ultricies mi eget mauris pharetra et. Diam vel quam elementum pulvinar etiam. Malesuada fames ac turpis egestas. Dolor sit amet consectetur adipiscing. Eget nunc lobortis mattis aliquam faucibus purus in massa. Mollis nunc sed id semper risus in hendrerit. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit. In ante metus dictum at tempor commodo ullamcorper a lacus. Pellentesque eu tincidunt tortor aliquam nulla facilisi cras fermentum odio. Nisl pretium fusce id velit ut tortor. Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Penatibus et magnis dis parturient montes nascetur ridiculus mus. Fringilla est ullamcorper eget nulla facilisi etiam. Bibendum enim facilisis gravida neque convallis a.\r\n\r\nLacinia at quis risus sed vulputate. Eget nulla facilisi etiam dignissim diam quis enim lobortis scelerisque. Amet facilisis magna etiam tempor orci. Faucibus a pellentesque sit amet porttitor. Amet nisl purus in mollis nunc sed id. Laoreet sit amet cursus sit amet dictum sit amet justo. Tristique magna sit amet purus gravida. Porttitor lacus luctus accumsan tortor posuere ac ut. Libero justo laoreet sit amet cursus sit. Ut consequat semper viverra nam libero justo.\r\n\r\nAliquet nec ullamcorper sit amet risus. Mattis nunc sed blandit libero volutpat sed cras ornare. Ullamcorper dignissim cras tincidunt lobortis feugiat. Pellentesque diam volutpat commodo sed egestas. Tristique senectus et netus et. Elit sed vulputate mi sit amet mauris. Venenatis cras sed felis eget velit aliquet sagittis id. Viverra adipiscing at in tellus integer feugiat. Arcu odio ut sem nulla pharetra diam sit. Sit amet volutpat consequat mauris nunc congue. Quis vel eros donec ac odio tempor. Turpis egestas maecenas pharetra convallis posuere morbi leo urna molestie. Nec tincidunt praesent semper feugiat nibh sed. Semper quis lectus nulla at volutpat diam ut venenatis. Ipsum suspendisse ultrices gravida dictum fusce ut placerat. In vitae turpis massa sed elementum tempus egestas. Facilisi nullam vehicula ipsum a arcu. Purus in massa tempor nec feugiat nisl.', NULL, 'admin', 1, 'es', 6, '2018-09-18 15:12:43', '2018-09-18 15:12:43', '2018-09-18 15:12:43', 0, 0, NULL, 0, 0, NULL, NULL, 0),
(5, 1, 1, 'Et magnis dis parturient montes nascetur', 'Ornare massa eget egestas purus viverra accumsan in. Metus vulputate eu scelerisque felis imperdiet proin. Dui accumsan sit amet nulla facilisi morbi tempus iaculis. Viverra orci sagittis eu volutpat odio facilisis mauris sit. Arcu vitae elementum curabitu', 'Et magnis dis parturient montes nascetur ridiculus mus mauris. Penatibus et magnis dis parturient montes nascetur ridiculus mus. Mattis molestie a iaculis at erat pellentesque adipiscing commodo elit. Ut aliquam purus sit amet luctus venenatis lectus magna fringilla. Ipsum suspendisse ultrices gravida dictum fusce ut placerat orci nulla. Eget est lorem ipsum dolor. Gravida dictum fusce ut placerat orci nulla. At in tellus integer feugiat scelerisque varius morbi enim nunc. Etiam tempor orci eu lobortis elementum nibh tellus molestie. Iaculis nunc sed augue lacus viverra vitae congue.\r\n\r\nOrnare massa eget egestas purus viverra accumsan in. Metus vulputate eu scelerisque felis imperdiet proin. Dui accumsan sit amet nulla facilisi morbi tempus iaculis. Viverra orci sagittis eu volutpat odio facilisis mauris sit. Arcu vitae elementum curabitur vitae nunc sed velit. Adipiscing enim eu turpis egestas pretium aenean. Amet purus gravida quis blandit. Dui vivamus arcu felis bibendum ut tristique. Elit at imperdiet dui accumsan. Est velit egestas dui id ornare arcu odio ut sem. Cursus eget nunc scelerisque viverra mauris in. Orci nulla pellentesque dignissim enim sit amet venenatis urna cursus. Placerat vestibulum lectus mauris ultrices eros in cursus turpis massa. Morbi tincidunt ornare massa ege', NULL, 'admin', 1, 'es', 4, '2018-09-18 15:13:24', '2018-09-18 15:13:24', '2018-09-18 15:13:24', 0, 0, NULL, 0, 0, NULL, NULL, 0);
";

$news_database_inserts = [
    /* CONFIG */
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_list_moderation_limits', '200');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_submit_main_cats', '0');",
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
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_moderation', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'allow_multiple_pages', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_stats', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_adv_stats', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_meta_opengraph', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_anon_translate', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_submit_anon', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_submit_users', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_users_edit_own_news', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_allow_users_delete_own_news', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_view_anon', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_view_user', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_breadcrum', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_breadcrum_separator', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_translate', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_users_translate', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_translate_own_news', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_pager_max', '8');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_side_news', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_dflt_getnews_limit', '10');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_section_sections', '3');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('News', 'news_section_getnews_limit', '10');"
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
