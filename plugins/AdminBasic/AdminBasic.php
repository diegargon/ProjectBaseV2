<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function AdminBasic_init() {
    global $sm, $cfg, $debug, $tpl;

    (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("AdminBasic initialice", "AdminBasic Init", "INFO") : null;

    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if ((defined('ACL') && $acl_auth->acl_ask("admin_all")) || (!defined('ACL') && $user['isAdmin'])) {
            //  $tpl->addto_tplvar("HEADER_MENU_ELEMENT", $tpl->getTPL_file("AdminBasic", "admin_menu_opt"));
            register_action("header_menu_element", "AdminBasic_menu");
        }
    }
}

function AdminBasic_menu() {
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
