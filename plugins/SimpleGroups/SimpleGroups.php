<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleGroups_init() {
    global $groups;

    !isset($groups) ? $groups = new Groups() : false;
}

function SimpleGroups_AdminInit() {
    
}

function SimpleGroups_install() {
    global $db;
    require_once "db/SimpleGroups.db.php";
    foreach ($simplegroups_database_install as $query) {
        $r = $db->query($query);
    }
    //admin
    if ($r) {
        $r = $db->query($simplegroups_database_install_insert_admin_group);
    }
    $admin_grp_id = $db->insert_id();

    if ($r) {
        $db->query($simplegroups_database_install_insert_groups);
    }
    if ($r) {
        $db->update("users", ['groups' => $admin_grp_id], ['isAdmin' => 1]);
    }
    return ($r) ? true : false;
}

function SimpleGroups_preInstall() {
    return true;
}

function SimpleGroups_preInstall_info() {
    return true;
}

function SimpleGroups_upgrade($version, $from_version) {
    return true;
}

function SimpleGroups_uninstall() {
    global $db;
    require_once "db/SimpleGroups.db.php";
    foreach ($simplegroups_database_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
