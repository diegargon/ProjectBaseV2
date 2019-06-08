<?php

/**
 *  DebugBasic
 * 
 *  Log debug messsages
 * 
 *  Error levels: ERROR, WARNING, NOTICE, DEBUG
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Class Debug
 */
class Debug {

    /**
     * Main array with all debug messages
     * @var array
     */
    private $debug_msg = [];

    /**
     * Log message
     * @param string $msg
     * @param string $module
     * @param string $level
     */
    function log($msg, $module, $level = 'DEBUG') {
        $this->debug_msg[] = ['msg' => $msg, 'module' => $module, 'level' => $level];
    }

    /**
     * Get debug messages
     * @param string $module
     * @param string $level
     * @return array
     */
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

    /**
     * Print debug
     * 
     * @param string $module
     * @param string $level
     * @return string
     */
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
                $result .= '<span style="color:blue">[' . $element['level'] . ']</span> ';
            } else if ($element['level'] == 'NOTICE') {
                $result .= '<span style="color:yellow">[' . $element['level'] . ']</span> ';
            } else {
                $result .= '[' . $element['level'] . '] ';
            }
            $result .= '[' . $element['module'] . ']' . $element['msg'] . '<br/>';
        }
        return $result;
    }

}
