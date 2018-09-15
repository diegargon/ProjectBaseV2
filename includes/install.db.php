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
  `cfg_id` int(32) NOT NULL,
  `cfg_key` varchar(32) NOT NULL,
  `plugin` varchar (64) NOT NULL,
  `cfg_value` varchar(128) NOT NULL,
  `group` varchar(64) NULL,
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
  MODIFY `cfg_id` int(32) NOT NULL AUTO_INCREMENT;
";

//LINKS
$core_database[] = "
CREATE TABLE `pb_links` (
  `link_id` int(11) NOT NULL,
  `plugin` varchar(32) NOT NULL,
  `source_id` int(11) NOT NULL,
  `type` varchar(11) NOT NULL,
  `link` varchar(256) NOT NULL,
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
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT;
";

$core_inserts = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'plugins_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_INSTALLED', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_VERSION', '" . CORE_VERSION . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_URL', '" . $cfg['WEB_URL'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_LANG', '" . $cfg['WEB_LANG'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'STATIC_SRV_URL', '" . $cfg['STATIC_SRV_URL'] . "');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_TIMEZONE', 'UTC');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_DATEFORMAT', 'd/m/y H:i');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'DEFAULT_DB_DATEFORMAT', 'Y-m-d H:i:s');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'FRIENDLY_URL', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WELCOME_BOTS', 'Google|MSN|Yahoo|Lycos|Bing|twitter|Facebook');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CONFIG_KEY_MAX', '32');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CONFIG_VALUE_MAX', '128');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_NAME', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_DESC', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CORE_PATH', '/var/www/html');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'FOOT_COPYRIGHT', 'Copyright &copy; 2016 - 2018 Diego García All Rights Reserved');",
    //"INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', '', '');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'BAD_BOTS', 'ia_archiver|Altavista|eStyle|MJ12bot|ips-agent|Yandex|Semrush|Baidu|Sogou|Pcore');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_DIR', 'ltr');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'WEB_LANG', 'en');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'PAGE_VIEWPORT', 'width=device-width,minimum-scale=1,initial-scale=1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CHARSET', 'UTF-8');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'CON_FILE', 'index.php');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('CORE', 'remote_checks', '1');"
];


$core_database = array_merge($core_database, $core_inserts);

/*
 * 
 * PLUGINS
 * 
 */

$core_database[] = "
    
CREATE TABLE `" . DB_PREFIX . "plugins` (
  `plugin_id` int(12) NOT NULL,
  `plugin_name` varchar(32) NOT NULL,
  `version` float(4,2) NOT NULL,
  `core` int (1) NOT NULL DEFAULT '0',
  `enabled` int(1) NOT NULL DEFAULT '0',
  `autostart` int(1) NOT NULL DEFAULT '0',
  `installed` int(1) NOT NULL DEFAULT '0',
  `missing` int(1) NOT NULL DEFAULT '0',
  `upgrade_from` float(4,2) NOT NULL DEFAULT '0',
  `main_file` varchar(32) NOT NULL,
  `function_init` varchar(32) NOT NULL,
  `function_admin_init` varchar(32) NOT NULL,
  `function_install` varchar(32) NOT NULL,
  `function_pre_install` varchar(32) NOT NULL,
  `function_pre_install_info` varchar(32) DEFAULT NULL,
  `function_upgrade` varchar(32) NOT NULL,
  `function_uninstall` varchar(32) NOT NULL,
  `provide` varchar(32) NOT NULL,
  `depends` varchar(1024) DEFAULT NULL,
  `priority` int(2) NOT NULL,
  `optional` varchar(1024) DEFAULT NULL,
  `conflicts` varchar(1024) DEFAULT NULL
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
  MODIFY `plugin_id` int(12) NOT NULL AUTO_INCREMENT;
";
