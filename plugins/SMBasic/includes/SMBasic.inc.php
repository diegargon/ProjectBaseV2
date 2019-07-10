<?php

/**
 *  SMBasic main include file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Show options for login and register in the top navigation
 * 
 * @global array $cfg
 * @global array $LNG
 * @global SimpleFrontend $frontend 
 * @global SessionManager $sm
 * @return string
 */
function SMBasic_SetTopNavUserMenu() {
    global $cfg, $LNG, $sm, $frontend;

    $user = $sm->getSessionUser();

    if ($cfg['FRIENDLY_URL']) {
        $login_url = "/{$cfg['WEB_LANG']}/login";
        $register_url = "/{$cfg['WEB_LANG']}/register";
        $profile_url = "/{$cfg['WEB_LANG']}/profile";
        $logout_url = "/{$cfg['WEB_LANG']}/logout";
    } else {
        $login_url = "/{$cfg['CON_FILE']}?module=SMBasic&page=login&lang={$cfg['WEB_LANG']}";
        $register_url = "/{$cfg['CON_FILE']}?module=SMBasic&page=register&lang={$cfg['WEB_LANG']}";
        $profile_url = "/{$cfg['CON_FILE']}?module=SMBasic&page=profile&lang={$cfg['WEB_LANG']}'";
        $logout_url = "/{$cfg['CON_FILE']}?module=SMBasic&page=logout&lang={$cfg['WEB_LANG']}";
    }

    if ($user && $user['uid'] > 0) {
        $logout = "<div class='drop_nav_top'><a class='header-menu-link' href='$logout_url' rel='nofollow'>{$LNG['L_LOGOUT']}</a></div>";
        $profile = "<div class='drop_nav_top'><a class='header-menu-link' href='$profile_url' rel='nofollow'>{$LNG['L_PROFILE']}</a></div>";

        $frontend->addTopDropMenu($logout, 10);
        $frontend->addTopDropMenu($profile, 9);
    } else {
        $login = "<div class='nav_top'><a class='header-menu-link' href='$login_url' rel='nofollow'>{$LNG['L_LOGIN']}</a></div>";
        $register = "<div class='nav_top'><a class='header-menu-link' href='$register_url' rel='nofollow'>{$LNG['L_REGISTER']}</a></div>";
        $frontend->addTopMenu($login, 2);
        $frontend->addTopMenu($register, 2);
    }

    if ($cfg['smbasic_set_drop_caption']) {
        !empty($user['avatar']) ? $avatar = $user['avatar'] : $avatar = $cfg['smbasic_default_img_avatar'];
        $caption = '<div class="drop_nav_top"><img src="' . $avatar . '"/><span class="drop_btn_caption">' . $user['username'] . '</span></div>';
        $frontend->setDropMenuCaption($caption);
    }

    return true;
}

/**
 * Print to debug session debug details
 * @global Database $db
 * @global SessionManager $sm
 * @global array $debug
 * @global TimeUtil $timeUtil
 * @return boolean
 */
function setSessionDebugDetails() {
    global $db, $sm, $debug, $timeUtil;

    $debug->log('Session Details', 'SMBasic', 'DEBUG');
    $debug->log('Time Now: ' . $timeUtil->getTimeNow(), 'SMBasic', 'DEBUG');
    $debug->log('Timezone: ' . date_default_timezone_get(), 'SMBasic', 'DEBUG');
    if (!($user = $sm->getSessionUser())) {
        $debug->log('Anonymous Session', 'SMBasic', 'DEBUG');
        return false;
    }

    if (isset($_SESSION)) {
        if (!empty($sm->getData('uid'))) {
            $debug->log('Session VAR ID:' . $sm->getData('uid'), 'SMBasic', 'DEBUG');
        }
    } else {
        $debug->log('Session ins\'t set', 'SMBasic', 'DEBUG');
    }

    $query = $db->selectAll('sessions', ['session_uid' => $user['uid']], 'LIMIT 1');
    $session = $db->fetch($query);
    if ($session) {

        $debug->log('Session DB IP: ' . $session['session_ip'], 'SMBasic', 'DEBUG');
        $debug->log('Session DB Browser: ' . $session['session_browser'], 'SMBasic', 'DEBUG');
        $debug->log('Session DB Create: ' . $session['session_created'], 'SMBasic', 'DEBUG');
        $debug->log("Session DB Expire:" . $timeUtil->timestampToDate($session['session_expire']), 'SMBasic', 'DEBUG');
    }
    $debug->log('PHP Session expire: ' . ini_get('session.gc_maxlifetime'), 'SMBasic', 'DEBUG');
    if (isset($_COOKIE)) {
        $debug->log('Cookie State is set', 'SMBasic', 'DEBUG');
        foreach ($_COOKIE as $key => $val) {
            $debug->log("Cookie array $key -> $val", 'SMBasic', 'DEBUG');
        }
    } else {
        $debug->log('Cookie not set', 'SMBasic', 'DEBUG');
    }
    $user = $sm->getSessionUSer();
    if ($user) {
        $debug->log('User ID: ' . $user['uid'], 'SMBasic', 'DEBUG');
        $debug->log('Username: ' . $user['username'], 'SMBasic', 'DEBUG');
        $debug->log('isFounder: ' . $user['isFounder'], 'SMBasic', 'DEBUG');
        $debug->log('isAdmin: ' . $user['isAdmin'], 'SMBasic', 'DEBUG');
    }
}

/**
 * Creation a email for send after register or resend when not active
 * 
 * @global array $LNG
 * @global array $cfg
 * @param  int $active
 * @return string
 */
function SMBasic_create_reg_mail($active) {
    global $LNG, $cfg;

    if ($active > 1) {
        if ($cfg['FRIENDLY_URL']) {
            $URL = $cfg['WEB_URL'] . $cfg['WEB_LANG'] . '/login&active=' . $active;
        } else {
            $URL = $cfg['CON_FILE'] . '?module=SMBasic&page=login&active=' . $active;
        }
        $msg = $LNG['L_REG_EMAIL_MSG_ACTIVE'] . $URL;
    } else {
        if ($cfg['FRIENDLY_URL']) {
            $URL = $cfg['WEB_URL'] . $cfg['WEB_LANG'] . '/login';
        } else {
            $URL = $cfg['CON_FILE'] . '?module=SMBasic&page=login';
        }
        $msg = $LNG['L_REG_EMAIL_MSG_WELCOME'] . $URL;
    }
    return $msg;
}
