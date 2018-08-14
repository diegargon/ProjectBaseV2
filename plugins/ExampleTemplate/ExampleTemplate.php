<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function ExampleTemplate_init() {

}

function ExampleTemplate_AdminInit() {
    global $debug;
    defined('DEBUG')  ? $debug->log("ExampleTemplate initialice", "SMBasic", "INFO") : null;    
}

function ExampleTemplate_install() {
    global $db;
    require_once "db/ExampleTemplate.db.php";
    foreach ($exampleTemplate_database as $query) {
        $db->query($query);
    }    
    return true;    
}

function ExampleTemplate_preInstall() {
    
}
function ExampleTemplate_preInstall_info() {
    
}

function ExampleTemplate_upgrade() {
    
}
function ExampleTemplate_uninstall() {
    global $db;
    require_once "db/ExampleTemplate.db.php";
    foreach ($exampleTemplate_database_uninstall as $query) {
        $db->query($query);
    }    
    return true;        
}
