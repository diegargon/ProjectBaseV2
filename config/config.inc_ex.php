<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 *  Custom config file
 * 
 */

define('DEBUG', true);
//SQL
define('SQL', true);

define('DB_TYPE', 'mysql');
define('DB', 'ProjectBaseV2');
define('DB_HOST', 'localhost');
define('DB_USER', 'projectbase');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8');
define('DB_PREFIX', 'pb_');
define('DB_MINCHAR_SEARCH', 2);

//Filter
define('FILTER', "SecureFilter");

//DEBUG
define('DEBUG_CORE', "DebugBasic");

//CORE 
//ONLY EFFECT FOR INSTALL LATER WE USE THE CONF IN DATABASE

$cfg['WEB_URL'] = "";
$cfg['STATIC_SRV_URL'] = $cfg['WEB_URL'];
$cfg['WEB_LANG'] = "es";
$cfg['WEB_LOGO'] = $cfg['STATIC_SRV_URL'] . "favicon-96x96.png";

/*

$cfg['TITLE'] = $cfg['WEB_NAME'];
$cfg['FRIENDLY_URL'] = 1;
$cfg['PAGE_TITLE'] = $cfg['TITLE'];
$cfg['PAGE_KEYWORDS'] = "test";
$cfg['PAGE_AUTHOR'] = "ProjectBase";

$cfg['PAGE_DESC'] = "Noticias";
*&
/*
$cfg['THEME'] = "default";
*/
/*
$cfg['CORE_VERSION'] = "";


$cfg['WEB_LANG_NAME'] = "Español";
1 = 1; //used when not ML


$cfg['BACKLINK'] = "javascript:history.go(-1)";

cfg['REMOTE_CHECKS'] = 1;
$cfg['ADMIN_EMAIL'] = "";
$cfg['CONTACT_EMAIL'] = $cfg['ADMIN_EMAIL'];
$cfg['smbasic_register_reply_email'] = "";
$cfg['SERVER_STRESS'] = 0.8;
$cfg['TERMS_URL'] = "Terms";

$cfg['SOCIAL_FACEBOOK_URL'] = "";
$cfg['SOCIAL_TWEETER_URL'] = "";
//$cfg['']
 */