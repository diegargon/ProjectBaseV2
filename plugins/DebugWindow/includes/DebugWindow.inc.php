<?php

/**
 *  DebugWindow Include file
 *  
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage DebugWindow
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * return html a DebugWindow
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
