<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 *  TODO Añadir en hazmin el id y poder establecer el padre
 * 
 */

!defined('IN_WEB') ? exit : true;

function SimpleGroups_init() {
    global $groups;

    !isset($groups) ? $groups = new Groups() : false;
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
    } else {
        return false;
    }
    $admin_grp_id = $db->insert_id();

    if ($r) {
        $r = $db->query($simplegroups_database_install_insert_admin_limited_group);
    } else {
        return false;
    }
    $admin_limited_id = $db->insert_id();

    if ($r) {
        $r = $db->query($simplegroups_database_install_insert_registered_group);
    } else {
        return false;
    }
    $registered_grp_id = $db->insert_id();

    if ($r) {
        $r = $db->query($simplegroups_database_install_anon_group);
    } else {
        return false;
    }
    $anon_grp_id = $db->insert_id();

    if ($r) {
        $db->update("groups", ['group_father' => $admin_grp_id], ['group_id' => $admin_limited_id]);
        $db->update("groups", ['group_father' => $admin_limited_id], ['group_id' => $registered_grp_id]);
        $db->update("groups", ['group_father' => $registered_grp_id], ['group_id' => $anon_grp_id]);
        $db->update("users", ['groups' => $admin_grp_id . "," . $registered_grp_id], ['isFounder' => 1]);
        $db->update("users", ['groups' => $registered_grp_id], ['isFounder' != 1]);
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
