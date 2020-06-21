<?php

/**
 *  Multilang  main file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Multilang
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init func
 * @global Multilang $ml
 */
function Multilang_init() {
    global $ml, $tpl;

    if (!defined('MULTILANG')) {
        define('MULTILANG', TRUE);
        $ml = new Multilang();
        $tpl->getCssFile('Multilang');
    }

    return true;
}

/**
 * Install function
 * @global db $db
 * @return boolean
 */
function Multilang_install() {
    global $db;
    require_once ('db/Multilang.db.php');
    foreach ($multilang_database as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

/**
 * Uninstall func
 * @global db $db
 * @return boolean
 */
function Multilang_uninstall() {
    global $db;
    require_once ('db/Multilang.db.php');
    foreach ($multilang_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
