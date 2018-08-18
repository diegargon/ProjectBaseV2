<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function AdminBasic_init() {
    global $sm;

    define('ADMIN', TRUE);
    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if ((defined('ACL') && $acl_auth->acl_ask("admin_all")) || (!defined('ACL') && $user['isAdmin'])) {
            register_action("header_menu_element", "AdminBasic_menu_opt");
        }
    }
}

function AdminBasic_menu_opt() {
    global $tpl;
    return $tpl->getTPL_file("AdminBasic", "admin_menu_opt");
}

function AdminBasic_Install() {

    global $db;
    require_once "db/AdminBasic.db.php";
    foreach ($adminbasic_database as $query) {
        $db->query($query);
    }
    return;
}
