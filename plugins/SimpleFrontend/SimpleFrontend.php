<?php

/**
 *  SimpleFrontend
 * 
 *  responsible for displaying the content
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 * 
 * @global SimpleFrontend $frontend
 */
function SimpleFrontend_init() {
    global $frontend;

    !defined('FRONTEND') ? $frontend = new SimpleFrontend() : null;
    define('FRONTEND', true);
}

/**
 * Install function
 * 
 * @global db $db
 * @global array $cfg
 * @return boolean
 */
function SimpleFrontend_install() {
    global $db, $cfg;

    require_once ('db/SimpleFrontend.db.php'); // need $cfg
    foreach ($simplefrontend_db_install as $query) {
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
function SimpleFrontend_preInstall() {
    return true;
}

/**
 * preInstall info function
 * 
 * @return boolean
 */
function SimpleFrontend_preInstall_info() {
    return true;
}

/**
 * Upgrade function
 * 
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
function SimpleFrontend_upgrade($version, $from_version) {
    return true;
    /*
      global $db;
      require_once "db/SimpleFrontend.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($exampleTemplate_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : false;
      }
      return false;
     * 
     */
}

/**
 * Uninstall function
 * 
 * @global db $db
 * @global array $cfg
 * @return boolean
 */
function SimpleFrontend_uninstall() {
    global $db, $cfg;
    require_once ('db/SimpleFrontend.db.php'); //need $cfg
    foreach ($simplefrontend_db_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
