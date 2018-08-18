<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * do_action("encrypt_password") // Override/set for change default one
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Init() {
    global $sm, $cfg, $debug;
    
    define ('SESSIONS', TRUE);
    
    (defined('DEBUG') && $cfg['smbasic_debug']) ? $debug->log("SMBasic initialice", "SMBasic", "INFO") : null;

    !isset($sm) ? $sm = new SessionManager : null;
    $sm->start();

    register_action("header_menu_element", "SMBasic_navLogReg");
}

function SMBasic_Install() {

    global $db;
    require_once "db/SMBasic.db.php";
    foreach ($smbasic_database as $query) {
        $db->query($query);
    }
    return;
}

function SMBasic_preInstall() {
    return;
}

function SMBasic_preInstall_info() {
    //return msg
    return "This plugin provided a basic session manager";
}

function SMBasic_uninstall() {
    
}
