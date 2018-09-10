<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleFrontend_init() {
    global $frontend, $plugins;

    //require('includes/blocks.class.php');
    //FIX: On first install $frontend its mandatory but blocks not, that going to cause problems if blocks its disable
    //Probably make blocks optionals and check if active before express start.

    $plugins->express_start("Blocks");
    !isset($frontend) ? $frontend = new SimpleFrontend() : null;
}

function SimpleFrontend_install() {
    global $db, $cfg;

    require_once "db/SimpleFrontend.db.php";
    foreach ($simplefrontend_db_install as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function SimpleFrontend_preInstall() {
    return true;
}

function SimpleFrontend_preInstall_info() {
    return true;
}

function SimpleFrontend_upgrade($version, $from_version) {
    return true;
    /*
      global $db;
      require_once "db/SimpleFrontend.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($exampleTemplate_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : false;
      }
      return false;
     * 
     */
}

function SimpleFrontend_uninstall() {
    global $db, $cfg;
    require_once "db/SimpleFrontend.db.php";
    foreach ($simplefrontend_db_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
