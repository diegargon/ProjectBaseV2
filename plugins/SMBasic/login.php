<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!($sm->getPerm("login_enable"))) {
    $frontend->messageBox(['msg' => "L_E_LOGGIN_DISABLE"]);
    return;
}

$user = $sm->getSessionUser();

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_LOGIN'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_LOGIN'];
//END HEAD MOD

if ($user && $user['uid'] > 0) {
    $frontend->messageBox(['msg' => 'L_E_ALREADY_LOGGED']);
    return;
}

require_once("includes/SMBasic.login.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['active'])) {
        if (($activation_code = $filter->get_int('active', 1, 1)) &&
                !SMBasic_user_activate_account($activation_code)
        ) {
            $msgbox['title'] = "L_SM_REGISTERED";
            $msgbox['msg'] = "L_SM_E_ACTIVATION";
        } else {
            $msgbox['title'] = "L_SM_TITLE_OK";
            $msgbox['msg'] = "L_SM_ACTIVATION_OK";
        }
        $msgbox['backlink'] = $cfg['WEB_URL'];
        $frontend->messageBox($msgbox);
        return;
    }

    if (isset($_GET['reset'])) {
        if (!SMBasic_user_reset_password()) {
            $msgbox['msg'] = "L_SM_E_RESET";
        } else {
            $msgbox['title'] = 'L_SM_TITLE_OK';
            $msgbox['msg'] = "L_SM_RESET_OK";
        }
        $msgbox['backlink'] = $cfg['WEB_URL'];
        $frontend->messageBox($msgbox);
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !empty($_POST['password'])) {
        $email = $filter->post_email("email");
        $password = $filter->post_password("password");
        ($filter->post_int("rememberme")) ? $rememberme = 1 : $rememberme = 0;
        if ($email != false && $password != false) {
            SMBasic_Login($email, $password, $rememberme);
        } else {
            die('[{"status": "error", "msg": "' . $LNG['L_E_EMAILPASSWORD'] . '"}]');
        }
    } else if (isset($_POST['email']) && !empty($_POST['reset_password_chk'])) {
        SMBasic_RequestResetOrActivation();
    }
} else {
    /*
      if ($cfg['smbasic_oauth']) {
      require_once 'includes/SMBasic-oauth.inc.php';
      if (!empty($_GET['provider'])) {
      SMB_oauth_DoLogin();
      } else {
      $login_data['oAuth_data'] = SMB_oauth_getLoginURL();
      }
      }
     */
    $tpl->getCssFile("SMBasic");
    $tpl->getCssFile("SMBasic", "SMBasic-mobile");
    SMBasic_LoginScripts();
    if ($cfg['FRIENDLY_URL']) {
        $login_data['register_url'] = "/{$cfg['WEB_LANG']}/register";
    } else {
        $login_data['register_url'] = "/{$cfg['CON_FILE']}?module=SMBasic&page=register&lang={$cfg['WEB_LANG']}";
    }
    $tpl->addtoTplVar("ADD_TO_BODY", $tpl->getTplFile("SMBasic", "login", $login_data));
}