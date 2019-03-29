<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function GoogleAnalytics_init() {
    global $tpl;

    $tpl->addtoTplVar("SCRIPTS_BOTTOM", $tpl->getTplFile("GoogleAnalytics"));
}

function GoogleAnalytics_install() {
    global $db;

    require_once "db/GoogleAnalytics.db.php";
    foreach ($google_analytics_db_install as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}

function GoogleAnalytics_uninstall() {
    global $db;

    require_once "db/GoogleAnalytics.db.php";
    foreach ($google_analytics_db_uninstall as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
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
