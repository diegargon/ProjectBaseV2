<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Login($email, $password, $rememberme) {
    global $cfg, $LNG, $db, $sm;

    $password_encrypt = $sm->encryptPassword($password);

    if (empty($password_encrypt) || $password_encrypt == $password) {
        die('[{"status": "error", "msg": "' . $LNG['L_E_INTERNAL'] . '"}]');
    }

    $query = $db->select_all("users", ["email" => "$email", "password" => "$password_encrypt"], "LIMIT 1");
    if (($user = $db->fetch($query))) {
        $db->free($query);

        if ($user['active'] == 1) {
            if ($user['disable'] == 1) {
                die('[{"status": "error", "msg": "' . $LNG['L_SM_E_DISABLE'] . '"}]');
            } else {
                $sm->setUserSession($user, $rememberme);
                die('[{"status": "ok", "msg": "' . $cfg['WEB_URL'] . '"}]');
            }
        } else {
            if ($user['active'] == 0 || $user['active'] > 1) { //-1 disable by admin not send email
                $mail_msg = SMBasic_create_reg_mail($user['active']);
                mail($user['email'], $LNG['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
            }
            die('[{"status": "error", "msg": "' . $LNG['L_ACCOUNT_INACTIVE'] . '"}]');
        }
    } else {
        die('[{"status": "error", "msg": "' . $LNG['L_E_EMAILPASSWORD'] . '"}]');
        //die('[{"status": "error", "msg": "' . $password_encrypt . '"}]');
    }
}

function SMBasic_user_activate_account($activation_code) {
    global $db;

    $activation_code = $db->escape_strip($activation_code);

    $query = $db->select_all("users", ["active" => $activation_code], "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }

    $db->update("users", ["active" => 1], ["active" => $activation_code]);

    return true;
}

function SMBasic_RequestResetOrActivation() {
    global $LNG, $cfg, $db, $filter;

    if (($email = $filter->post_email("email")) == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL'] . '"}]');
        return false;
    }
    if (strlen($email) > $cfg['smbasic_max_email']) {
        die('[{"status": "1", "msg": "' . $LNG['L_EMAIL_LONG'] . '"}]');
        return false;
    }
    $query = $db->select_all("users", ["email" => "$email"], "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_EMAIL_NOEXISTS'] . '"}]');
    } else {
        $user = $db->fetch($query);
        if ($user['active'] > 1) {
            $mail_msg = SMBasic_create_reg_mail($user['active']);
            mail($email, $LNG['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
            die('[{"status": "2", "msg": "' . $LNG['L_ACTIVATION_EMAIL'] . '"}]');
        } else {
            $reset = mt_rand(11111111, 2147483647);
            $db->update("users", ["reset" => "$reset"], ["email" => $db->escape_strip($email)]);
            $URL = $cfg['WEB_URL'] . "login" . "&reset=$reset&email=$email";
            $msg = $LNG['L_RESET_EMAIL_MSG'] . "\n" . "$URL";
            mail($email, $LNG['L_RESET_EMAIL_SUBJECT'], $msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
            die('[{"status": "2", "msg": "' . $LNG['L_RESET_EMAIL'] . '"}]');
        }
    }

    return false;
}

function SMBasic_user_reset_password() {
    global $cfg, $LNG, $db, $filter, $sm, $frontend;

    $reset = $filter->get_int('reset', 1, 1);
    $email = $filter->get_email('email');
    if ($reset == false || $email == false) {
        return false;
    }
    $query = $db->select_all("users", ["email" => "$email", "reset" => "$reset"]);
    if ($db->num_rows($query) > 0) {
        $user = $db->fetch($query);
        $password = SMBasic_randomPassword();
        $password_encrypted = $sm->encryptPassword($password);
        $db->update("users", ["password" => "$password_encrypted", "reset" => "0"], ["uid" => "{$user['uid']}"]);
        $URL = "{$cfg['WEB_URL']}" . "login";
        $msg = $LNG['L_RESET_SEND_NEWMAIL_MSG'] . "\n" . "$password\n" . "$URL";
        mail($email, $LNG['L_RESET_SEND_NEWMAIL_SUBJECT'], $msg, "From: {$cfg['smbasic_register_reply_email']} \r\n");
        $frontend->messageBox(['msg' => 'L_RESET_PASSWORD_SUCCESS']);
        return true;
    } else {
        return false;
    }
}

function SMBasic_randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

    return implode($pass);
}

function SMBasic_LoginScripts() {
    global $tpl;

    $tpl->addScriptFile("standard", "jquery", "TOP");
    $tpl->addScriptFile("SMBasic", "login", "BOTTOM");
}
