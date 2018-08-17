<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * 
 */
!defined('IN_WEB') ? exit : true;

function admin_load_plugin_files() {
    //Load administration side from all register plugins (all enabled) and init the admin_init function.

    global $plugins, $debug, $cfg;


    foreach ($plugins->getEnabled() as $plugin) {
        (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("Admin processing " . $plugin['plugin_name'], "AdminBasic", "DEBUG") : null;
        if (!empty($plugin['function_admin_init'])) {
            $admin_file = "plugins/" . $plugin['plugin_name'] . "/admin/" . $plugin['plugin_name'] . ".admin.php";
            if (file_exists($admin_file)) {
                require_once($admin_file);
                if (function_exists($plugin['function_admin_init'])) {
                    $init_function = $plugin['function_admin_init'];
                    $init_function();
                } else {
                    (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("ADMIN: Function {$plugin['function_admin_init']} not exist", "AdminBasic", "DEBUG") : null;
                }
            } else {
                (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("ADMIN: File $admin_file not exist", "AdminBasic", "DEBUG") : null;
            }
        } else {
            (defined('DEBUG') && $cfg['adminbasic_debug']) ? $debug->log("ADMIN: Plugin {$plugin['plugin_name']} haven't the function admin_init declared in his json file", "AdminBasic", "DEBUG") : null;
        }
    }
}

function Admin_GetPluginState($plugin) {
    global $plugins, $tpl;
    $content = "";


    foreach ($plugins->getEnabled() as $enabled_plugin) {
        if ($enabled_plugin['plugin_name'] == $plugin) {
            //Switch array to text for display
            $enabled_plugin['depends'] = AdminBasic_unserialize_forPrint($enabled_plugin['depends']);
            $enabled_plugin['optional'] = AdminBasic_unserialize_forPrint($enabled_plugin['optional']);
            $enabled_plugin['conflicts'] = AdminBasic_unserialize_forPrint($enabled_plugin['conflicts']);

            $content = $tpl->getTPL_file("AdminBasic", "plugin_state", (array) $enabled_plugin);
        }
    }
    return $content;
}

function AdminBasic_unserialize_forPrint($data, $htmlseparator = "<br/>") {
    $a_data = unserialize($data);
    $result = "";
    foreach ($a_data as $data) {
        $result .= $data->name . " " . $data->min_version . "/" . $data->max_version . $htmlseparator;
    }
    return $result;
}

function AdminBasic_GetConfig($plugin) {
    global $db;

    $query = $db->select("config", "cfg_key,cfg_value", ["plugin" => $plugin]);
    return $db->fetch_all($query);
}

function admin_general_aside($params) {
    global $LNG;

    $general = "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_PL_PLUGINS'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_PL_ADMIN_DEBUG'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=10'>Php Info</a></li>\n";

    $general .= do_action("ADD_ADM_GENERAL_OPT");
    return $general;
}

function admin_general_content($params) {
    global $db, $plugins, $filter, $LNG, $cfg;

    $content = "";

    if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($plugin_id = $filter->post_int("plugin_id")) != false) {

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
            //$db->silent(true);
            $plugins->uninstall($plugin_id, 1);
            //$db->silent(false);
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
            $db->delete("plugins", ["plugin_id" => $plugin_id], "LIMIT 1");
        }
        if (isset($_POST['btnUpgrade'])) {
            $plugins->upgrade($plugin_id);
        }
    }

    if (isset($_POST['btnReScan'])) {
        $plugins->reScanToDB();
    }

    if (isset($_POST['btnDebugChange'])) {
        $q = $db->search("config", "cfg_key", "_debug");
        while ($result = $db->fetch($q)) {
            $checked_value = 0;
            if (isset($_POST['debug_list'])) { //avoid warning if uncheck all
                foreach ($_POST['debug_list'] as $checked) {
                    if ($result['cfg_id'] == $checked) {
                        $checked_value = 1;
                    }
                }
            }
            $db->update("config", ["cfg_value" => $checked_value], ["cfg_id" => $result['cfg_id']]);
        }
    }

    if ($params['opt'] == 1) {
        $content = "<h1>" . $LNG['L_PL_STATE'] . "</h1>";
        $content .= Admin_GetPluginState("AdminBasic");
    } else if ($params['opt'] == 2) {


        ($_SERVER['REQUEST_METHOD'] === 'POST') ? $force_reload = 1 : $force_reload = 0;

        $all_plugins = array_merge($plugins->getEnabled($force_reload), $plugins->getDisabled($force_reload));

        $content .= "<form action='#' method='post' id='formRescan'>";
        $content .= "<p><input type='submit' name='btnReScan'  value='" . $LNG['L_PL_RESCAN'] . "'></p>";
        $content .= "</form>";
        $content .= "<hr/>";
        foreach ($all_plugins as $plugin) {
            $content .= "<form action='' method='post' id='formPlugin'> ";
            $content .= "<p><span>{$plugin['plugin_name']}</span> {$plugin['version']}<br/>";
            $content .= $LNG['L_PL_PROVIDE'] . $plugin['provide'] . "<br/>";
            $content .= !empty($r = AdminBasic_unserialize_forPrint($plugin['depends'])) ? $LNG['L_PL_DEPENDS'] . "<br/>" . $r : null;
            $content .= !empty($r = AdminBasic_unserialize_forPrint($plugin['optional'])) ? $LNG['L_PL_OPTIONAL'] . "<br/>" . $r : null;
            $content .= !empty($r = AdminBasic_unserialize_forPrint($plugin['conflicts'])) ? $LNG['L_PL_CONFLICTS'] . "<br/>" . $r : null;
            $content .= "</p>";
            $content .= "<input type='hidden' name='plugin_id' value='{$plugin['plugin_id']}'/>";
            if ($plugin['missing']) {
                $content .= "<p>{$LNG['L_PL_E_ITSMISSING']}</p>";
                $content .= "<input type='submit' name='btnDeleteMissing'  value='" . $LNG['L_PL_DELETE'] . "'>";
            } else if (!$plugin['installed']) {
                $content .= "<input type='submit' name='btnInstall'  value='" . $LNG['L_PL_INSTALL'] . "'>";
                $content .= "<input type='submit' name='btnCleanFailed'  value='" . $LNG['L_PL_CLEAN_FAILED'] . "'>";
            } else if ($plugin['upgrade_from'] != 0) {
                $content .= "<input type='submit' name='btnUpgrade'  value='" . $LNG['L_PL_UPGRADE'] . "'>";
            } else {
                if (!$plugin['enabled']) {
                    $content .= "<input type='submit' name='btnUninstall'  value='" . $LNG['L_PL_UNINSTALL'] . "'>";
                    $content .= "<input type='submit' name='btnEnable' value='" . $LNG['L_PL_ENABLE'] . "'>";
                } else {
                    if ($plugin['core']) { //CORE PLUGINS can't be disable only switch
                        $content .= "<input disabled name='btnDisable' type='submit' value='" . $LNG['L_PL_DISABLE'] . "'>";
                    } else {
                        $content .= "<input type='submit' name='btnDisable'  value='" . $LNG['L_PL_DISABLE'] . "'>";
                        if ($plugin['autostart']) {
                            $content .= "<input type='submit' name='btnAutostartOff'  value='" . $LNG['L_PL_AUTOSTART_TURN_OFF'] . "'>";
                        } else {
                            $content .= "<input type='submit' name='btnAutostartOn'  value='" . $LNG['L_PL_AUTOSTART_TURN_ON'] . "'>";
                        }
                    }
                }
            }

            $content .= "</form>";
        }

        return $content;
    } else if ($params['opt'] == 3) {
        $content .= "<form method='post' action=''>";

        $q = $db->search("config", "cfg_key", "_debug");
        while ($result = $db->fetch($q)) {
            $content .= "<p>{$result['cfg_key']}";

            if ($result['cfg_value'] == 1) {
                $content .= "<input type='checkbox' name='debug_list[]' value='{$result['cfg_id']}' checked>";
            } else if ($result['cfg_value'] == 0) {
                $content .= "<input type='checkbox' name='debug_list[]' value='{$result['cfg_id']}'>";
            }
            $content .= "</p>";
        }
        $content .= "<input type='submit' name='btnDebugChange'/>";
        $content .= "</form>";
        /*
          /   $enable_plugins = $plugins->getPluginsDB(["enabled" => 1]);
          $content .= "<form method='' action='post'>";
          foreach ($enable_plugins as $enable_plugin) {
          $content .= "<p>";
          $content .= "{$enable_plugin['plugin_name']}";
          $debug_key = strtolower($enable_plugin['plugin_name']) . "_debug";
          if (isset($cfg[$debug_key]) && $cfg[$debug_key] == 1) {
          $content .= "<input type='checkbox' name='{$enable_plugin['plugin_name']}' checked>";
          } else {
          $content .= "<input type='checkbox' name='{$enable_plugin['plugin_name']}'>";
          }
          $content .= "</p>";
          }
          $content .= "</form>";
         * 
         */
    } else if ($params['opt'] == 4) {
        $content .= AdminPluginConfig("CORE");
    } else if ($params['opt'] == 10) {
        $content .= "<div style='width:100%'>" . get_phpinfo() . "</div>";
    }
    return $content;
}

function AdminPluginConfig($plugin) {
    global $db, $filter;

    if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmitConfig']))) {
        $cfg_id = $filter->post_int("configID", 8, 1);
        //WARN: UNFILTERING, UNCHECKING
        $value = $_POST['cfg_value'];
        if (!empty($cfg_id) && ($cfg_id != false) && ($value !== false)) {
            $db->update("config", ["cfg_value" => $value], ["cfg_id" => $cfg_id], "LIMIT 1");
        }
    }
    $content = "<div id='divTable' class='divTable'>";
    $result = $db->select_all("config", ["plugin" => $plugin], "ORDER BY plugin");
    while ($row = $db->fetch($result)) {
        $content .= "<form method='post' action='#'>";
        $content .= "<div id='divRow1' class='divRow'>";
        $content .= "<div class='divCell divCellFixed'>{$row['plugin']}</div>";
        $content .= "<div class='divCell divCellLeft'>{$row['cfg_key']}</div>";
        $content .= "<div class='divCell divCellRight'><input type='text' maxlength='128' size='32' name='cfg_value' value='{$row['cfg_value']}'/>";
        $content .= "<input type='hidden' name='configID' value='{$row['cfg_id']}'/>";
        $content .= "<input type='submit' name='btnSubmitConfig'/>";
        $content .= "</form></div>";

        $content .= "</div>";
    }
    $content .= "</div>";
    return $content;
}

function get_phpinfo() {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $phpinfo = preg_replace('/934(px)?/', '100%', $phpinfo);
    return $phpinfo;
}
