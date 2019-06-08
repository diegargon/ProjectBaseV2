<?php

/**
 *  AdminBasic
 * 
 *  Init file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 * 
 * @global sm $sm
 * @global array $cfg
 * @global frontend $frontend
 * @global db $db
 * @global acl_auth $acl_auth
 */
function AdminBasic_init() {
    global $sm, $cfg, $frontend;

    define('ADMIN', TRUE);

    /* ACL its optional we check if acl its installed and admin acl not installe for install the permissions */
    if ($cfg['acl_installed'] && !isset($cfg['adminbasic_acl_install'])) {
        global $db;
        require_once "db/AdminBasic.db.php";
        foreach ($adminbasic_acl_install as $query) {
            $db->query($query);
        }
    }

    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if (($user['isFounder']) || (defined('ACL') && $acl_auth->acl_ask("admin_all"))) {
            register_action("header_menu_element", "AdminBasic_menu_opt");
        }
    }

    $frontend->registerPage(['module' => 'AdminBasic', 'page' => 'adm', 'type' => 'disk']);
}

/**
 * Menu opt
 * 
 * @global tpl $tpl
 * @return string
 */
function AdminBasic_menu_opt() {
    global $tpl;
    return $tpl->getTplFile("AdminBasic", "admin_menu_opt");
}

/**
 * Install function
 * 
 * @global db $db
 * @return boolean
 */
function AdminBasic_Install() {

    global $db;
    require_once "db/AdminBasic.db.php";
    foreach ($adminbasic_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }

    if (defined('ACL')) {
        foreach ($admin_acl_install as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
    }
    return true;
}
