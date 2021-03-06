<?php
/**
 *  New Install process
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
 */
!defined('IN_WEB') ? exit : true;

/**
 * Install script
 */
global $db;

require_once ('db/install.db.php');

$step = filter_input(INPUT_GET, 'step', FILTER_VALIDATE_INT);

if ($step == null || $step == false) {
    $step = 0;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Install (Step <?= $step ?>)</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <?php
        if ($step == 0) {
            ?>
            <p>Welcome to installation process</p>
            <p>Step 1</p>
            <a href="?step=1">Install core</a><br/>
            <?php
        }

        if ($step == 1) {

            if ($db->tableExists('config') || $db->tableExists('plugin')) {
                exit('ERROR: Core tables exist, install failed? drop tables');
            }
            foreach ($core_database as $query) {
                $db->query($query);
            }
            //POPULATE PLUGINS WITH THE PLUGINS
            $plugins->reScanToDB();

            $first = true;
            ?>
            <p>Core installed sucessfull</p>
            <p>Minimal setup</p>
            <hr/>
            <p>Please selected the session manager</p>
            <form action="?step=2" method="post">
                <?php
                $first = true;
                $provide_results = $plugins->getPluginProvide('SESSIONS');

                foreach ($provide_results as $provider) {
                    if ($first) {
                        $first = false;
                        echo "<input checked type='radio' name='session_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    } else {
                        echo "<input type='radio' name='session_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    }
                    echo "<hr/>";
                }
                ?>

                <p>Please selected the template manager</p>

                <?php
                $first = true;
                $provide_results = $plugins->getPluginProvide('TPL');

                foreach ($provide_results as $provider) {
                    if ($first) {
                        $first = false;
                        echo "<input checked type='radio' name='tpl_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    } else {
                        echo "<input type='radio' name='tpl_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    }
                    echo "<hr/>";
                }
                ?>

                <p>Please selected the admin manager</p>

                <?php
                $first = true;
                $provide_results = $plugins->getPluginProvide("ADMIN");

                foreach ($provide_results as $provider) {
                    if ($first) {
                        $first = false;
                        echo "<input checked type='radio' name='admin_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    } else {
                        echo "<input type='radio' name='admin_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    }
                    echo "<hr/>";
                }
                ?>

                <p>Please selected the frontend manager</p>

                <?php
                $first = true;
                $provide_results = $plugins->getPluginProvide("FRONTEND");

                foreach ($provide_results as $provider) {
                    if ($first) {
                        $first = false;
                        echo "<input checked type='radio' name='frontend_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    } else {
                        echo "<input type='radio' name='frontend_mgr' value='$provider->plugin_name'>$provider->plugin_name<br\>";
                    }
                    echo "<hr/>";
                }
                ?>

                <br/><br/>
                <input type="submit" value="Submit">
                <br/>
            </form>
            <?php
        }
        if ($step == 2) {

            $set_ary = [
                'enabled' => 1,
                'core' => 1,
                'autostart' => 1,
                'installed' => 1
            ];

            $session_mgr = filter_input(INPUT_POST, 'session_mgr', FILTER_SANITIZE_STRING);
            $tpl_mgr = filter_input(INPUT_POST, 'tpl_mgr', FILTER_SANITIZE_STRING);
            $admin_mgr = filter_input(INPUT_POST, 'admin_mgr', FILTER_SANITIZE_STRING);
            $frontend_mgr = filter_input(INPUT_POST, 'frontend_mgr', FILTER_SANITIZE_STRING);

            if (!isset($session_mgr) || !isset($tpl_mgr) || !isset($admin_mgr)) {
                die('ERROR basic core plugins missing');
            }

            $plugin = $plugins->getPluginByName($session_mgr);
            require_once("plugins/$plugin->plugin_name/$plugin->main_file");
            $func_plugInstall = $plugin->function_install;
            if (!function_exists($func_plugInstall) || !$func_plugInstall()) {
                die($plugin->plugin_name . 'Install error');
            } else {
                $db->update('plugins', $set_ary, ['plugin_name' => $plugin->plugin_name]);
            }

            $plugin = $plugins->getPluginByName($tpl_mgr);
            require_once("plugins/$plugin->plugin_name/$plugin->main_file");
            $func_plugInstall = $plugin->function_install;
            if (!function_exists($func_plugInstall) || !$func_plugInstall()) {
                die($plugin->plugin_name . 'Install error');
            } else {
                $db->update('plugins', $set_ary, ['plugin_name' => $plugin->plugin_name]);
            }


            $plugin = $plugins->getPluginByName($admin_mgr);
            require_once("plugins/$plugin->plugin_name/$plugin->main_file");
            $func_plugInstall = $plugin->function_install;
            if (!function_exists($func_plugInstall) || !$func_plugInstall()) {
                die($plugin->plugin_name . 'Install error');
            } else {
                $db->update('plugins', $set_ary, ['plugin_name' => $plugin->plugin_name]);
            }


            $plugin = $plugins->getPluginByName($frontend_mgr);
            require_once("plugins/$plugin->plugin_name/$plugin->main_file");
            $func_plugInstall = $plugin->function_install;
            if (!function_exists($func_plugInstall) || !$func_plugInstall()) {
                die($plugin->plugin_name . 'Install error');
            } else {
                $db->update('plugins', $set_ary, ['plugin_name' => $plugin->plugin_name]);
            }

            //Blocks are optionally, check if works next reinstall and delete it if
            //TODO: Search for core dependeces and mark as core
            /*
              if ($plugin->plugin_name == 'SimpleFrontend') {
              $plugin = $plugins->getPluginByName('Blocks');
              require_once("plugins/$plugin->plugin_name/$plugin->main_file");
              $func_plugInstall = $plugin->function_install;
              if (!function_exists($func_plugInstall) || !$func_plugInstall()) {
              die($plugin->plugin_name . 'Install error');
              } else {
              $db->update('plugins', $set_ary, ['plugin_name' => $plugin->plugin_name]);
              }
              }
             */

            $db->update('config', ['cfg_value' => 1], ['cfg_key' => 'CORE_INSTALLED', 'plugin' => 'CORE']);
            ?>   
            <p>Installation finished</p>
            <?php
        }
        ?>
    </body>
</html>


