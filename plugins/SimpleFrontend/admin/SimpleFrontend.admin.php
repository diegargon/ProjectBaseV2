<?php

/*
 *  Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
 * 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Admin init function
 * @global frontend $frontend
 * @global plugins $plugins
 * @global blocks $blocks
 * @global array $cfg
 * @return boolean
 */
function SimpleFrontend_AdminInit() {
    global $frontend, $plugins;
    if ((!$plugins->expressStart('SimpleFrontend'))) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }
    register_action('add_admin_menu', 'SimpleFrontend_AdminMenu', '5');
}

/**
 * Admin menu
 * 
 * @global plugins $plugins
 * @param array $params
 * @return string
 */
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

/**
 * Admin aside menu
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function SimpleFrontend_AdminAside($params) {
    global $LNG;

    $aside_menu = '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>';
    // if (defined('BLOCKS')) {
    $aside_menu .= '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=2">' . $LNG['L_FRONT_INDEX_CFG'] . '</a></li>';
    //}
    $aside_menu .= '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';

    return $aside_menu;
}

/**
 * Admin content
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
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

/**
 * Index configuration
 * 
 * @global tpl $tpl
 * @global array $cfg
 * @global filter $filter
 * @global array $LNG
 * @global frontend $frontend
 * @global blocks $blocks
 * @global db $db
 * @return string
 */
function SimpleFrontEnd_index_cfg() {
    global $tpl, $cfg, $filter, $LNG, $frontend;

    $layouts = $frontend->getLayouts();

    $content = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnChangeLayout'])) {
        global $db;

        $layout_opt = $filter->postStrictChars('admin_layout', 255, 1);

        if (!empty($layout_opt)) {
            if ($layout_opt == 'none') {
                $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => ''], ['cfg_key' => 'index_layout']);
            } else {
                foreach ($layouts as $layout) {
                    if ($layout['file'] == $layout_opt) {
                        $db->upsert('config', ['plugin' => 'SimpleFrontend', 'cfg_value' => $layout['file']], ['cfg_key' => 'index_layout']);
                        $cfg['index_layout'] = $layout['file'];
                    }
                }
            }
        }
    }

    $page_data['layouts_select'] = '';
    foreach ($layouts as $layout) {
        if ($layout['file'] == $cfg['index_layout']) {
            $page_data['layouts_select'] .= '<option selected value="' . $layout['file'] . '">' . $layout['name'] . '</option>';            
        } else {
            $page_data['layouts_select'] .= '<option value="' . $layout['file'] . '">' . $layout['name'] . '</option>';
        }
    }
    $content .= $tpl->getTplFile('SimpleFrontend', 'admin_layouts', $page_data);

    return $content;
}

/*
function SimpleFrontEnd_index_cfg() {
    global $tpl, $cfg, $filter, $LNG, $frontend, $blocks;

    $layouts = $frontend->getLayouts();
    $blocks_pages = $blocks->getPages();
    $content = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnChangeLayout'])) {
        global $db;

        $layout_opt = $filter->postStrictChars('admin_layout', 255, 1);
        $layout_page = $filter->postStrictChars('page', 255, 1);
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
*/