<?php

/**
 *  StdComments - Main file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage StdComments
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global tpl $tpl
 * @global array $cfg
 * @return boolean
 */
function StdComments_init() {
    global $tpl;

    $tpl->getCssFile('StdComments');

    return true;
}

/**
 * Install
 * @global db $db
 * @return boolean
 */
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

/**
 * preInstall
 * 
 * @return boolean
 */
function StdComments_preInstall() {
    return true;
}

/**
 * preInstall_info
 * 
 * @return boolean
 */
function StdComments_preInstall_info() {
    return true;
}

/**
 * Upgrade
 * @global db $db
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
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

/**
 * Uninstall
 * @global db $db
 * @return boolean
 */
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
