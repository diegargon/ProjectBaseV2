<?php

/**
 *  Custom config example, rename to config.inc.php
 *  
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
/**
 * Report errors
 */
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
/* error_reporting(E_ALL); */

/**
 * Debug messages true, comment to disable
 */
define('DEBUG', true);
//SQL
/**
 * We use SQL always must be true
 */
define('SQL', true);

/**
 * Support type: mysql only atm
 */
define('DB_TYPE', 'mysql');
/**
 * Database name
 */
define('DB_NAME', 'ProjectBaseV2');
/**
 * Database host
 */
define('DB_HOST', 'localhost');
/**
 * Database User
 */
define('DB_USER', '');
/**
 *  Database Password
 */
define('DB_PASSWORD', '');
/**
 * Default database charset
 */
define('DB_CHARSET', 'utf8');
/**
 * Default table prefix
 */
define('DB_PREFIX', 'pb_');
/**
 * Default minimal chars for seach
 */
define('DB_MINCHAR_SEARCH', 2);

/**
 * Default filter class
 */
define('FILTER', 'SecureFilter');

/**
 * Debug debug class
 */
define('DEBUG_CORE', 'DebugBasic');


//CORE 
//ONLY EFFECT FOR INSTALL LATER WE USE THE CONF IN DATABASE

$cfg['REL_PATH'] = '/';
$cfg['WEB_URL'] = 'https://localhost.ld' . $cfg['REL_PATH'];
$cfg['STATIC_SRV_URL'] = $cfg['WEB_URL'];
$cfg['WEB_LANG'] = 'es';
$cfg['WEB_LOGO'] = $cfg['STATIC_SRV_URL'] . '/favicon-96x96.png';

$cfg['default_timezone'] = 'Europe/Madrid';
$cfg['server_timezone'] = 'Europe/Madrid';
$cfg['default_dateformat'] = 'd/m/U H:i';
