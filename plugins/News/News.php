<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function News_init() {
    global $frontend;

    define("News", true);
    $news_perms = get_news_perms("init");
    if ($news_perms['news_submit_new']) {
        register_action("header_menu_element", "submit_news_menu");
    }

    $pages_array = [
        ['module' => 'News', 'page' => 'section', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'view_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'submit_news', 'type' => 'disk'],
        ['module' => 'News', 'page' => 'edit_news', 'type' => 'disk']
    ];
    if (!$frontend->register_page_array($pages_array)) {
        die("Register pages faild on News module");
    }
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
