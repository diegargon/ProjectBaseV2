<?php

/**
 *  NewsmediaUploader - Main file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function NewsMediaUploader_init() {
    global $frontend;

    /*
      if ($user && defined('ACL') && $cfg['NMU_ACL_CHECK']) {
      global $acl_auth;
      if (!$acl_auth->acl_ask($cfg['NMU_ACL_LIST'])) {
      $tpl->addtoTplVar("NEWS_FORM_TOP_OPTION", NMU_disable_warn());
      return false;
      }
      }
     */

    $frontend->registerPage(['module' => 'NewsMediaUploader', 'page' => 'upload', 'type' => 'disk']);
    $frontend->registerPage(['module' => 'NewsMediaUploader', 'page' => 'remote_upload', 'type' => 'disk']);
    $frontend->registerPage(['module' => 'NewsMediaUploader', 'page' => 'get_links', 'type' => 'disk']);
    register_action('news_new_form_add', 'NMU_form_add');
    register_action('news_edit_form_add', 'NMU_form_add');
    register_action('news_newlang_form_add', 'NMU_form_add');
    register_action('news_newpage_form_add', 'NMU_form_add');
    
    return true;
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
