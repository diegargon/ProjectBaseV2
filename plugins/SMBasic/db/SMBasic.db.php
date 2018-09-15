<?php

/*
 *  Copyright @ 20016 - 2018 Diego Garcia
 * 
 *  default_session -> user php build in or not
 *  session_start -> start session_start, ignore in default session (start yes)
 *  check_user_agent -> give problems on movile device TODO
 * 
 */
!defined('IN_WEB') ? exit : true;

/* SESSIONS */
$smbasic_database [] = "
CREATE TABLE `" . DB_PREFIX . "sessions` (
  `session_id` varchar(64) NOT NULL,
  `session_uid` int(11) NOT NULL,
  `session_data` varchar(512) DEFAULT NULL,
  `session_ip` varchar(15) NOT NULL,
  `session_browser` text NOT NULL,
  `session_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_expire` int(11) NOT NULL,
  `last_login` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
        
";

$smbasic_database[] = "
ALTER TABLE `" . DB_PREFIX . "sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `session_id` (`session_id`);
  
";

/* USER */

$smbasic_database[] = "
CREATE TABLE `" . DB_PREFIX . "users` (
  `uid` int(16) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) NOT NULL,
  `regdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` int(12) NOT NULL DEFAULT '0',
  `disable` tinyint(4) NOT NULL DEFAULT '0',
  `isFounder` tinyint(1) NOT NULL DEFAULT '0',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `tos` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$smbasic_database[] = "
ALTER TABLE `" . DB_PREFIX . "users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `email` (`email`);
";

$smbasic_database[] = "
ALTER TABLE `" . DB_PREFIX . "users`
  MODIFY `uid` int(32) NOT NULL AUTO_INCREMENT;
";

/* INSERTS */

$smbasic_insert_database = [
    /* CONFIG */
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_default_session', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_session_start', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_persistence', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_max_email', '60');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_max_username', '32');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_min_username', '4');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_use_salt', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_pw_salt', '5565');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_session_expire', '86400');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_cookie_prefix', 'default_');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_check_user_agent', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_check_ip', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_need_username', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_email_confirmation', '1');",
    /* PROFILE CONFIG */
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_can_change_username', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_can_change_email', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_dflt_avatar_img', 'plugins/SMBasic/tpl/img/avatar.png');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_https_remove_avatar', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_session_salt', 'y1!^!ob32a.,$!!$3]Q&%@/^^i@?Xx]')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_cookie_expire', '86400')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_default_img_avatar', '')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_https_remote_avatar', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_ask_terms', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_register_enable', '1')",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_login_enable', '1')",
    /* REGISTER */
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_max_password', '32');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_min_password', '8');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', 'smbasic_register_reply_email', 'noreply@envigo.net');",
    // "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', '', '')",
    // "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('SMBasic', '', '')",
    /* DEFAULT ADMIN USER , PASSWORD adminadmin */
    "INSERT INTO `" . DB_PREFIX . "users` (`username`, `password`, `email`, `isFounder`, `isAdmin` `active`) VALUES ('admin', 'cfecaf3d2dd296bf3accb3d2a62346d37afc99e7f8df52fdbc9de7ec1b33451efbfde53ba6c69cbb657ef9a7d9498ceb9e67fd64820a8b6c05ee671d53f28d1e', 'diego@envigo.net', '1', '1', '1')"
];

$smbasic_database = array_merge($smbasic_database, $smbasic_insert_database);

/*
 * 
 * UNINSTALL
 * 
 */

$smbasic_uninstall_database = [
    "DROP TABLE `" . DB_PREFIX . "sessions`",
    "DROP TABLE `" . DB_PREFIX . "users`",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SMBasic'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'SMBasic'"
];
