<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_ProfileEdit($user) {
    global $cfg, $tpl;


    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    $tpl->AddScriptFile("standard", "jquery", "BOTTOM");
    $tpl->AddScriptFile("SMBasic", "profile", "BOTTOM");

    $form_data = [
        "username" => $user['username'],
        "email" => $user['email'],
    ];
    (empty($user['avatar'])) ? $form_data['avatar'] = $cfg['STATIC_SRV_URL'] . $cfg['smbasic_default_img_avatar'] : $user['avatar'];

    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "profile", $form_data));
}

function SMBasic_ProfileView() {
    global $tpl, $sm, $filter, $frontend;

    $uid = $filter->get_int("viewprofile", 10, 1);
    if (empty($uid)) {
        $frontend->message_box(['msg' => 'L_SM_E_USER_NOT_EXISTS']);
    }
    $v_user = $sm->getUserByID($uid);
    if ($v_user) {
        $tpl->getCSS_filePath("SMBasic");
        $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
        $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "viewprofile", $v_user));
    } else {
        $frontend->message_box(['msg' => 'L_SM_E_USER_NOT_EXISTS']);
    }
}

function SMBasic_ProfileChange() {
    global $LNG, $cfg, $db, $sm, $filter;

    if (empty($_POST['cur_password']) || strlen($_POST['cur_password']) < $cfg['smbasic_min_password']) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_PASSWORD_EMPTY_SHORT'] . '"}]');
    }
    if (!$password = $filter->post_password("cur_password")) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_PASSWORD'] . '"}]');
    }

    $password_encrypted = $sm->encrypt_password($password);

    $user = $sm->getSessionUser();
    if (empty($user)) {
        die('[{"status": "0", "msg": "' . $LNG['L_E_INTERNAL'] . '"}]');
    }
    //Check USER password
    $query = $db->select_all("users", ["uid" => $user['uid'], "password" => "$password_encrypted"], "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        die('[{"status": "2", "msg": "' . $LNG['L_WRONG_PASSWORD'] . '"}]');
    }

    $q_set_ary = [];

    if (!empty($_POST['avatar'])) {
        $avatar = $filter->validate_media($_POST['avatar'], 256);
        if ($avatar < 0) {
            die('[{"status": "6", "msg": "' . $LNG['L_SM_E_AVATAR'] . '"}]');
        } else {
            if ($cfg['smbasic_https_remote_avatar']) {
                if (strpos($avatar, "https") === false) {
                    die('[{"status": "6", "msg": "' . $LNG['L_SM_E_HTTPS'] . $avatar . '"}]');
                }
            }
            $user['avatar'] != $avatar ? $q_set_ary['avatar'] = $db->escape_strip(urldecode($avatar)) : false;
        }
    }

    if ((!empty($_POST['new_password']) && empty($_POST['r_password']) ) ||
            (!empty($_POST['r_password']) && empty($_POST['new_password']) )
    ) {
        die('[{"status": "3", "msg": "' . $LNG['L_E_NEW_BOTH_PASSWORD'] . '"}]');
    }

    if (!empty($_POST['new_password']) && !empty($_POST['r_password'])) {
        if ($_POST['new_password'] != $_POST['r_password']) {
            die('[{"status": "3", "msg": "' . $LNG['L_E_NEW_PASSWORD_NOTMATCH'] . '"}]');
        }
        if ((strlen($_POST['new_password']) < $cfg['smbasic_min_password'])) {
            die('[{"status": "3", "msg": "' . $LNG['L_E_NEWPASS_TOOSHORT'] . '"}]');
        }
        if (($new_password = $filter->post_password("new_password")) != false) {
            $new_password_encrypt = $sm->encrypt_password($new_password);
            $q_set_ary['password'] = $new_password_encrypt;
        }
    }

    if (( $cfg['smbasic_can_change_username'] == 1)) {
        if (empty($_POST['username']) && $cfg['smbasic_need_username'] == 1) {
            die('[{"status": "4", "msg": "' . $LNG['L_USERNAME_EMPTY'] . '"}]');
        } else if (empty($_POST['username']) && $cfg['smbasic_need_username'] == 0) {
            $q_set_ary['username'] = '';
        } else {
            if (strlen($_POST['username']) < $cfg['smbasic_min_username']) {
                die('[{"status": "4", "msg": "' . $LNG['L_USERNAME_SHORT'] . '"}]');
            }
            if (strlen($_POST['username']) > $cfg['smbasic_max_username']) {
                die('[{"status": "4", "msg": "' . $LNG['L_USERNAME_LONG'] . '"}]');
            }
            if (($username = $filter->post_strict_chars("username", $cfg['smbasic_max_username'], $cfg['smbasic_min_username'])) == false) {
                die('[{"status": "4", "msg": "' . $LNG['L_USERNAME_CHARS'] . '"}]');
            }
            if ($user['username'] != $username && !empty($username)) {
                $query = $db->select_all("users", ["username" => "$username"], "LIMIT 1");
                if ($db->num_rows($query) > 0) {
                    die('[{"status": "4", "msg": "' . $LNG['L_E_USERNAME_EXISTS'] . '"}]');
                } else {
                    $q_set_ary['username'] = $db->escape_strip($username);
                }
            }
        }
    }

    if (( $cfg['smbasic_can_change_email'] == 1)) {
        if (($email = $filter->post_email("email")) == false) {
            die('[{"status": "4", "msg": "' . $LNG['L_E_EMAIL'] . '"}]');
        }
        if (strlen($email) > $cfg['smbasic_max_email']) {
            die('[{"status": "4", "msg": "' . $LNG['L_EMAIL_LONG'] . '"}]');
        }
        if ($email != $user['email']) {
            $query = $db->select_all("users", ["email" => "$email"], "LIMIT 1");
            if ($db->num_rows($query) > 0) {
                die('[{"status": "5", "msg": "' . $LNG['L_E_EMAIL_EXISTS'] . '"}]');
            } else {
                $q_set_ary["email"] = $db->escape_strip($email);
            }
        }
    }

    do_action("SMBasic_ProfileChange", $q_set_ary);

    !empty($q_set_ary) ? $db->update("users", $q_set_ary, ["uid" => $user['uid']], "LIMIT 1") : false;

    die('[{"status": "ok", "msg": "' . $LNG['L_UPDATE_SUCCESSFUL'] . '", "url": "' . $filter->srv_request_uri() . '"}]');
}
