<?php

/**
 *  Custom config example, rename to config.inc.php
 * 
 *  DO NOT EDIT use config/config.inc.php
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Absolute path
 */
define('ABSPATH', dirname(__FILE__));

error_reporting(E_ALL);

global $cfg;
$cfg = [];
