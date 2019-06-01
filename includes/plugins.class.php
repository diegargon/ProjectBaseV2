<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 * 
 * 
 */
!defined('IN_WEB') ? exit : true;

class Plugins {

    private $debug;
    private $registered_plugins = [];
    private $depends_provide = [];
    private $plugins_db = [];

    function __construct() {
        
    }

    public function init() {
        global $debug, $cfg;

        (defined('DEBUG') && $cfg['plugins_debug']) ? $this->debug = 1 : $this->debug = 0;

        /*
         * Init enabled plugins with autostart=1, and depends with autostart 0 or 1.
         */

        $this->setPluginsDB();

        //We use &reference because setStarted add field in the loop and foreach copy the array
        foreach ($this->plugins_db as &$plugin) {
            if ($plugin['autostart'] && $plugin['enabled']) {
                $this->debug ? $debug->log('WORKING on ' . $plugin['plugin_name'] . '...', 'PLUGINS', 'INFO') : null;
                if (empty($plugin['started'])) {
                    if ($this->pluginCheck($plugin)) {
                        $this->debug ? $debug->log('Check sucessfull ' . $plugin['plugin_name'] . '...', 'PLUGINS', 'INFO') : null;
                        $this->startPlugin($plugin);
                    } else {
                        $this->debug ? $debug->log('Check unsucessfull ' . $plugin['plugin_name'] . '...', 'PLUGINS', 'ERROR') : null;
                    }
                } else {
                    $this->debug ? $debug->log('Plugin ' . $plugin['plugin_name'] . ' already started', 'PLUGINS', 'INFO') : null;
                }
            } else {
                $this->debug ? $debug->log('Autorstart off, omitting ' . $plugin['plugin_name'] . ' for now...', 'PLUGINS', 'INFO') : null;
            }
        }
    }

    function getPluginsDB() {
        $this->setPluginsDB();
        return $this->plugins_db;
    }

    function setPluginsDB($force = 0) {
        global $db;

        if (empty($this->plugins_db) || $force == 1) {
            $result = $db->selectAll('plugins');
            if ($result) {
                $this->plugins_db = $db->fetchAll($result);
            }
        }
    }

    function startPlugin($plugin) {
        global $debug;

        $this->debug ? $debug->log('STARTING plugin ' . $plugin['plugin_name'] . ' ...', 'PLUGINS', 'INFO') : null;

        require_once('plugins/' . $plugin['plugin_name'] . '/' . $plugin['main_file']);
        $this->includePluginFiles($plugin['plugin_name']);
        $init_function = $plugin['function_init'];
        if (function_exists($init_function)) {
            $init_function();
        } else {
            $this->debug ? $debug->log('Function init on ' . $plugin['plugin_name'] . ' no exist', 'PLUGINS', 'ERROR') : null;
            return false;
        }
        $this->setStarted($plugin);
        $allprovide = preg_split('/\s+/', $plugin['provide']);
        foreach ($allprovide as $provide) {
            $this->setCoreDepend($provide, $plugin['version']);
        }

        return true;
    }

    function setStarted($plugin) {
        foreach ($this->plugins_db as $key => $plugin_db) {
            if ($plugin['plugin_id'] == $plugin_db['plugin_id']) {
                $this->plugins_db[$key]['started'] = 1;
                return;
            }
        }
    }

    function getPluginID($plugin_name) {
        foreach ($this->plugins_db as $plugin) {
            if ($plugin['plugin_name'] == $plugin_name && $plugin['enabled'] == 1) {
                return $plugin['plugin_id'];
            }
        }
        return false;
    }

    function install($pluginid) {
        global $db;

        $query = $db->selectAll('plugins', ['plugin_id' => $pluginid], 'LIMIT 1');
        $plugin = $db->fetch($query);
        if ($plugin['installed'] != 1) {
            require_once("plugins/{$plugin['plugin_name']}/{$plugin['main_file']}");
            $func_plugInstall = $plugin['function_install'];
            if (function_exists($func_plugInstall)) {
                if ($func_plugInstall()) {
                    $db->update('plugins', ['installed' => 1], ['plugin_id' => $pluginid]);
                }
            } else {
                die('FUNCION NO EXISTE');
            }
        } else {
            return false;
        }
        $this->setPluginsDB(1);
        return true;
    }

