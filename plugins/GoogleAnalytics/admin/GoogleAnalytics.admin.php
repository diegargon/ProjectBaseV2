<?php

/**
 *  GoogleAnalytics - Admin file
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage GoogleAnalytics
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global plugins $plugins
 */
function GoogleAnalytics_AdminInit() {
    global $plugins;
    $plugins->expressStart('GoogleAnalytics') ? register_action('add_admin_menu', 'GoogleAnalytics_AdminMenu', '5') : null;
}

/**
 * Admin menu
 * 
 * @global plugins $plugins
 * @param array $params
 * @return string
 */
function GoogleAnalytics_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('GoogleAnalytics');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'GoogleAnalytics_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'GoogleAnalytics_admin_content', $params);
        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">GoogleAnalytics</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">GoogleAnalytics</a></li>';
    }
}

/**
 * Admin aside
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function GoogleAnalytics_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
}

/**
 * Admin content
 * @global array $LNG
 * @param array $params
 * @return string
 */
function GoogleAnalytics_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('GoogleAnalytics');
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('GoogleAnalytics');
    }
    return $page_data;
}
