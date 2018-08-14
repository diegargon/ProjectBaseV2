<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Multilang_init() {
    global $cfg, $ml, $debug;

    defined('DEBUG') && $cfg['multilang_debug'] ? $debug->log("Multilang Inititated", "MULTILANG", "DEBUG") : null;

    $ml = new Multilang($cfg);
}

function Multilang_install() {
    global $db;
    require_once "db/Multilang.db.php";
    foreach ($multilang_database as $query) {
        $db->query($query);
    }
    return true;
}

function Multilang_uninstall() {
    global $db;
    require_once "db/Multilang.db.php";
    foreach ($multilang_database_uninstall as $query) {
        $db->query($query);
    }
    return true;
}
