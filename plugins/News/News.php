<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function News_init() {
    define("News", true);
}

function News_install() {
    global $db;
    require_once "db/News.db.php";
    foreach ($news_database_install as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}

function News_preInstall() {
    return true;
}

function News_preInstall_info() {
    return true;
}

function News_upgrade($version, $from_version) {
    /*
      global $db;
      require_once "db/News.db.php";
      if ($version == 0.3 && $from_version == 0.2) {
      foreach ($news_database_upgrade_002_to_003 as $query) {
      $r = $db->query($query);
      }
      return ($r) ? true : false;
      }

     */
    return false;
}

function News_uninstall() {
    global $db;
    require_once "db/News.db.php";
    foreach ($news_database_uninstall as $query) {
        $r = $db->query($query);
    }
    return ($r) ? true : false;
}
