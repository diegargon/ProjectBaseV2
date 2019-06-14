<?php

/**
 *  DebugWindow main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage DebugWindow
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init function
 */
function DebugWindow_init() {
    register_action('add_to_footer', 'debug_window');
}

/**
 * return html a debug window
 * @global type $cfg
 * @global type $db
 * @global type $debug
 * @return boolean|string
 */
function debug_window() {
    global $cfg, $db, $debug;

    if (defined('DEBUG')) {

        ($cfg['smbasic_debug']) ? setSessionDebugDetails() : null;

        $q_history = $db->getQueryHistory();
        foreach ($q_history as $value) {
            $debug->log($value, 'MYSQL');
        }
        $debug_data = '<div style="height:250px;width:100%;border:1px solid #ccc;;overflow:auto;">';
        $debug_data .= $debug->printDebug();
        $debug_data .= '</div>';
        return $debug_data;
    }
    return false;
}

/**
 * Install
 * @return boolean
 */
function DebugWindow_install() {
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
    return true;
}
