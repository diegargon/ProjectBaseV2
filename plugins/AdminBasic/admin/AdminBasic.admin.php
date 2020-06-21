<?php

/**
 *  AdminBasic - Admin file
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global plugins $plugins
 */
function AdminBasic_AdminInit() {
    global $plugins;
    $plugins->expressStart('AdminBasic') ? register_action('add_admin_menu', 'AdminBasic_AdminMenu', '5') : null;
}

/**
 * Admin menu
 * 
 * @global plugins $plugins
 * @param array $params
 * @return string
 */
function AdminBasic_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('AdminBasic');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'AdminBasic_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'AdminBasic_admin_content', $params);
        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">AdminBasic</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">AdminBasic</a></li>';
    }
}

/**
 * Admin aside
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function AdminBasic_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=2">' . $LNG['L_PL_PLUGINS'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=3">' . $LNG['L_PL_ADMIN_DEBUG'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=10">Php Info</a></li>';
}

/**
 * Admin content
 * @global array $LNG
 * @param array $params
 * @return string
 */
function AdminBasic_admin_content($params) {
    global $db, $plugins, $filter, $LNG;

    $content = '';

    if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($plugin_id = $filter->postInt('plugin_id')) != false) {
        if (!admin_auth("w_plugin_cfg")) {
            return false;
        }
        if (isset($_POST['btnInstall'])) {
            if ($plugins->install($plugin_id) == false) {
                die("Plugin $plugin_id install failed");
            }
        }
        if (isset($_POST['btnUninstall'])) {
            if ($plugins->uninstall($plugin_id) == false) {
                die("Plugin $plugin_id uninstall failed");
            }
        }
        if (isset($_POST['btnCleanFailed'])) {
            $db->silent(true);
            $plugins->uninstall($plugin_id, 1);
            $db->silent(false);
        }
        if (isset($_POST['btnEnable'])) {
            $plugins->setEnable($plugin_id, 1);
        }
        if (isset($_POST['btnDisable'])) {
            $plugins->setEnable($plugin_id, 0);
        }
        if (isset($_POST['btnAutostartOn'])) {
            $plugins->setAutostart($plugin_id, 1);
        }
        if (isset($_POST['btnAutostartOff'])) {
            $plugins->setAutostart($plugin_id, 0);
        }
        if (isset($_POST['btnDeleteMissing'])) {
            $db->delete('plugins', ['plugin_id' => $plugin_id], 'LIMIT 1');
        }
        if (isset($_POST['btnUpgrade'])) {
            $plugins->upgrade($plugin_id);
        }
    }

    if (($_SERVER['REQUEST_METHOD'] === 'POST') && $plugin_id == false) {

        if (isset($_POST['btnReScan'])) {
            if (!admin_auth('w_plugin_cfg')) {
                return false;
            }
            $plugins->reScanToDB();
        }

        if (isset($_POST['btnDebugChange'])) {
            if (!admin_auth('w_plugin_cfg')) {
                return false;
            }

            $q = $db->search('config', 'cfg_key', '_debug');
            while ($result = $db->fetch($q)) {
                $checked_value = 0;
                if (isset($_POST['debug_list'])) { //avoid warning if uncheck all
                    foreach ($_POST['debug_list'] as $checked) {
                        if ($result['cfg_id'] == $checked) {
                            $checked_value = 1;
                        }
                    }
                }
                $db->update('config', ['cfg_value' => $checked_value], ['cfg_id' => $result['cfg_id']]);
            }
        }
    }

    if ($params['opt'] == 1) {
        $content = '<h1>' . $LNG['L_PL_STATE'] . '</h1>';
        $content .= Admin_GetPluginState('AdminBasic');
    } else if ($params['opt'] == 2) {
        if (!admin_auth('r_plugin_cfg')) {
            return false;
        }

        $content .= plugins_ctrl_display();
    } else if ($params['opt'] == 3) {
        $content .= '<form method="post" action="">';

        $q = $db->search('config', 'cfg_key', '_debug');
        while ($result = $db->fetch($q)) {
            $content .= "<p>{$result['cfg_key']}";

            if ($result['cfg_value'] == 1) {
                $content .= "<input type='checkbox' name='debug_list[]' value='{$result['cfg_id']}' checked>";
            } else if ($result['cfg_value'] == 0) {
                $content .= "<input type='checkbox' name='debug_list[]' value='{$result['cfg_id']}'>";
            }
            $content .= '</p>';
        }
        $content .= '<input type="submit" name="btnDebugChange"/>';
        $content .= '</form>';
    } else if ($params['opt'] == 4) {
        if (!admin_auth('r_general_cfg')) {
            return false;
        }
        $content .= AdminPluginConfig('CORE');
    } else if ($params['opt'] == 10) {
        if (!admin_auth('r_phpinfo')) {
            return false;
        }
        $content .= '<div style="width:100%">' . get_phpinfo() . '</div>';
    }
    return $content;
}
