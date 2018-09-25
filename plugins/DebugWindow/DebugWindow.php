<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function DebugWindow_init() {
    register_action('add_to_footer', 'stats_window');
    register_action('add_to_footer', 'debug_window');
}

function stats_window() {
    $content = '<p>Memory usage: ' . formatBytes(memory_get_usage()) . ' / Memory peak: ';
    $content .= formatBytes(memory_get_peak_usage()) . '</p>';
    $content .= '<p>Memory  real usage: ' . formatBytes(memory_get_usage(true)) . ' / Memory real peak: ';
    $content .= formatBytes(memory_get_peak_usage(true)) . '</p>';
    return $content;
}

function debug_window() {
    global $cfg, $db, $debug;

    if (defined('DEBUG')) {

        ($cfg['smbasic_debug']) ? setSessionDebugDetails() : null;

        $q_history = $db->get_query_history();
        foreach ($q_history as $value) {
            $debug->log($value, 'MYSQL');
        }
        $debug_data = '<div style="height:250px;width:100%;border:1px solid #ccc;;overflow:auto;">';
        $debug_data .= $debug->print_debug();
        $debug_data .= '</div>';
        return $debug_data;
    }
    return false;
}

function DebugWindow_install() {
    return true;
}

function DebugWindow_preInstall() {
    return true;
}

function DebugWindow_preInstall_info() {
    return true;
}

function DebugWindow_upgrade($version, $from_version) {
    return true;
}

function DebugWindow_uninstall() {
    return true;
}
