<?php

/**
 *  SimpleACL - Init file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleACL
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_init() {
    define('ACL', TRUE);
    global $acl_auth;

    empty($acl_auth) ? $acl_auth = new ACL : false;
}

function SimpleACL_install() {
    global $db;

    require_once ('db/SimpleACL.db.php');
    foreach ($simpleacl_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function SimpleACL_preInstall() {
    
}

function SimpleACL_preInstall_info() {
    return true;
}

function SimpleACL_upgrade($version, $from_version) {
    return true;
}

function SimpleACL_uninstall() {
    global $db;

    $db->silent(true);
    require_once ('db/SimpleACL.db.php');
    foreach ($simpleacl_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
