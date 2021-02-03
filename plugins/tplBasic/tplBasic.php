<?php

/**
 *  tplBasic
 * 
 *  responsable get and formating templates
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage tplBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * tplBasic init function
 * 
 * @global tpl $tpl
 * @global array $cfg
 * @global db $db
 */
function tplBasic_init() {
    global $tpl, $cfg;

    define('TPL', TRUE);

    if (defined('SQL')) {
        global $db;
        $tpl = new TPL($cfg, $db);
    } else {
        $tpl = new TPL($cfg);
    }

    return true;
}

/**
 * Install
 * 
 * @global db $db
 * @return boolean
 */
function tplBasic_Install() {
    global $db;
    require_once 'db/tplBasic.db.php';
    foreach ($tplbasic_database as $query) {
        if ($db->query($query) == false) {
            return false;
        }
    }
    return true;
}
