<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once("includes/SMBasic.register.php");

$user = $sm->getSessionUser();

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_REGISTER'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_REGISTER'];
//END HEAD MOD

if ($user && $user['uid'] != 0) {
    $msgbox['msg'] = "L_E_ALREADY_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

//if ((!isset($_POST['email']) || ($cfg['smbasic_need_username'] == 1) && !isset($_POST['username'])) &&
//        !isset($_POST['password']) && !isset($_POST['register'])) {

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SMBasic_RegisterSubmit();
} else {
    SMBasic_Register();
}
    