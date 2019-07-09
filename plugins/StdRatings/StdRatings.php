<?php

/**
 *  StdRatings - Init file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage StdRatings
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 */
function StdRatings_init() {
    return true;    
}

/**
 * Install function
 * 
 * @global db $db
 * @return boolean
 */
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

/**
 * preInstall function
 * 
 * @return boolean
 */
function StdRatings_preInstall() {
    return true;
}

/**
 * preInstall info
 * 
 * @return boolean
 */
function StdRatings_preInstall_info() {
    return true;
}

/**
 * Upgrade function 
 * @global db $db
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
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

/**
 * Uninstall function
 * 
 * @global db $db
 * @return boolean
 */
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
