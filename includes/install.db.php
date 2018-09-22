<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$core_database = [];

/*
 * CONFIG
 */

/*
  $core_database[] = "
  DROP TABLE IF EXISTS `config`;
  ";
 */

//CONFIG
$core_database[] = "
CREATE TABLE `" . DB_PREFIX . "config` (
  `cfg_id` int(10) UNSIGNED NOT NULL,
  `cfg_key` char(255) NOT NULL,
  `plugin` char (255) NOT NULL,
  `cfg_value` char(255) NOT NULL,
  `group` char(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";

";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "config`
  ADD PRIMARY KEY (`cfg_id`),
  ADD UNIQUE KEY `cfg_id` (`cfg_id`),
  ADD UNIQUE KEY `cfg_key` (`cfg_key`);
";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "config`
  MODIFY `cfg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
";


//LINKS
$core_database[] = "
CREATE TABLE `pb_links` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `plugin` char(255) NOT NULL,
  `source_id` int(10) UNSIGNED NOT NULL,
  `type` char(64) NOT NULL,
  `link` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";  
";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "links`
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `link_id` (`link_id`);
";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "links`
  MODIFY `link_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
";

$core_inserts = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'plugins_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_INSTALLED', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_VERSION', '" . CORE_VERSION . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_URL', '" . $cfg['WEB_URL'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_LANG', '" . $cfg['WEB_LANG'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_LOGO', '" . $cfg['WEB_LOGO'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'STATIC_SRV_URL', '" . $cfg['STATIC_SRV_URL'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_TIMEZONE', 'UTC');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_DATEFORMAT', 'd/m/y H:i');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_DB_DATEFORMAT', 'Y-m-d H:i:s');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'FRIENDLY_URL', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WELCOME_BOTS', 'Google|MSN|Yahoo|Lycos|Bing|twitter|Facebook');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CONFIG_KEY_MAX', '32');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CONFIG_VALUE_MAX', '128');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_NAME', 'My Web Name');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_DESC', 'Ny Web Name Description');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_PATH', '" . ABSPATH . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'FOOT_COPYRIGHT', 'Copyright &copy; 2016 - 2018 Diego García All Rights Reserved');",
    //"INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', '', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'BAD_BOTS', 'ia_archiver|Altavista|eStyle|MJ12bot|ips-agent|Yandex|Semrush|Baidu|Sogou|Pcore');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_DIR', 'ltr');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'PAGE_VIEWPORT', 'width=device-width,minimum-scale=1,initial-scale=1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CHARSET', 'UTF-8');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CON_FILE', 'index.php');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'user_name_regex', '/^[a-zA-Z/'/-\040]+$/');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'accepted_media_regex', 'jpe?g|bmp|png|JPE?G|BMP|PNG|gif');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'remote_checks', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'max_int', '4294967295');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'link_min_length', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'link_max_length', '255');"
];


$core_database = array_merge($core_database, $core_inserts);

/*
 * 
 * PLUGINS
 * 
 */

$core_database[] = "
    
CREATE TABLE `" . DB_PREFIX . "plugins` (
  `plugin_id` int(10) UNSIGNED NOT NULL,
  `plugin_name` char(255) NOT NULL,
  `version` float(4,2) NOT NULL,
  `core` tinyint (1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `autostart` tinyint(1) NOT NULL DEFAULT '0',
  `installed` tinyint(1) NOT NULL DEFAULT '0',
  `missing` tinyint(1) NOT NULL DEFAULT '0',
  `upgrade_from` float(4,2) NOT NULL DEFAULT '0',
  `main_file` char(255) NOT NULL,
  `function_init` char(255) NOT NULL,
  `function_admin_init` char(255) NOT NULL,
  `function_install` char(255) NOT NULL,
  `function_pre_install` char(255) NOT NULL,
  `function_pre_install_info` char(255) DEFAULT NULL,
  `function_upgrade` char(255) NOT NULL,
  `function_uninstall` char(255) NOT NULL,
  `provide` char(255) NOT NULL,
  `depends` varchar(1020) DEFAULT NULL,
  `priority` tinyint(1) NOT NULL,
  `optional` varchar(1020) DEFAULT NULL,
  `conflicts` varchar(1020) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";    

";

$core_database[] = "
    ALTER TABLE `" . DB_PREFIX . "plugins` ADD PRIMARY KEY(`plugin_id`);
";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "plugins` ADD UNIQUE(`plugin_id`);
";

$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "plugins` ADD UNIQUE(`plugin_name`);
";


$core_database[] = "
ALTER TABLE `" . DB_PREFIX . "plugins`
  MODIFY `plugin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
";
