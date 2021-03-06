<?php

/**
 *  tplBasic - admin file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage tplBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */

/**
 *  tplBasic Admin init function
 */
function tplBasic_AdminInit() {
    register_action('add_admin_menu', 'tplBasic_AdminMenu', '5');
}

/**
 * tplBasic admin menu
 * 
 * @global plugins $plugins
 * @param array $params
 * @return string
 */
function tplBasic_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('tplBasic');

    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'tplBasic_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'tplBasic_admin_content', $params);

        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">tplBasic</a></li>';
    } else if ($params['admtab'] == 3) {
        
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">tplBasic</a></li>';
    }
}

/**
 * Admin aside menu
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function tplBasic_AdminAside($params) {
    global $LNG;
    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a onclick="return confirm(\'' . $LNG['L_SURE'] . '\')" href="admin&admtab=' . $params['admtab'] . '&opt=3">' . $LNG['L_DELETE'] . ' cache</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
}

/**
 * tplBasic admin content
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function tplBasic_admin_content($params) {
    global $LNG, $tpl;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('tplBasic');
    } else if ($params['opt'] == 3) {
        $tpl->deleteCache();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('tplBasic');
    }

    return $page_data;
}
