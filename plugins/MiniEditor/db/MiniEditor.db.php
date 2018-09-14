<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$minieditor_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('MiniEditor', 'minieditor_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('MiniEditor', 'minieditor_parser_allow_ext_img', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('MiniEditor', 'minieditor_debug', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('MiniEditor', 'minieditor_min_length', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('MiniEditor', 'minieditor_max_length', '10000');",
];
/* UNINSTALL */

$minieditor_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'MiniEditor'";
$minieditor_database_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin_name = 'MiniEditor'";