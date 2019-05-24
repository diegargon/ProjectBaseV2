<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
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

    /* Default SimpleFrontend layouts */
    $frontend->registerLayout(['name' => 'Index 3 Colums', 'plugin' => 'SimpleFrontend', 'file' => 'index_3', 'sections' => 3]);
    $frontend->registerLayout(['name' => 'Index 2 Colums', 'plugin' => 'SimpleFrontend', 'file' => 'index_2', 'sections' => 2]);
    $frontend->registerLayout(['name' => 'Index 1 Colums', 'plugin' => 'SimpleFrontend', 'file' => 'index_1', 'sections' => 2]);

    if (defined('BLOCKS')) {
        global $blocks, $cfg;

        $blocks->registerBlocksPage('index', $cfg['index_sections']);
    }
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

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=2">' . $LNG['L_FRONT_INDEX_CFG'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
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
    global $tpl, $cfg, $filter, $LNG, $frontend, $blocks;

    $layouts = $frontend->getLayouts();
    $blocks_pages = $blocks->getPages();
    $content = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnChangeLayout'])) {
        global $db;

        $layout_opt = $filter->post_strict_chars('admin_layout', 255, 1);
        $layout_page = $filter->post_strict_chars('page', 255, 1);
        if (!empty($layout_opt) && !empty($layout_page)) {
            if ($layout_opt == 'none') {
                $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => ''], ['cfg_key' => $layout_page . '_layout']);
                $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => ''], ['cfg_key' => $layout_page . '_plugin_layout']);
                $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => ''], ['cfg_key' => $layout_page . '_sections']);
            } else {
                foreach ($layouts as $layout) {
                    if ($layout['file'] == $layout_opt) {
                        $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => $layout['file']], ['cfg_key' => $layout_page . '_layout']);
                        $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => $layout['plugin']], ['cfg_key' => $layout_page . '_plugin_layout']);
                        $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => $layout['sections']], ['cfg_key' => $layout_page . '_sections']);
                        $cfg[$layout_page . '_layout'] = $layout['file'];
                        $cfg[$layout_page . '_plugin_layout'] = $layout['plugin'];
                        $cfg[$layout_page . '_sections'] = $layout['sections'];
                    }
                }
            }
        }
    }
    foreach ($blocks_pages as $blocks_page) {
        $page_data['page_name'] = $blocks_page['page_name'];
        $page_data['layouts_select'] = '<option value="none">' . $LNG['L_NONE'] . '</option>';
        foreach ($layouts as $layout) {
            if (isset($cfg[$blocks_page['page_name'] . '_layout']) && ($layout['file'] == $cfg[$blocks_page['page_name'] . '_layout'])) {
                $page_data['layouts_select'] .= '<option selected value="' . $layout['file'] . '">' . $layout['name'] . '</option>';
            } else {
                $page_data['layouts_select'] .= '<option value="' . $layout['file'] . '">' . $layout['name'] . '</option>';
            }
        }
        $content .= $tpl->getTplFile('SimpleFrontend', 'admin_layouts', $page_data);
    }

    return $content;
}

function Admin_getLayouts() {

    require_once('plugins/SimpleFrontend/includes/layouts.php');

    //Custom layouts, tpl going on custom tpl;
    foreach (glob('frontpage/*.layouts.php') as $layouts) {
        include($layouts);
    }
}
