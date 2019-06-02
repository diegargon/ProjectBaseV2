<?php

/**
 *  Blocks main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Blocks
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global Blocks $blocks
 */
function Blocks_init() {
    global $blocks;

    !defined('BLOCKS') ? $blocks = new Blocks : false;

    define('BLOCKS', true);
}

/**
 * Install
 * 
 * @global db $db
 * @return boolean
 */
function Blocks_install() {
    global $db;
    require_once ('db/Blocks.db.php');
    foreach ($blocks_database_install as $query) {
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
function Blocks_preInstall() {
    return true;
}

/**
 * preInstall info
 * 
 * @return boolean
 */
function Blocks_preInstall_info() {
    return true;
}

/**
 * Upgrade
 * @param float $version
 * @param float $from_version
 * @return boolean
 */
function Blocks_upgrade($version, $from_version) {
    return true;
}

/**
 * Uninstall
 * 
 * @global db $db
 * @return boolean
 */
function Blocks_uninstall() {
    global $db;
    require_once ('db/Blocks.db.php');
    foreach ($blocks_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
