<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */

function tplBasic_AdminInit() {
    register_action('add_admin_menu', 'tplBasic_AdminMenu', '5');
}

function tplBasic_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('tplBasic');

    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'tplBasic_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'tplBasic_admin_content', $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>tplBasic</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>tplBasic</a></li>";
    }
}

function tplBasic_AdminAside($params) {
    global $LNG;
    return "<li><a href='{$params['url']}&admtab={$params['admtab']}&opt=1'>" . $LNG['L_PL_STATE'] . '</a></li>' .
            "<li><a href='{$params['url']}&admtab={$params['admtab']}&opt=2'>" . $LNG['L_PL_CONFIG'] . '</a></li>';
}

function tplBasic_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('tplBasic');
    } else if ($params['opt'] == 2) {
        $page_data .= AdminPluginConfig('tplBasic');
    }

    return $page_data;
}
