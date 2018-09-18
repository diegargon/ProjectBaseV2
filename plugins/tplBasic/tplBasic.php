<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 *  
 *  Not really a template engine, just a logic/design separator
 *
 */
!defined('IN_WEB') ? exit : true;

function tplBasic_init() {
    global $tpl, $cfg;

    define('TPL', TRUE);

    if (defined('SQL')) {
        global $db;
        $tpl = new TPL($cfg, $db);
    } else {
        $tpl = new TPL($cfg);
    }
}

function tplBasic_Install() {
    global $db;
    require_once "db/tplBasic.db.php";
    foreach ($tplbasic_database as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}
