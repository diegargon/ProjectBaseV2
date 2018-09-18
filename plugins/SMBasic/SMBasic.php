<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * do_action("encrypt_password") // Override/set for change default one
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Init() {
    global $sm, $frontend;

    define('SESSIONS', TRUE);

    !isset($sm) ? $sm = new SessionManager : null;
    $sm->start();

    register_action("header_menu_element", "SMBasic_navLogReg");

    $frontend->register_page(['module' => 'SMBasic', 'page' => 'login', 'type' => 'disk']);
    $frontend->register_page(['module' => 'SMBasic', 'page' => 'logout', 'type' => 'virtual', 'func' => [$sm, 'logout']]);
    $frontend->register_page(['module' => 'SMBasic', 'page' => 'profile', 'type' => 'disk']);
    $frontend->register_page(['module' => 'SMBasic', 'page' => 'register', 'type' => 'disk']);
}

function SMBasic_Install() {

    global $db;
    require_once "db/SMBasic.db.php";
    foreach ($smbasic_database as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
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
