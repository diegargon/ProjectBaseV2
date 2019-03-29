<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function MiniEditor_init() {
    
}

function MiniEditor_install() {
    global $db;
    require_once "db/MiniEditor.db.php";
    foreach ($minieditor_database_install as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
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
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}
