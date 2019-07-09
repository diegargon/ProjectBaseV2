<?php

/**
 *  ExampleTemplate
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage ExampleTemplate
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global ExampleTemplate $example_template
 */
function ExampleTemplate_init() {
    global $example_template;
    !isset($example_template) ? $example_template = new ExampleTemplate() : null;
    
    return true;
}

/**
 * Install
 * 
 * @global db $db
 * @return boolean
 */
function ExampleTemplate_install() {
    global $db;
    require_once ('db/ExampleTemplate.db.php');
    if (!empty($exampleTemplate_database_install)) {
        foreach ($exampleTemplate_database_install as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
    }
    return true;
}

/**
 * PreInstall
 * 
 * @return boolean
 */
function ExampleTemplate_preInstall() {
    return true;
}

/**
 * PreInstall Info
 * 
 * @return boolean
 */
function ExampleTemplate_preInstall_info() {
    return true;
}

/**
 * Upgrade 
 * 
 * @global db $db
 * @param flaot $version
 * @param float $from_version
 * @return boolean
 */
function ExampleTemplate_upgrade($version, $from_version) {
    global $db;
    require_once ('db/ExampleTemplate.db.php');
    if ($version == 0.3 && $from_version == 0.2) {
        if (!empty($exampleTemplate_database_upgrade_002_to_003)) {
            foreach ($exampleTemplate_database_upgrade_002_to_003 as $query) {
                if (!$db->query($query)) {
                    return false;
                }
            }
        }
        return true;
    }
    return false;
}

/**
 * Uninstall
 * @global db $db
 * @return boolean
 */
function ExampleTemplate_uninstall() {
    global $db;
    require_once ('db/ExampleTemplate.db.php');
    if (!empty($exampleTemplate_database_uninstall)) {
        foreach ($exampleTemplate_database_uninstall as $query) {
            if (!$db->query($query)) {
                return false;
            }
        }
    }
    return true;
}
