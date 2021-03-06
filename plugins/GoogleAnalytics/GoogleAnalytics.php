<?php

/**
 *  GoogleAnalytics main
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage GoogleAnalytics
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function GoogleAnalytics_init() {
    global $tpl;

    $tpl->setPrefetchURL('https://www.google-analytics.com');
    $tpl->addtoTplVar('SCRIPTS_BOTTOM', $tpl->getTplFile('GoogleAnalytics'));

    return true;
}

function GoogleAnalytics_install() {
    global $db;

    require_once ('db/GoogleAnalytics.db.php');
    foreach ($google_analytics_db_install as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}

function GoogleAnalytics_uninstall() {
    global $db;

    require_once ('db/GoogleAnalytics.db.php');
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
