<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsComments_init() {
    //register_action("news_show_page", "News_Comments");
    //register_action("Newspage_get_comments", "News_Comment_Details");
}

function NewsComments_install() {
    global $db;
    require_once "db/NewsComments.db.php";
    foreach ($newsComments_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function NewsComments_preInstall() {
    return true;
}

function NewsComments_preInstall_info() {
    return true;
}

function NewsComments_upgrade($version, $from_version) {
    global $db;
    require_once "db/NewsComments.db.php";
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($newsComments_database_upgrade_002_to_003 as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function NewsComments_uninstall() {
    global $db;
    require_once "db/NewsComments.db.php";
    foreach ($newsComments_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
