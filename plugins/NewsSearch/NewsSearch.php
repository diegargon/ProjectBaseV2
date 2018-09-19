<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsSearch_init() {
    define('NEWS_SEARCH', true);
}

function NewsSearch_install() {
    global $db;
    require_once "db/NewsSearch.db.php";
    foreach ($newsSearch_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function NewsSearch_preInstall() {
    return true;
}

function NewsSearch_preInstall_info() {
    return true;
}

function NewsSearch_upgrade($version, $from_version) {
    global $db;
    require_once "db/NewsSearch.db.php";
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($newsSearch_database_upgrade_002_to_003 as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function NewsSearch_uninstall() {
    global $db;
    require_once "db/NewsSearch.db.php";
    foreach ($newsSearch_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
