<?php

/**
 *  MiniEditor main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * ME init func
 */
function MiniEditor_init() {
    
}

/**
 * Me install func
 * @global Database $db
 * @return boolean
 */
function MiniEditor_install() {
    global $db;
    require_once "db/MiniEditor.db.php";
    foreach ($minieditor_database_install as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}

/**
 * ME preInstall
 * @return boolean
 */
function MiniEditor_preInstall() {
    return true;
}

/**
 * ME preInstall info
 * @return boolean
 */
function MiniEditor_preInstall_info() {
    return true;
}

/**
 * Me upgrade func
 * @param float $version
 * @param float $from_version
 */
function MiniEditor_upgrade($version, $from_version) {
    
}

/**
 * ME uninstall func
 * @global Database $db
 * @return boolean
 */
function MiniEditor_uninstall() {
    global $db;
    require_once "db/MiniEditor.db.php";
    foreach ($minieditor_database_uninstall as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}
