<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function StdComments_init() {
    global $tpl, $cfg;
    
    if ($cfg['stdcomments_disable_by_stress'] && is_server_stressed()) {
        return false;
    }
    
    $tpl->getCSS_filePath('StdComments');
}

function StdComments_install() {
    global $db;
    require_once ('db/StdComments.db.php');
    foreach ($stdComments_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function StdComments_preInstall() {
    return true;
}

function StdComments_preInstall_info() {
    return true;
}

function StdComments_upgrade($version, $from_version) {
    global $db;
    require_once ('db/StdComments.db.php');
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($stdComments_database_upgrade_002_to_003 as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function StdComments_uninstall() {
    global $db;
    require_once ('db/StdComments.db.php');
    foreach ($stdComments_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
