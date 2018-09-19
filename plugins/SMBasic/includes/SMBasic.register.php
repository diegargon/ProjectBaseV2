<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
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
    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    $tpl->AddScriptFile("standard", "jquery", "TOP");
    $tpl->AddScriptFile("SMBasic", "register", "BOTTOM");

    $register_data['terms_url'] = $sm->getPage("terms");
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "register", $register_data));
}

function SMBasic_RegisterSubmit() {
    global $cfg, $LNG, $db, $filter, $sm;

    if (($email = $filter->post_email("email")) == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL'] . '"}]');
    }
    if (($cfg['smbasic_need_username'] == 1) &&
            (($username = $filter->post_strict_chars("username", $cfg['smbasic_max_username'])) == false)) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_USERNAME'] . '"}]');
    }
    if (($cfg['smbasic_need_username'] == 1) &&
            (strlen($username) < $cfg['smbasic_min_username'])
    ) {
        die('[{"status": "2", "msg": "' . $LNG['L_USERNAME_SHORT'] . '"}]');
    }
    if (($password = $filter->post_password("password", 64, 1)) == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD'] . '"}]');
    }
    if (strlen($_POST['password']) < $cfg['smbasic_min_password']) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD_MIN'] . '"}]');
    }
    if (strlen($_POST['password']) > $cfg['smbasic_max_password']) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_PASSWORD_MAX'] . '"}]');
    }

    $query = $db->select_all("users", array("username" => "$username"), "LIMIT 1");

    if (($db->num_rows($query)) > 0) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_USERNAME_EXISTS'] . '"}]');
    }

    $query = $db->select_all("users", array("email" => "$email"));
    if (($db->num_rows($query)) > 0) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL_EXISTS'] . '"}]');
    }

    $db->free($query);

    $password = $sm->encrypt_password($password);
    if ($cfg['smbasic_email_confirmation']) {
        $active = mt_rand(11111111, 2147483647); //Largest mysql init
        $register_message = $LNG['L_REGISTER_OKMSG_CONFIRMATION'];
    } else {
        $active = 1;
        $register_message = $LNG['L_REGISTER_OKMSG'];
    }
    $mail_msg = SMBasic_create_reg_mail($active);
    $query = $db->insert("users", array("username" => $db->escape_strip($username), "password" => "$password", "email" => $db->escape_strip($email), "active" => "$active"));

    if ($query) {
        mail($email, $LNG['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
        die('[{"status": "ok", "msg": "' . $register_message . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "7", "msg": "' . $LNG['L_REG_ERROR_WHILE_REG'] . '"}]');
    }

    return true;
}
