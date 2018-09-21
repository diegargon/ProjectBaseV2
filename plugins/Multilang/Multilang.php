<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Multilang_init() {
    global $ml;

    if (!defined('MULTILANG')) {
        define('MULTILANG', TRUE);
        $ml = new Multilang();
    }
}

function Multilang_install() {
    global $db;
    require_once "db/Multilang.db.php";
    foreach ($multilang_database as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function Multilang_uninstall() {
    global $db;
    require_once "db/Multilang.db.php";
    foreach ($multilang_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
