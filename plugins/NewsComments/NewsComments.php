<?php

/**
 *  Newscomments - main file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage Newscomments
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function NewsComments_init() {
    global $cfg;

    if ($cfg['nc_disable_by_stress'] && is_server_stressed()) {
        return false;
    }
    register_action('news_show_page', 'News_Comments');
    
    return true;
}

function NewsComments_install() {
    global $db;

    require_once ('db/NewsComments.db.php');
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

    require_once ('db/NewsComments.db.php');
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

    require_once ('db/NewsComments.db.php');
    foreach ($newsComments_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
