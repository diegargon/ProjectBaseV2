<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;


require_once("includes/SMBasic.profile.php");


//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_PROFILE'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_PROFILE'];
//END HEAD MOD

$user = $sm->getSessionUser();

if (empty($user) || $user === false || $user['uid'] <= 0) {
    //$sm->destroy();
    $msgbox['msg'] = "L_E_NOT_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

if (isset($_POST['profile'])) {
    SMBasic_ProfileChange($user);
} else if (isset($_GET['viewprofile'])) {
    SMBasic_ProfileView();
} else {
    SMBasic_ProfileEdit($user);
}