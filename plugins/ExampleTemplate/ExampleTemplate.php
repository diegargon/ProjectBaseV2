<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function ExampleTemplate_init() {
    global $example_template;
    !isset($example_template) ? $example_template = new ExampleTemplate(): null;
    
}

function ExampleTemplate_install() {
    global $db;
    require_once "db/ExampleTemplate.db.php";
    foreach ($exampleTemplate_database as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function ExampleTemplate_preInstall() {
    return true;
}

function ExampleTemplate_preInstall_info() {
    return true;
}

function ExampleTemplate_upgrade($version, $from_version) {
    global $db;
    require_once "db/ExampleTemplate.db.php";
    if ($version == 0.3 && $from_version == 0.2) {
        foreach ($exampleTemplate_database_upgrade_002_to_003 as $query) {
            $r = $db->query($query);
        }
        return ($r) ? true : false;
    }
    return false;
}

function ExampleTemplate_uninstall() {
    global $db;
    require_once "db/ExampleTemplate.db.php";
    foreach ($exampleTemplate_database_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
