<?php

/**
 *  SMBasic main include file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
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
    global $cfg, $sm, $frontend, $tpl;

    $user = $sm->getSessionUser();

    if ($cfg['FRIENDLY_URL']) {
        $login_url = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/login";
        $register_url = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/register";
        $profile_url = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/profile";
        $logout_url = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/logout";
    } else {
        $login_url = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=SMBasic&page=login&lang={$cfg['WEB_LANG']}";
        $register_url = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=SMBasic&page=register&lang={$cfg['WEB_LANG']}";
        $profile_url = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=SMBasic&page=profile&lang={$cfg['WEB_LANG']}'";
        $logout_url = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=SMBasic&page=logout&lang={$cfg['WEB_LANG']}";
    }

    if ($user && $user['uid'] > 0) {
        $frontend->addMenuItem('dropdown_menu', $tpl->getTPLFile('SMBasic', 'sm_menu_opt', ['profile_menu' => 1, 'profile_url' => $profile_url]), 8);
        $frontend->addMenuItem('dropdown_menu', $tpl->getTPLFile('SMBasic', 'sm_menu_opt', ['logout_menu' => 1, 'logout_url' => $logout_url]), 9);
    } else {
        $frontend->addMenuItem('top_menu_right', $tpl->getTPLFile('SMBasic', 'sm_menu_opt', ['login_menu' => 1, 'login_url' => $login_url]), 8);
        $frontend->addMenuItem('top_menu_right', $tpl->getTPLFile('SMBasic', 'sm_menu_opt', ['register_menu' => 1, 'register_url' => $register_url]), 8);
    }

    if ($cfg['smbasic_set_drop_caption'] && $user['uid'] > 0) {
        $tpl->getCssFile('SMBasic');
        $tpl->getCssFile('SMBasic', 'SMBasic-mobile');
        !empty($user['avatar']) ? $menu_data['avatar'] = $user['avatar'] : $menu_data['avatar'] = $cfg['STATIC_SRV_URL'] . '/' .$cfg['smbasic_default_img_avatar'];
        $menu_data['drop_menu_caption'] = 1;
        $menu_data['username'] = $user['username'];
        $frontend->addMenuItem('dropdown_menu_caption', $tpl->getTPLFile('SMBasic', 'sm_menu_opt', $menu_data));
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
            $URL = $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?module=SMBasic&page=login&active=' . $active;
        }
        $msg = $LNG['L_REG_EMAIL_MSG_ACTIVE'] . $URL;
    } else {
        if ($cfg['FRIENDLY_URL']) {
            $URL = $cfg['WEB_URL'] . $cfg['WEB_LANG'] . '/login';
        } else {
            $URL = $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?module=SMBasic&page=login';
        }
        $msg = $LNG['L_REG_EMAIL_MSG_WELCOME'] . $URL;
    }
    return $msg;
}
