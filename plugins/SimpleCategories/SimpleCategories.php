<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function SimpleCats_init() {
    global $ctgs;

    define('CATS', TRUE);

    !isset($ctgs) ? $ctgs = new Categories() : null;
}

function SimpleCats_install() {
    global $db;

    require_once ('db/SimpleCats.db.php');
    foreach ($simplecats_db_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function SimpleCats_preInstall() {
    
}

function SimpleCats_preInstall_info() {
    return true;
}

function SimpleCats_upgrade($version, $from_version) {
    return true;
}

function SimpleCats_uninstall() {
    global $db;

    $db->silent(true);
    require_once ('db/SimpleCats.db.php');
    foreach ($simplecats_db_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
