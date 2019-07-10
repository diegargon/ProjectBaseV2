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
    global $sm, $cfg, $frontend, $plugins;

    define('ADMIN', TRUE);

    /* ACL its optional we check if acl its installed and admin acl not installe for install the permissions */
    if (isset($cfg['acl_installed']) && !isset($cfg['adminbasic_acl_install'])) {
        global $db;
        require_once ('db/AdminBasic.db.php');
        foreach ($adminbasic_acl_install as $query) {
            $db->query($query);
        }
    }

    if ($plugins->checkEnabledProvider('ACL')) {
        $plugins->expressStartProvider('ACL');
    }
    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if (($user['isFounder']) || (defined('ACL') && $acl_auth->acl_ask('w_admin_all||r_admin_all||r_adminmain_access')) || (!defined('ACL') && $user['isAdmin'])) {
            setTopNavAdminMenu();
        }
    }

    $frontend->registerPage(['module' => 'AdminBasic', 'page' => 'adm', 'type' => 'disk']);

    return true;
}

/**
 * Menu opt
 * 
 * @global tpl $tpl
 * @return string
 */
function SetTopNavAdminMenu() {
    global $tpl, $frontend;
    
    $frontend->addTopMenu($tpl->getTplFile('AdminBasic', 'admin_menu_opt'), 2);
            
    return true;
}

/**
 * Install function
 * 
 * @global db $db
 * @return boolean
 */
function AdminBasic_Install() {

    global $db;
    require_once ('db/AdminBasic.db.php');
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
