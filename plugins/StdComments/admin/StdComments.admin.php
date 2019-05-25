<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function StdComments_AdminInit() {
    global $plugins;

    $plugins->expressStart('StdComments') ? register_action('add_admin_menu', 'StdComments_AdminMenu', '5') : null;
}

function StdComments_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('StdComments');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'StdComments_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'StdComments_admin_content', $params);

        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">StdComments</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">StdComments</a></li>';
    }
}

function StdComments_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
}

function StdComments_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('StdComments');
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('StdComments');
    }
    return $page_data;
}
