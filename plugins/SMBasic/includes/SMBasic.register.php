<?php

/**
 *  SMBasic register file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Register() {
    global $tpl, $sm;
    /*
      if ($cfg['smbasic_oauth']) {
      require_once 'includes/SMBasic-oauth.inc.php';
      if (!empty($_GET['provider'])) {
      SMB_oauth_DoLogin();
      } else {
      $register_data['oAuth_data'] = SMB_oauth_getLoginURL();
      }
      }
     * 
     */
    $tpl->getCssFile('SMBasic');
    $tpl->getCssFile('SMBasic', 'SMBasic-mobile');
    $tpl->addScriptFile('standard', 'jquery', 'TOP');
    $tpl->addScriptFile('SMBasic', 'register', 'BOTTOM');

    $register_data['terms_url'] = $sm->getPage('terms');
    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SMBasic', 'register', $register_data));
}

function SMBasic_RegisterSubmit() {
    global $cfg, $LNG, $db, $filter, $sm;

    if (($email = $filter->postEmail('email')) == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL'] . '"}]');
    }
    if (($cfg['smbasic_need_username'] == 1) &&
            (($username = $filter->postUsername("username", $cfg['smbasic_max_username'])) == false)) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_USERNAME'] . '"}]');
    }
    if (($cfg['smbasic_need_username'] == 1) &&
            (strlen($username) < $cfg['smbasic_min_username'])
    ) {
        die('[{"status": "2", "msg": "' . $LNG['L_USERNAME_SHORT'] . '"}]');
    }
    if (strlen($_POST['password']) < $cfg['smbasic_min_password']) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD_MIN'] . '"}]');
    }
    if (strlen($_POST['password']) > $cfg['smbasic_max_password']) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD_MAX'] . '"}]');
    }
    if (($password = $filter->postPassword("password", $cfg['smbasic_max_password'], $cfg['smbasic_min_password'])) == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD'] . '"}]');
    }

    $query = $db->selectAll('users', ['username' => $username], 'LIMIT 1');

    if (($db->numRows($query)) > 0) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_USERNAME_EXISTS'] . '"}]');
    }

    $query = $db->selectAll('users', ['email' => $email]);
    if (($db->numRows($query)) > 0) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL_EXISTS'] . '"}]');
    }

    $db->free($query);

    $password = $sm->encryptPassword($password);
    if ($cfg['smbasic_email_confirmation']) {
        $active = mt_rand(11111111, 2147483647); //Largest mysql init
        $register_message = $LNG['L_REGISTER_OKMSG_CONFIRMATION'];
    } else {
        $active = 1;
        $register_message = $LNG['L_REGISTER_OKMSG'];
    }
    $mail_msg = SMBasic_create_reg_mail($active);
    $user_what = [
        'username' => $db->escapeStrip($username),
        'password' => "$password",
        'email' => $db->escapeStrip($email),
        'active' => "$active"
    ];
    $query = $db->insert('users', $user_what);

    if ($query) {
        mail($email, $LNG['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
        die('[{"status": "ok", "msg": "' . $register_message . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "7", "msg": "' . $LNG['L_REG_ERROR_WHILE_REG'] . '"}]');
    }

    return true;
}
