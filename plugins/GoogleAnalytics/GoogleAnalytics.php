<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function GoogleAnalytics_init() {
    global $tpl;

    $tpl->addto_tplvar("SCRIPTS_BOTTOM", $tpl->getTPL_file("GoogleAnalytics"));
}

function GoogleAnalytics_install() {
    global $db;

    require_once "db/GoogleAnalytics.db.php";
    foreach ($google_analytics_db_install as $query) {
        $r = $db->query($query);
    }

    return ($r) ? true : false;
}

function GoogleAnalytics_uninstall() {
    global $db;

    require_once "db/GoogleAnalytics.db.php";
    foreach ($google_analytics_db_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function GoogleAnalytics_preInstall() {
    return true;
}

function GoogleAnalytics_admin_init() {
    return true;
}

function GoogleAnalytics_preInstallInfo() {
    return true;
}

function GoogleAnalytics_upgrade($version, $from_version) {
    return true;
}