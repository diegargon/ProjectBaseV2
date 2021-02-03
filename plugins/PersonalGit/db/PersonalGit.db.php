<?php

/**
 *  PersonalGit - Install DB file
 *  
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage PersonalGit
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

$personalGit_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('PersonalGit', 'git_token', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('PersonalGit', 'git_user', 'none');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('PersonalGit', 'git_menu_text', 'Git');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('PersonalGit', 'max_readme_chars', '200');"
];

/* UNINSTALL */

$personalGit_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'PersonalGit'",
];

