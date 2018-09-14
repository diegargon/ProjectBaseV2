<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function MiniEditor_init() {
}

function MiniEditor_install() {
    global $db;
    require_once "db/MiniEditor.db.php";
    foreach ($minieditor_database_install as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function MiniEditor_preInstall() {
    return true;
}

function MiniEditor_preInstall_info() {
    return true;
}

function MiniEditor_upgrade($version, $from_version) {
    
}

function MiniEditor_uninstall() {
    global $db;
    require_once "db/MiniEditor.db.php";
    foreach ($minieditor_database_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
