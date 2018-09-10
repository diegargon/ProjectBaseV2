<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Blocks_init() {
    global $blocks;

    empty($blocks) ? $blocks = new Blocks : false;
}

function Blocks_install() {
    global $db;
    require_once "db/Blocks.db.php";
    foreach ($blocks_database_install as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function Blocks_preInstall() {
    return true;
}

function Blocks_preInstall_info() {
    return true;
}

function Blocks_upgrade($version, $from_version) {
    return true;
}

function Blocks_uninstall() {
    global $db;
    require_once "db/Blocks.db.php";
    foreach ($blocks_database_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
