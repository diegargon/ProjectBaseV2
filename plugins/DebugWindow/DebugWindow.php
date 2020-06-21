<?php

/**
 *  DebugWindow main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage DebugWindow
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 */
function DebugWindow_init() {
    global $cfg, $sm;

    if (defined('SESSIONS') && defined('DEBUG')) {
        $user = $sm->getSessionUser();

        if ($cfg['debugwindow_only_root'] && $user['isAdmin']) {
            register_action('add_to_footer', 'debug_window');
        }
    }

    return true;
}

/**
 * Install
 * @return boolean
 */
function DebugWindow_install() {
    global $db;
    require_once ('db/DebugWindow.db.php');
    if (!empty($debugWindow_database_install)) {
        foreach ($debugWindow_database_install as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * preInstall
 * @return boolean
 */
function DebugWindow_preInstall() {
    return true;
}

/**
 * preInstall info
 * @return boolean
 */
function DebugWindow_preInstall_info() {
    return true;
}

/**
 * upgrade func
 * @param float $version
 * @param flaot $from_version
 * @return boolean
 */
function DebugWindow_upgrade($version, $from_version) {
    return true;
}

function DebugWindow_uninstall() {
    global $db;
    require_once ('db/DebugWindow.db.php');
    if (!empty($debugWindow_database_uninstall)) {
        foreach ($debugWindow_database_uninstall as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
    }
    return true;
}
