<?php

/**
 *  SMBasic register page
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_REGISTER'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_REGISTER'];
//END HEAD MOD

if (!($sm->register_enable)) {
    $frontend->messageBox(['msg' => 'L_E_REGISTER_DISABLE']);
    return false;
}

require_once('includes/SMBasic.register.php');

$user = $sm->getSessionUser();

if ($user && $user['uid'] != 0) {
    $frontend->messageBox(['msg' => 'L_E_ALREADY_LOGGED']);
    return false;
}

//if ((!isset($_POST['email']) || ($cfg['smbasic_need_username'] == 1) && !isset($_POST['username'])) &&
//        !isset($_POST['password']) && !isset($_POST['register'])) {

if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['register'])) {
    SMBasic_RegisterSubmit();
} else {
    SMBasic_Register();
}
    