    function uninstall($pluginid, $force = 0) {
        global $db;

        $query = $db->selectAll('plugins', ['plugin_id' => $pluginid], 'LIMIT 1');
        $plugin = $db->fetch($query);
        if ($plugin['installed'] == 1 || $force) {
            require_once("plugins/{$plugin['plugin_name']}/{$plugin['main_file']}");
            $func_plugUninstall = $plugin['function_uninstall'];
            if (function_exists($func_plugUninstall)) {
                if ($func_plugUninstall()) {
                    $db->update('plugins', ['installed' => 0], ['plugin_id' => $pluginid]);
                }
            } else {
                die('FUNCION NO EXISTE');
            }
        } else {
            return false;
        }
        $this->setPluginsDB(1);
        return true;
    }

    function upgrade($pluginid) {
        global $db;

        $query = $db->selectAll('plugins', ['plugin_id' => $pluginid], 'LIMIT 1');
        $plugin = $db->fetch($query);
        if ($plugin['installed'] == 1) {
            require_once("plugins/{$plugin['plugin_name']}/{$plugin['main_file']}");
            $func_Upgrade = $plugin['function_upgrade'];
            if (function_exists($func_Upgrade)) {
                if ($func_Upgrade($plugin['version'], $plugin['upgrade_from'])) {
                    $db->update('plugins', ['upgrade_from' => 0], ['plugin_id' => $pluginid]);
                }
            } else {
                die('FUNCION NO EXISTE');
            }
        } else {
            return false;
        }
        return true;
    }

    function setEnable($pluginid, $value) {
        global $db;

        if (!(($value == 0) || ($value == 1))) {
            return false;
        }
        $db->update('plugins', ['enabled' => $value], ['plugin_id' => $pluginid], 'LIMIT 1');
        $this->setPluginsDB(1);
        return true;
    }

//TODO: Cambiar para que actualice $plugins_db?
    function setAutostart($pluginid, $value) {
        global $db;

        if (!(($value == 0) || ($value == 1))) {
            return false;
        }

        $db->update('plugins', ['autostart' => $value], ['plugin_id' => $pluginid], 'LIMIT 1');
        $this->setPluginsDB(1);
        return true;
    }

    public function scanDir() {
        global $debug;
        foreach (glob('plugins/*', GLOB_ONLYDIR) as $plugins_dir) {
            $filename = str_replace('plugins/', '', $plugins_dir);
            $full_json_filename = "$plugins_dir/$filename.json";

            if (file_exists($full_json_filename)) {
                $jsondata = file_get_contents($full_json_filename);
                $plugin_data = json_decode($jsondata);
                $this->debug ? $debug->log('Plugin ' . $plugin_data->plugin_name . ' added to the registered', 'PLUGINS', 'INFO') : null;
                array_push($this->registered_plugins, $plugin_data);
            }
        }
    }

    public function getPluginProvide($provide) {
        $result = [];

        foreach ($this->registered_plugins as $plugin) {
            if (!empty($plugin) && (trim($plugin->provide)) == $provide) {
                array_push($result, $plugin);
            }
        }

        return $result;
    }

    public function getPluginByName($plug_name) {
        foreach ($this->registered_plugins as $plugin) {
            if (!empty($plugin) && (trim($plugin->plugin_name)) == $plug_name) {
                return $plugin;
            }
        }
        return null;
    }

    function reScanToDB() {
        global $db;

        $this->registered_plugins = [];
        $this->scanDir();
        $db->update('plugins', ['missing' => 1]); //mark everything missing

        foreach ($this->registered_plugins as $plugin) {
            $result = $db->selectAll('plugins', ['plugin_name' => $plugin->plugin_name], 'LIMIT 1');

            $query_ary = [
                'plugin_name' => $plugin->plugin_name,
                'version' => $plugin->version,
                'main_file' => $plugin->main_file,
                'function_init' => $plugin->function_init,
                'function_admin_init' => $plugin->function_admin_init,
                'function_install' => $plugin->function_install,
                'function_pre_install' => $plugin->function_pre_install,
                'function_pre_install_info' => $plugin->function_pre_install_info,
                'function_uninstall' => $plugin->function_uninstall,
                'function_upgrade' => $plugin->function_upgrade,
                'provide' => $plugin->provide,
                'depends' => serialize($plugin->depends),
                'priority' => $plugin->priority,
                'optional' => serialize($plugin->optional),
                'conflicts' => serialize($plugin->conflicts)
            ];

            if ($db->numRows($result) > 0) {
                $plugin_row = $db->fetch($result);
                if ($plugin->version != $plugin_row['version']) {
                    if ($plugin_row['upgrade_from'] == 0) {
                        $query_ary['upgrade_from'] = $plugin_row['version'];
                    }
                    $query_ary['missing'] = 0;
                    $db->update('plugins', $query_ary, ['plugin_name' => $plugin->plugin_name]);
                } else {
                    //$db->update("plugins", ["missing" => 0], ["plugin_name" => $plugin->plugin_name]);
                    /* Update DB all better while devel and not versioning */
                    $query_ary['missing'] = 0;
                    $db->update('plugins', $query_ary, ['plugin_name' => $plugin->plugin_name]);
                }
            } else {
                $db->insert('plugins', $query_ary);
            }
        }
    }

