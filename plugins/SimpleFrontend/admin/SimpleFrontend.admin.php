<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 */
!defined('IN_WEB') ? exit : true;

function SimpleFrontend_AdminInit() {
    global $frontend, $plugins;
    if ((!$plugins->express_start('SimpleFrontend'))) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }
    register_action('add_admin_menu', 'SimpleFrontend_AdminMenu', '5');
}

function SimpleFrontend_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('SimpleFrontend');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'SimpleFrontend_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'SimpleFrontend_admin_content', $params);
        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>SimpleFrontend</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>SimpleFrontend</a></li>";
    }
}

function SimpleFrontend_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_FRONT_INDEX_CFG'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SimpleFrontend_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('SimpleFrontend');
    } else if ($params['opt'] == 2) {
        $page_data = '<h1>' . $LNG['L_FRONT_INDEX_CFG'] . '</h1>';
        $page_data .= SimpleFrontEnd_index_cfg();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('SimpleFrontend');
    }
    return $page_data;
}

function SimpleFrontEnd_index_cfg() {
    global $tpl, $cfg, $filter, $LNG;
    $page_data = [];

    foreach (glob('plugins/SimpleFrontend/index_layouts/*layout.php') as $layouts) {
        include($layouts);
    }
    //Plugin external layout, templates in tpl;
    foreach (glob('frontpage/index_layouts/*layout.php') as $layouts) {
        include($layouts);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $db;

        $index_layout_opt = $filter->post_strict_chars('index_layout', 255, 1);

        if (!empty($index_layout_opt) && $index_layout_opt != $cfg['index_layout']) {
            if ($index_layout_opt == 'none') {
                $db->update('config', ['cfg_value' => ''], ['cfg_key' => 'index_layout']);
                $db->update('config', ['cfg_value' => ''], ['cfg_key' => 'index_sections']);
            } else {
                foreach ($index_layouts as $layout) { //index_layout[] on include($layouts)
                    if ($layout['file'] == $index_layout_opt) {
                        $db->update('config', ['cfg_value' => $layout['file']], ['cfg_key' => 'index_layout']);
                        $db->update('config', ['cfg_value' => $layout['sections']], ['cfg_key' => 'index_sections']);
                        $cfg['index_layout'] = $layout['file'];
                        $cfg['index_sections'] = $layout['sections'];
                    }
                }
            }
        }
    }

    $page_data['layouts_select'] = '<option value="none">' . $LNG['L_NONE'] . '</option>';
    foreach ($index_layouts as $layout) {
        if ($layout['file'] == $cfg['index_layout']) {
            $page_data['layouts_select'] .= "<option selected value='{$layout['file']}'>{$layout['name']}</option>";
        } else {
            $page_data['layouts_select'] .= "<option value='{$layout['file']}'>{$layout['name']}</option>";
        }
    }

    return $tpl->getTplFile('SimpleFrontend', 'admin_index', $page_data);
}
