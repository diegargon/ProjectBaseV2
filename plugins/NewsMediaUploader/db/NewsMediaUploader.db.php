<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;


//CONFIG
$newsMediaUploader_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'newsmedia_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_max_filesize', '8mb');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_accepted_files', 'jpeg,jpg,png,gif');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_allow_anon', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_create_thumbs', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_create_mobile', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_create_desktop', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_media_files_dir', 'media');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_tumbs_width', '250');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_mobile_width', '300');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_desktop_width', '800');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'upload_max_list_files', '10');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', 'allow_remote_file_upload', '1');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('NewsMediaUploader', '', '');",
];

/* UNINSTALL */

$newsMediaUploader_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'NewsMediaUploader'",
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'NewsMediaUploader'"
];

