<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function AdminBasic_init() {
    global $sm, $cfg;

    define('ADMIN', TRUE);

    /* ACL its optional we must check if ACL was activate after first install for create the admin perms if need  */
    if (defined('ACL') && !isset($cfg['adminbasic_acl_install'])) {
        global $db;
        require_once "db/AdminBasic.db.php";
        foreach ($adminbasic_acl_install as $query) {
            $db->query($query);
        }
    }

    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if (($user['isAdmin']) || (defined('ACL') && $acl_auth->acl_ask("admin_all"))) {
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
    foreach ($adminbasic_database_install as $query) {
        $db->query($query);
    }

    if (defined('ACL')) {
        foreach ($admin_acl_install as $query) {
            $db->query($query);
        }
    }
    return;
}
