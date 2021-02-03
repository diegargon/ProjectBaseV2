<?php

/**
 *  SMBasic profile view include file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_ProfileView() {
    global $tpl, $sm, $filter, $frontend, $timeUtil;

    $uid = $filter->getInt('viewprofile');
    if (empty($uid)) {
        $frontend->messageBox(['msg' => 'L_SM_E_USER_NOT_EXISTS']);
        return false;
    }
    $v_user = $sm->getUserByID($uid);
    if ($v_user) {
        $v_user['regdate'] = $timeUtil->formatDbDate($v_user['regdate']);
        $v_user['last_login'] = $timeUtil->formatDbDate($v_user['last_login']);
        $v_user['BACKLINK'] = $filter->srvReferer();
        $tpl->getCssFile('SMBasic');
        $tpl->getCssFile('SMBasic', 'SMBasic-mobile');
        $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SMBasic', 'viewprofile', $v_user));
    } else {
        $frontend->messageBox(['msg' => 'L_SM_E_USER_NOT_EXISTS']);
        return false;
    }
}

function SMBasic_ProfileEdit($user) {
    global $cfg, $tpl;

    $tpl->getCssFile('SMBasic');
    $tpl->getCssFile('SMBasic', 'SMBasic-mobile');
    $tpl->addScriptFile('standard', 'jquery', 'BOTTOM');
    $tpl->addScriptFile('SMBasic', 'profile', 'BOTTOM');
    $tpl->addScriptFile('SMBasic', 'dropdown', 'BOTTOM');

    $form_data = [
        'username' => $user['username'],
        'email' => $user['email'],
    ];
    (empty($user['avatar'])) ? $form_data['avatar'] = $cfg['STATIC_SRV_URL'] . $cfg['smbasic_default_img_avatar'] : $form_data['avatar'] = $user['avatar'];
    do_action('SMBasic_ProfileEdit', $form_data);
    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SMBasic', 'profile', $form_data));

    return true;
}

function SMBasic_ProfileChange() {
    global $LNG, $cfg, $db, $sm, $filter;

    if (empty($_POST['cur_password']) || strlen($_POST['cur_password']) < $cfg['smbasic_min_password']) {
        die('[{"status": "1", "msg": "' . $LNG['L_E_PASSWORD_EMPTY_SHORT'] . '"}]');
    }
    if (!$password = $filter->postPassword("cur_password", $cfg['smbasic_max_password'], $cfg['smbasic_min_password'])) {
        die('[{"status": "2", "msg": "' . $LNG['L_E_PASSWORD'] . '"}]');
    }

    $password_encrypted = $sm->encryptPassword($password);

    $user = $sm->getSessionUser();
    if (empty($user)) {
        die('[{"status": "0", "msg": "' . $LNG['L_E_INTERNAL'] . '"}]');
    }
    //Check USER password
    $query = $db->selectAll("users", ["uid" => $user['uid'], "password" => "$password_encrypted"], "LIMIT 1");
    if ($db->numRows($query) <= 0) {
        die('[{"status": "2", "msg": "' . $LNG['L_WRONG_PASSWORD'] . '"}]');
    }

    $q_set_ary = [];

    if (!empty($_POST['avatar'])) {
        $avatar = $filter->valMedia($_POST['avatar'], 255, 1);
        if ($avatar < 0) {
            die('[{"status": "6", "msg": "' . $LNG['L_SM_E_AVATAR'] . '"}]');
        } else {
            if ($cfg['smbasic_https_remote_avatar']) {
                if (strpos($avatar, "https") === false) {
                    die('[{"status": "6", "msg": "' . $LNG['L_SM_E_HTTPS'] . $avatar . '"}]');
                }
            }
            $user['avatar'] != $avatar ? $q_set_ary['avatar'] = $db->escapeStrip(urldecode($avatar)) : null;
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
        if (($new_password = $filter->postPassword("new_password", $cfg['smbasic_max_password'], $cfg['smbasic_min_password'])) != false) {
            $new_password_encrypt = $sm->encryptPassword($new_password);
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
            if (($username = $filter->postUsername("username", $cfg['smbasic_max_username'], $cfg['smbasic_min_username'])) == false) {
                die('[{"status": "4", "msg": "' . $LNG['L_USERNAME_CHARS'] . '"}]');
            }
            if ($user['username'] != $username && !empty($username)) {
                $query = $db->selectAll("users", ["username" => "$username"], "LIMIT 1");
                if ($db->numRows($query) > 0) {
                    die('[{"status": "4", "msg": "' . $LNG['L_E_USERNAME_EXISTS'] . '"}]');
                } else {
                    $q_set_ary['username'] = $db->escapeStrip($username);
                }
            }
        }
    }

    if (( $cfg['smbasic_can_change_email'] == 1)) {
        if (($email = $filter->postEmail("email")) == false) {
            die('[{"status": "4", "msg": "' . $LNG['L_E_EMAIL'] . '"}]');
        }
        if (strlen($email) > $cfg['smbasic_max_email']) {
            die('[{"status": "4", "msg": "' . $LNG['L_EMAIL_LONG'] . '"}]');
        }
        if ($email != $user['email']) {
            $query = $db->selectAll("users", ["email" => "$email"], "LIMIT 1");
            if ($db->numRows($query) > 0) {
                die('[{"status": "5", "msg": "' . $LNG['L_E_EMAIL_EXISTS'] . '"}]');
            } else {
                $q_set_ary["email"] = $db->escapeStrip($email);
            }
        }
    }

    do_action('SMBasic_ProfileChange', $q_set_ary);

    !empty($q_set_ary) ? $db->update('users', $q_set_ary, ['uid' => $user['uid']], 'LIMIT 1') : null;

    die('[{"status": "ok", "msg": "' . $LNG['L_UPDATE_SUCCESSFUL'] . '", "url": "' . $filter->srvRequestUri() . '"}]');
}
