<?php

/*
 *  Copyright @ 2018 Diego Garcia
 * 
 *  Class: register_uniq_action("action",  array($class, "method"));
 *  Function: register_uniq_action("action", "function"))
 * 
 *  
 * 
 */
!defined('IN_WEB') ? exit : true;

global $actions;
$actions = [];

function register_action($event, $func, $priority = 5) {
    global $actions;

    $actions[$event][] = ["function_name" => $func, "priority" => $priority];
}

function register_uniq_action($event, $func, $priority = 5) {
    global $actions;

    foreach ($actions as $key => $value) {
        if ($key == $event) {
            $actions[$key][0] = ["function_name" => $func, "priority" => $priority];
            return;
        }
    }
    $actions[$event][] = ["function_name" => $func, "priority" => $priority];
}

function do_action($event, &$params = null) {
    global $actions;

    if (isset($actions[$event])) {
        usort($actions[$event], function($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        foreach ($actions[$event] as $func) {

            if (is_array($func['function_name'])) {
                if (method_exists($func['function_name'][0], $func['function_name'][1])) {
                    if (isset($return)) {
                        $return .= call_user_func_array($func['function_name'], [&$params]);
                    } else {
                        $return = call_user_func_array($func['function_name'], [&$params]);
                    }
                }
            } else {
                if (function_exists($func['function_name'])) {
                    if (isset($return)) {
                        $return .= call_user_func_array($func['function_name'], [&$params]);
                    } else {
                        $return = call_user_func_array($func['function_name'], [&$params]);
                    }
                }
            }
        }
    }
    if (isset($return)) {
        return $return;
    } else {
        return false;
    }
}

function action_isset($this_event) {
    global $actions;

    foreach ($actions as $event => $func) {
        if (($event == $this_event) && function_exists($func[0])) {
            return true;
        }
    }

    return false;
}
