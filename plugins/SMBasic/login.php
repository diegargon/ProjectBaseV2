<?php

/**
 *  SMBasic login page
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

if (!($sm->login_enable)) {
    $frontend->messageBox(['msg' => 'L_E_LOGGIN_DISABLE']);
    return false;
}

$user = $sm->getSessionUser();

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_LOGIN'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_LOGIN'];
//END HEAD MOD

if ($user && $user['uid'] > 0) {
    $frontend->messageBox(['msg' => 'L_E_ALREADY_LOGGED']);
    return false;
}

require_once('includes/SMBasic.login.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['active'])) {
        if (($activation_code = $filter->getInt('active', 10, 1)) &&
                !SMBasic_user_activate_account($activation_code)
        ) {
            $msgbox['title'] = 'L_SM_REGISTERED';
            $msgbox['msg'] = 'L_SM_E_ACTIVATION';
        } else {
            $msgbox['title'] = 'L_SM_TITLE_OK';
            $msgbox['msg'] = 'L_SM_ACTIVATION_OK';
        }
        $msgbox['backlink'] = $cfg['WEB_URL'];
        $frontend->messageBox($msgbox);
        return false;
    }

    if (isset($_GET['reset'])) {
        if (!SMBasic_user_reset_password()) {
            $msgbox['msg'] = 'L_SM_E_RESET';
        } else {
            $msgbox['title'] = 'L_SM_TITLE_OK';
            $msgbox['msg'] = 'L_SM_RESET_OK';
        }
        $msgbox['backlink'] = $cfg['WEB_URL'];
        $frontend->messageBox($msgbox);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !empty($_POST['password'])) {
        $email = $filter->postEmail('email');
        $password = $filter->postPassword('password');
        ($filter->postInt('rememberme')) ? $rememberme = 1 : $rememberme = 0;
        if ($email != false && $password != false) {
            SMBasic_Login($email, $password, $rememberme);
            return false;
        } else {
            die('[{"status": "error", "msg": "' . $LNG['L_E_EMAILPASSWORD'] . '"}]');
        }
    } else if (isset($_POST['email']) && !empty($_POST['reset_password_chk'])) {
        SMBasic_RequestResetOrActivation();
        return false;
    }
}
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
$tpl->getCssFile('SMBasic');
$tpl->getCssFile('SMBasic', 'SMBasic-mobile');
$tpl->addScriptFile('standard', 'jquery', 'TOP');
$tpl->addScriptFile('SMBasic', 'login', 'BOTTOM');

if ($cfg['FRIENDLY_URL']) {
    $login_data['register_url'] = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/register";
} else {
    $login_data['register_url'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=SMBasic&page=register&lang={$cfg['WEB_LANG']}";
}
$tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SMBasic', 'login', $login_data));

