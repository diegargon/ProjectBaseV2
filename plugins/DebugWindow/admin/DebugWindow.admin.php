<?php

/**
 *  DebugWindow main admin
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage DebugWindow
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * DebugWindow main admin init
 * @global type $plugins
 */
function DebugWindow_AdminInit() {
    global $plugins;
    $plugins->expressStart('DebugWindow') ? register_action('add_admin_menu', 'DebugWindow_AdminMenu', '5') : null;
}

/**
 * DebugWindow admin menu
 * @global Plugins $plugins
 * @param array $params
 * @return string #content
 */
function DebugWindow_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('DebugWindow');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'DebugWindow_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'DebugWindow_admin_content', $params);

        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">DebugWindow</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">DebugWindow</a></li>';
    }
}

/**
 * DebugWindow admin aside menu
 * 
 * @global array $LNG
 * @param array $params
 * @return string #content
 */
function DebugWindow_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

/**
 * DebugWindow admin content
 * 
 * @global array $LNG
 * @param array $params
 * @return string #content
 */
function DebugWindow_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('DebugWindow');
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('DebugWindow');
    }
    return $page_data;
}
