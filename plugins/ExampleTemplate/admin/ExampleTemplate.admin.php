<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function ExampleTemplate_AdminInit() {
    global $plugins;
    $plugins->express_start('ExampleTemplate') ? register_action('add_admin_menu', 'ExampleTemplate_AdminMenu', '5') : null;
}

function ExampleTemplate_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('ExampleTemplate');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'ExampleTemplate_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'ExampleTemplate_admin_content', $params);
        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=$tab_num">ExampleTemplate</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=$tab_num">ExampleTemplate</a></li>';
    }
}

function ExampleTemplate_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>\n' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>\n';
}

function ExampleTemplate_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('ExampleTemplate');
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('ExampleTemplate');
    }
    return $page_data;
}
