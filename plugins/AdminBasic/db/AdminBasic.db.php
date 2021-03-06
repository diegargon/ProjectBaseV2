<?php

/**
 *  AdminBasic - Main db install file
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/* INSTALL */

$adminbasic_database_install = [
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('AdminBasic', 'adminbasic_debug', '1');"
];

/*
 * ACL INSTALL
 * 
 * TODO: FIX: if ACL not defined choice for install this later or do a "register perm" better
 */

$adminbasic_acl_install [] = "INSERT INTO `" . DB_PREFIX . "permissions` (`perm_name`, `perm_desc`, `plugin`) VALUES    
('r_adminmain_access', 'L_PERM_R_ADMINMAIN_ACCESS','ADMIN'),
('w_admin_all', 'L_PERM_R_PHPINFO','ADMIN'),
('r_admin_all', 'L_PERM_R_PHPINFO','ADMIN'),
('r_phpinfo', 'L_PERM_R_PHPINFO','ADMIN'),
('w_general_cfg', 'L_PERM_W_GENERAL_CFG','ADMIN'),
('r_general_cfg', 'L_PERM_R_GENERAL_CFG','ADMIN'),
('r_debug_cfg', 'L_PERM_R_DEBUG_CFG','ADMIN'),
('w_debug_cfg', 'L_PERM_W_DEBUG_CFG','ADMIN'),
('r_plugin_cfg', 'L_PERM_R_PLUGIN_CFG','ADMIN'),
('w_plugin_cfg', 'L_PERM_W_PLUGIN_CFG','ADMIN')
";

$adminbasic_acl_install [] = "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('AdminBasic', 'adminbasic_acl_install', '0.2');";
$adminbasic_acl_uninstall [] = "DELETE FROM `" . DB_PREFIX . "config` WHERE cfg_key = 'adminbasic_acl_install'";
/*
 * UNINSTALL 
 */
$adminbasic_database_uninstall = [
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'AdminBasic'",
    "DELETE FROM `" . DB_PREFIX . "plugins` WHERE plugin_name = 'AdminBasic'",
    "DELETE FROM `" . DB_PREFIX . "permissions` WHERE plugin = 'ADMIN'"
];
