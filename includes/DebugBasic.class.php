<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 *  Error levels:
 *  ERROR
 *  WARNING
 *  NOTICE
 *  DEBUG
 */
!defined('IN_WEB') ? exit : true;

class Debug {

    private $debug_msg = [];

    function __construct() {
        
    }

    function log($msg, $module, $level = 'DEBUG') {
        $this->debug_msg[] = ['msg' => $msg, 'module' => $module, 'level' => $level];
    }

    function getDebug($module = 'all', $level = 'all') {
        $filter_debug = [];
        foreach ($this->debug_msg as $element) {
            if ($module == 'all' || $module == $element['module']) {
                if ($level == 'all' || $level == $element['level']) {
                    $filter_debug[] = ['msg' => $element['msg'], 'module' => $element['module'], 'level' => $element['level']];
                }
            }
        }
        return $filter_debug;
    }

    function printDebug($module = 'all', $level = 'all') {
        $module_track_br = '';
        $result = '';
        foreach ($this->getDebug($module, $level) as $element) {
            if ($module_track_br != $element['module']) { // add space between modules
                $module_track_br = $element['module'];
                $result .= '<br/>';
            }
            if ($element['level'] == 'ERROR') {
                $result .= '<span style="color:red">[' . $element['level'] . ']</span> ';
            } else if ($element['level'] == 'WARNING') {
                $result .= '<span style="color:yellow">[' . $element['level'] . ']</span> ';
            } else {
                $result .= '[' . $element['level'] . '] ';
            }
            $result .= '[' . $element['module'] . ']' . $element['msg'] . '<br/>';
        }
        return $result;
    }

}
