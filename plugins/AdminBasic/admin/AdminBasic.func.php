<?php

/**
 *  AdminBasic Functions
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * check auth
 * 
 * @global sm $sm
 * @global acl_auth $acl_auth
 * @global frontend $frontend
 * @param string $tokens
 * @return boolean
 */
function admin_auth($tokens) {
    global $sm, $acl_auth;

    $user = $sm->getSessionUser();

    if ($user && ($user['isFounder'] == 1 || (defined('ACL') && $acl_auth->acl_ask($tokens)) || (!defined('ACL') && $user['isAdmin']))
    ) {
        return true;
    }

    global $frontend;

    $msgbox['msg'] = 'L_E_NOACCESS';
    $msgbox['backlink'] = $sm->getPage('login');
    $msgbox['backlink_title'] = 'L_LOGIN';

    $frontend->messageBox($msgbox);

    return false;
}

/**
 * Load plugin files
 * 
 * @global plugins $plugins
 * @global debug $debug
 * @global array $cfg
 */
function admin_load_plugin_files() {
    //Load administration side from all register plugins (all enabled) and init the admin_init function.
    global $plugins, $debug, $cfg;

    foreach ($plugins->getPluginsDB() as $plugin) {
        if ($plugin['enabled']) {
            (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug_msg = 'ADMIN: processing ' . $plugin['plugin_name'] : null;
            if (!empty($plugin['function_admin_init'])) {
                $admin_file = 'plugins/' . $plugin['plugin_name'] . '/admin/' . $plugin['plugin_name'] . '.admin.php';
                if (file_exists($admin_file)) {
                    require_once($admin_file);
                    if (function_exists($plugin['function_admin_init'])) {
                        $init_function = $plugin['function_admin_init'];
                        $init_function();
                        (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("{$debug_msg} -> Init sucessfull ", 'AdminBasic', 'Debug') : null;
                    } else {
                        (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("{$debug_msg} -> Function {$plugin['function_admin_init']} not exist", 'AdminBasic', 'WARNING') : null;
                    }
                } else {
                    (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("{$debug_msg} -> File $admin_file not exist", 'AdminBasic', 'WARNING') : null;
                }
            } else {
                (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("{$debug_msg} -> Plugin haven't the function admin_init declared in his json file", 'AdminBasic', 'WARNING') : null;
            }
        }
    }
}

/**
 * Get Plugin State
 * 
 * @global plugins $plugins
 * @global tpl $tpl
 * @param string $plugin_name
 * @return string
 */
function Admin_GetPluginState($plugin_name) {
    global $plugins, $tpl;
    $content = '';

    foreach ($plugins->getPluginsDB() as $plugin) {
        if ($plugin['enabled'] && $plugin['plugin_name'] == $plugin_name) {
            //Switch array to text for display
            $plugin['depends'] = AdminBasic_unserialize_forPrint($plugin['depends']);
            $plugin['optional'] = AdminBasic_unserialize_forPrint($plugin['optional']);
            $plugin['conflicts'] = AdminBasic_unserialize_forPrint($plugin['conflicts']);

            $content = $tpl->getTplFile('AdminBasic', 'plugin_state_info', (array) $plugin);
        }
    }
    return $content;
}

/**
 * Unserialize data to print
 * 
 * TODO: Change this
 * 
 * @param string $data
 * @param string $htmlseparator
 * @return string
 */
function AdminBasic_unserialize_forPrint($data, $htmlseparator = '<br/>') {
    $a_data = unserialize($data);
    $result = '';
    foreach ($a_data as $data) {
        $result .= $data->name . ' ' . $data->min_version . '/' . $data->max_version . $htmlseparator;
    }
    return $result;
}

/**
 * What display
 * 
 * @global array $LNG
 * @global tpl $tpl
 * @global plugins $plugins
 * @param array $plugins_list
 * @return array
 */
function plugins_ctrl_display() {
    global $LNG, $tpl, $plugins;

    $content = '';
    $counter = 1;

    //($_SERVER['REQUEST_METHOD'] === 'POST') ? $force_reload = 0 : $force_reload = 1;

    $plugins_list = $plugins->getPluginsDB();

    if (empty($plugins_list)) {
        return false;
    }

    $num_items = count($plugins_list);

    foreach ($plugins_list as $plugin) {
        $plugin['TPL_CTRL'] = $counter;
        ($counter == $num_items) ? $plugin['TPL_FOOT'] = 1 : $plugin['TPL_FOOT'] = 0;

        $plugin['DEPENDS'] = !empty($r = AdminBasic_unserialize_forPrint($plugin['depends'])) ? $LNG['L_PL_DEPENDS'] . '<br/>' . $r : null;
        $plugin['OPTIONAL'] = !empty($r = AdminBasic_unserialize_forPrint($plugin['optional'])) ? $LNG['L_PL_OPTIONAL'] . '<br/>' . $r : null;
        $plugin['CONFLICTS'] = !empty($r = AdminBasic_unserialize_forPrint($plugin['conflicts'])) ? $LNG['L_PL_CONFLICTS'] . '<br/>' . $r : null;

        $plugin['BUTTOMS_CODE'] = "";

        if ($plugin['missing']) {
            $plugin['BUTTOMS_CODE'] .= "<p>{$LNG['L_PL_E_ITSMISSING']}</p>";
            $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnDeleteMissing'  value='" . $LNG['L_PL_DELETE'] . "'>";
        } else if (!$plugin['installed']) {

            $missing_install_depends = 0;
            //check if depends its installed for show install button because perhaps need the depends table for install.
            foreach (unserialize($plugin['depends']) as $depend) {
                if (!$plugins->checkInstalledProvider($depend->name)) {
                    $missing_install_depends = 1;
                }
            }
            if (!$missing_install_depends) {
                $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnInstall'  value='" . $LNG['L_PL_INSTALL'] . "'>";
                $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnCleanFailed'  value='" . $LNG['L_PL_CLEAN_FAILED'] . "'>";
            }
        } else if ($plugin['upgrade_from'] != 0) {
            $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnUpgrade'  value='" . $LNG['L_PL_UPGRADE'] . "'>";
        } else {
            if (!$plugin['enabled']) {
                $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnUninstall'  value='" . $LNG['L_PL_UNINSTALL'] . "'>";
                $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnEnable' value='" . $LNG['L_PL_ENABLE'] . "'>";
            } else {
                if ($plugin['core']) { //CORE PLUGINS can't be disable only switch
                    $plugin['BUTTOMS_CODE'] .= "<input disabled name='btnDisable' type='submit' value='" . $LNG['L_PL_DISABLE'] . "'>";
                } else {
                    $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnDisable'  value='" . $LNG['L_PL_DISABLE'] . "'>";
                    if ($plugin['autostart']) {
                        $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnAutostartOff'  value='" . $LNG['L_PL_AUTOSTART_TURN_OFF'] . "'>";
                    } else {
                        $plugin['BUTTOMS_CODE'] .= "<input type='submit' name='btnAutostartOn'  value='" . $LNG['L_PL_AUTOSTART_TURN_ON'] . "'>";
                    }
                }
            }
        }

        $content .= $tpl->getTplFile('AdminBasic', 'plugins_list', $plugin);
        $counter++;
    }
    return $content;
}

/**
 * Plugin config
 * 
 * @global db $db
 * @global filter $filter
 * @global tpl $tpl
 * @param string $plugin
 * @return boolean
 */
function AdminPluginConfig($plugin) {
    global $db, $filter, $tpl;

    if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmitConfig']))) {

        if (!admin_auth('w_general_cfg', 'ADMIN_CONFIG_' . $plugin)) {
            return false;
        }
        $cfg_id = $filter->postInt('configID');
        $value = $_POST['cfg_value'];
        if (!empty($cfg_id) && ($cfg_id != false) && ($value !== false)) {
            $value = $db->escape($value);
            $db->update('config', ['cfg_value' => $value], ['cfg_id' => $cfg_id], 'LIMIT 1');
        }
    }

    $cfg_result = $db->selectAll('config', ['plugin' => $plugin], 'ORDER BY plugin');
    $content = '';
    $counter = 1;
    $num_items = $db->numRows($cfg_result);

    while ($cfg_row = $db->fetch($cfg_result)) {
        $cfg_row['TPL_CTRL'] = $counter;
        $counter == $num_items ? $cfg_row['TPL_FOOT'] = 1 : $cfg_row['TPL_FOOT'] = 0;
        $cfg_row['cfg_value'] = htmlentities($cfg_row['cfg_value'], ENT_QUOTES);
        $content .= $tpl->getTplFile('AdminBasic', 'plugin_config', $cfg_row);
        $counter++;
    }

    return $content;
}

/**
 * Get phpinfo()
 * @return string
 */
function get_phpinfo() {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $phpinfo = preg_replace('/934(px)?/', '100%', $phpinfo);
    return $phpinfo;
}