    function setCoreDepend($depend, $version) {
        $this->depends_provide[$depend] = $version;
    }

    function expressStart($pluginname) {
        global $debug;

        $this->debug ? $debug->log('Express order to start plugin -> ' . $pluginname, 'PLUGINS', 'INFO') : null;
        foreach ($this->plugins_db as $plugin) {
            if ($plugin['enabled'] && $plugin['plugin_name'] == $pluginname) {
                if (!empty($plugin['started'])) {
                    $this->debug ? $debug->log('Plugin ' . $plugin['plugin_name'] . ' already started', 'PLUGINS', 'INFO') : null;
                    return true;
                }
                if ($this->pluginCheck($plugin)) {
                    if ($this->startPlugin($plugin)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        $this->debug ? $debug->log('Plugin ' . $pluginname . ' not exist ', 'PLUGINS', 'ERROR') : null;

        return false;
    }

    function expressStartProvider($provider) {
        global $debug;

        $this->debug ? $debug->log('Express order to start provider ' . $provider, 'PLUGINS', 'INFO') : null;

        foreach ($this->plugins_db as $plugin) {
            if ($plugin['enabled'] && $plugin['provide'] == $provider) {
                if ($this->checkStarted($plugin['plugin_name'])) {
                    $this->debug ? $debug->log('Plugin ' . $plugin['plugin_name'] . ' providing ' . $provider . ' already started', 'PLUGINS', 'INFO') : null;
                    return true;
                }
                if ($this->pluginCheck($plugin)) {
                    if ($this->startPlugin($plugin)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        $this->debug ? $debug->log('Plugin provided ' . $provider . ' not exist', 'PLUGINS', 'ERROR') : null;

        return false;
    }

    function checkDepends($depends) {
        foreach ($depends as $depend) {
            foreach ($this->registered_plugins as $registered) {
                foreach ($registered->depends as $reg_depends) {

                    if ($depend->name == 'CORE') {

                        if (($depend->min_version >= CORE_VERSION) &&
                                (CORE_VERSION <= $depend->max_version )
                        ) {
                            return true;
                        }
                    } else if ($depend->name == $reg_depends->name) {
                        if (($depend->min_version >= $registered->version) &&
                                ($depend->max_version <= $registered->version)
                        ) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    function includePluginFiles($plugin, $admin = 0) {
        global $cfg, $LNG, $debug; //LNG used on include

        $class_file = '';
        $inc_file = '';

        $lang_file = "plugins/$plugin/lang/{$cfg['WEB_LANG']}/$plugin.lang.php";
        if (file_exists($lang_file)) {
            $this->debug ? $debug->log('Loading lang file ' . $lang_file, 'PLUGINS', 'INFO') : null;
            include_once($lang_file);
        }

        //INC FILE
        if ($admin == 0) {
            $inc_file = 'plugins/' . $plugin . '/includes/' . $plugin . '.inc.php';
            $class_file = 'plugins/' . $plugin . '/includes/' . $plugin . '.class.php';
        } else {
            $inc_file = 'plugins/' . $plugin . '/admin/' . $plugin . '.admin.inc.php';
        }
        !empty($inc_file) && file_exists($inc_file) ? include_once($inc_file) : null;
        !empty($inc_file) && file_exists($class_file) ? include_once($class_file) : null;
        if ($this->debug) {
            if (!empty($inc_file) && file_exists($inc_file)) {
                include_once($inc_file);
                $debug->log('Loading file ' . $inc_file, 'PLUGINS', 'INFO');
            }
            if (!empty($inc_file) && file_exists($class_file)) {
                include_once($class_file);
                $debug->log('Loading file ' . $class_file, 'PLUGINS', 'INFO');
            }
        }
    }

    function checkStarted($plugin_name) {

        foreach ($this->plugins_db as $plugins) {
            if ($plugins['plugin_name'] == $plugin_name && !empty($plugins['started'])) {
                return true;
            }
        }
        return false;
    }

    private function pluginCheck($plugin) {
        global $debug;

        if ($this->checkProvideConflicts($plugin)) {
            $this->debug ? $debug->log('Conflicts ' . $plugin['plugin_name'] . ' another plugin provided', 'PLUGINS', 'ERROR') : null;
            return false;
        }

        if (empty($plugin['depends']) || $this->resolvePluginDepends($plugin)) {
            return true;
        }
        return false;
    }

    private function checkProvideConflicts($plugin) {
        //TODO: ONLY CHECK FDUPLICATE PROVIDE, DO CONFLICT CHECK
        $allprovide = preg_split('/\s+/', $plugin['provide']);
        foreach ($allprovide as $provide) {
            if (empty($provide)) {
                return false;
            }
            $result = $this->checkDuplicatedProvide($provide);
            if ($result) {
                return true;
            }
        }
        return false;
    }

    private function checkDuplicatedProvide($provide) {

        foreach ($this->plugins_db as $plugin) {
            if (!empty($plugin['started'])) {
                $allprovide = preg_split('/\s+/', $plugin['provide']);
                foreach ($allprovide as $started_provide) {
                    if ($started_provide == $provide) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function resolvePluginDepends($plugin) {
        global $debug;

        $this->debug ? $debug->log('Resolving depends... ', 'PLUGINS', 'INFO') : null;
        $depends = unserialize($plugin['depends']);


        if (empty($plugin['depends'])) {
            $this->debug ? $debug->log('Empty depends... ', 'PLUGINS', 'INFO') : null;
            return true;
        }

        foreach ($depends as $depend) {
            $this->debug ? $debug->log('As depend checking for ' . $depend->name, 'PLUGINS', 'INFO') : null;

            $result = $this->checkIfDepsStarted($depend->name, $depend->min_version, $depend->max_version);

            if (!$result) {
                $this->debug ? $debug->log('Searching for the necessary dependencies... ', 'PLUGINS', 'INFO') : null;
                if ($this->findDepsAndStart($depend->name, $depend->min_version, $depend->max_version)) {
                    $this->debug ? $debug->log('We found dependence ' . $depend->name . ' for ' . $plugin['plugin_name'], 'PLUGINS', 'INFO') : null;
                } else {
                    $this->debug ? $debug->log('No depedence ' . $depend->name . ' for ' . $plugin['plugin_name'] . ' in the registered plugins', 'PLUGINS', 'ERROR') : null;
                    return false;
                }
            } else {
                $this->debug ? $debug->log('Dependence already started', 'PLUGINS', 'INFO') : null;
            }
        }

        return true;
    }

    function checkEnabled($plugin_name) {
        foreach ($this->plugins_db as $plugin) {
            if ($plugin['enabled'] == 1 && $plugin['plugin_name'] == $plugin_name) {
                return true;
            }
        }
        return false;
    }

    function checkEnabledProvider($provide) {
        if ($provide == 'CORE' || $provide == 'SQL' || $provide == 'DEBUG') {
            return true;
        }
        foreach ($this->plugins_db as $plugin) {
            if ($plugin['enabled'] == 1 && $plugin['provide'] == $provide) {
                return true;
            }
        }
        return false;
    }

    function checkInstalledProvider($provide) {
        if ($provide == 'CORE' || $provide == 'SQL' || $provide == 'DEBUG') {
            return true;
        }
        foreach ($this->plugins_db as $plugin) {
            if ($plugin['installed'] == 1 && $plugin['provide'] == $provide) {
                return true;
            }
        }
        return false;
    }

    private function checkIfDepsStarted($depend_name, $min_version, $max_version) {
        global $debug;

        if (isset($this->depends_provide[$depend_name])) {
            $version = $this->depends_provide[$depend_name];
            if (($version >= $min_version) && ($version <= $max_version)) {
                $this->debug ? $debug->log('Dependence ' . $depend_name . ' already provide', 'PLUGINS', 'INFO') : null;
                return true;
            }
        }
        return false;
    }

    private function findDepsAndStart($depend_name, $min_version, $max_version) {
        global $debug;

        foreach ($this->plugins_db as $plugin) {
            if ($plugin['enabled']) {
                $allprovide = preg_split('/\s+/', $plugin['provide']);
                foreach ($allprovide as $provide) {

                    if (($provide == $depend_name) && ($plugin['version'] >= $min_version) && ($plugin['version'] <= $max_version)
                    ) {
                        if ($this->resolvePluginDepends($plugin)) {//resolv de dependes of the depends
                            if ($plugin['autostart']) {
                                $this->debug ? $debug->log('Starting ' . $depend_name . ' as depend', 'PLUGINS', 'INFO') : null;
                                $this->startPlugin($plugin);
                            } else {
                                $this->debug ? $debug->log('NOT Starting  as depend ' . $depend_name . ' because autostart its off, express start need', 'PLUGINS', 'INFO') : null;
                            }
                            return true;
                        }
                    }
                }
            }
        }
        $this->debug ? $debug->log('Failed finding dependence ' . $depend_name, 'PLUGINS', 'ERROR') : null;
        return false;
    }

}
