<?php

/**
 *  SMBasic main init file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
/*
 * do_action("encrypt_password") // Override/set for change default one
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Init() {
    global $sm, $frontend;

    define('SESSIONS', TRUE);

    !isset($sm) ? $sm = new SessionManager : null;
    $sm->start();

    SMBasic_SetTopNavUserMenu();

    $frontend->registerPage(['module' => 'SMBasic', 'page' => 'login', 'type' => 'disk']);
    $frontend->registerPage(['module' => 'SMBasic', 'page' => 'logout', 'type' => 'virtual', 'func' => [$sm, 'logout']]);
    $frontend->registerPage(['module' => 'SMBasic', 'page' => 'profile', 'type' => 'disk']);
    $frontend->registerPage(['module' => 'SMBasic', 'page' => 'register', 'type' => 'disk']);
    $frontend->registerPage(['module' => 'SMBasic', 'page' => 'terms', 'type' => 'disk']);

    return true;
}

function SMBasic_Install() {

    global $db;
    require_once ('db/SMBasic.db.php');
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
    return 'This plugin provided a basic session manager';
}

function SMBasic_uninstall() {
    
}
