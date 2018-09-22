<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once ('install.db.php');

$step = $filter->get_int('step', 1, 1);

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

            if ($db->table_exist('config') || $db->table_exist('plugin')) {
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
                $provide_results = $plugins->getPluginProvide('SESSIONS');

                foreach ($provide_results as $provider) {
                    if ($plugins->check_depends($provider->depends)) {
                        if ($first) {
                            $first = false;
                            echo '<input checked type="radio" name="session_mgr" value="' . $provider->plugin_name . '">' . $provider->plugin_name . '<br\>';
                        } else {
                            echo '<input type="radio" name="session_mgr" value="' . $provider->plugin_name . '">' . $provider->plugin_name . '<br\>';
                        }
                    } else {
                        echo '<input type="radio" name="sessions" value="none">None</option>';
                    }
                    require_once("plugins/$provider->plugin_name/$provider->main_file");
                    $plugins->includePluginFiles($provider->plugin_name);
                    $func_plugInstallInfo = $provider->function_pre_install_info;

                    if (function_exists($func_plugInstallInfo)) {
                        echo '<p>' . $func_plugInstallInfo() . '</p>';
                    }
                    echo '<hr/>';
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

            $session_mgr = $filter->post_AZChar('session_mgr', 255, 1);
            $tpl_mgr = $filter->post_AZChar('tpl_mgr', 255, 1);
            $admin_mgr = $filter->post_AZChar('admin_mgr', 255, 1);
            $frontend_mgr = $filter->post_AZChar('frontend_mgr', 255, 1);

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

            //TODO: Search for core dependeces and mark as core
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

            $db->update('config', ['cfg_value' => 1], ['cfg_key' => 'CORE_INSTALLED', 'plugin' => 'CORE']);
            ?>   
            <p>Installation finished</p>
            <?php
        }
        ?>
    </body>
</html>


