<?php

/**
 *  PersonalGit
 *  
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage PersonalGit
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Init
 * @global PersonalGit $personal_git
 */
function PersonalGit_init() {
    global $frontend;
    $frontend->registerPage(['module' => 'PersonalGit', 'page' => 'git', 'type' => 'disk']);
    $frontend->addMenuItem('sections_menu', git_section_nav_elements(), 9);

    return true;
}

/**
 * Install
 * 
 * @global db $db
 * @return boolean
 */
function PersonalGit_install() {
    global $db;
    require_once ('db/PersonalGit.db.php');
    foreach ($personalGit_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

/**
 * PreInstall
 * 
 * @return boolean
 */
function PersonalGit_preInstall() {
    return true;
}

/**
 * PreInstall Info
 * 
 * @return boolean
 */
function PersonalGit_preInstall_info() {
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
function PersonalGit_upgrade($version, $from_version) {
    return false;
}

/**
 * Uninstall
 * @global db $db
 * @return boolean
 */
function PersonalGit_uninstall() {
    global $db;
    require_once ('db/PersonalGit.db.php');
    foreach ($personalGit_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}
