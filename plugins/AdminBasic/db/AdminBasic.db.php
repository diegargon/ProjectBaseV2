<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$adminbasic_database = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('AdminBasic', 'adminbasic_debug', '1');"
];

$adminbasic_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'SMBasic'"
];
