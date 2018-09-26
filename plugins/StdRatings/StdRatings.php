<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function StdRatings_init() {
    
}

function StdRatings_install() {
    global $db;
    require_once ('db/StdRatings.db.php');
    foreach ($StdRatings_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function StdRatings_preInstall() {
    return true;
}

function StdRatings_preInstall_info() {
    return true;
}

function StdRatings_upgrade($version, $from_version) {
    global $db;
    require_once ('db/StdRatings.db.php');
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($StdRatings_database_upgrade_002_to_003 as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function StdRatings_uninstall() {
    global $db;
    require_once ('db/StdRatings.db.php');
    foreach ($StdRatings_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}