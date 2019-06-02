<?php

/**
 *  Custom config example, rename to config.inc.php
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */

define('DEBUG', true);
//SQL
define('SQL', true);

define('DB_TYPE', 'mysql');
define('DB_NAME', 'ProjectBaseV2');
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

$cfg['WEB_URL'] = '';
$cfg['STATIC_SRV_URL'] = $cfg['WEB_URL'];
$cfg['WEB_LANG'] = 'es';
$cfg['WEB_LOGO'] = $cfg['STATIC_SRV_URL'] . 'favicon-96x96.png';
$cfg['default_timezone'] = 'Europe/Madrid';
$cfg['server_timezone'] = 'Europe/Madrid';
$cfg['default_dateformat'] = 'd/m/y H:i';
