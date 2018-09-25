<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsMediaUploader_init() {
    global $frontend;

    /*
      if ($user && defined('ACL') && $cfg['NMU_ACL_CHECK']) {
      global $acl_auth;
      if (!$acl_auth->acl_ask($cfg['NMU_ACL_LIST'])) {
      $tpl->addto_tplvar("NEWS_FORM_TOP_OPTION", NMU_disable_warn());
      return false;
      }
      }
     */

    $frontend->register_page(['module' => 'NewsMediaUploader', 'page' => 'upload', 'type' => 'disk']);
    $frontend->register_page(['module' => 'NewsMediaUploader', 'page' => 'remote_upload', 'type' => 'disk']);
    register_action('news_new_form_add', 'NMU_form_add');
    register_action('news_edit_form_add', 'NMU_form_add');
    register_action('news_newlang_form_add', 'NMU_form_add');
    register_action('news_newpage_form_add', 'NMU_form_add');
}

function NewsMediaUploader_install() {
    global $db;
    require_once ('db/NewsMediaUploader.db.php');
    
    foreach ($newsMediaUploader_database_install as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }
    return true;
}

function NewsMediaUploader_preInstall() {
    return true;
}

function NewsMediaUploader_preInstall_info() {
    return true;
}

function NewsMediaUploader_upgrade($version, $from_version) {
    return true;
}

function NewsMediaUploader_uninstall() {
    global $db;
    require_once ('db/NewsMediaUploader.db.php');
    foreach ($newsMediaUploader_database_uninstall as $query) {
        if (!$db->query($query)) {
            return false;
        }
    }

    return true;
}