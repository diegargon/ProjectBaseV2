<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_init() {
    define('ACL', TRUE);
    global $acl_auth;

    empty($acl_auth) ? $acl_auth = new ACL : false;
}

function SimpleACL_install() {
    global $db;

    require_once "db/SimpleACL.db.php";
    foreach ($simpleacl_database_install as $query) {
        $r = $db->query($query);
    }

    return ($r) ? true : false;
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
    require_once "db/SimpleACL.db.php";
    foreach ($simpleacl_database_uninstall as $query) {
        $r = $db->query($query);
    }
    $db->silent(false);

    return ($r) ? true : false;
}
