<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_navLogReg() {
    global $cfg, $LNG, $sm;

    $user = $sm->getSessionUser();

    $elements = "";
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

    if ($user) {
        $elements .= "<li class='nav_right'><a href='$logout_url'>{$LNG['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='$profile_url'>" . $user['username'] . "</a></li>\n";
        if (!empty($user['avatar'])) {
            $elements .= "<li class='nav_right zero'><a href='$profile_url'><img src=" . $user['avatar'] . " /></a></li>";
        }
    } else {
        $elements .= "<li class='nav_right'><a href='$login_url'>{$LNG['L_LOGIN']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='$register_url'>{$LNG['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

    function setSessionDebugDetails() {
        global $db, $sm, $tUtil, $debug;

        $debug->log("Session Details", "SMBasic", "DEBUG");
        $debug->log("Time Now: " . $tUtil->format_date(time(), true) . "", "SMBasic", "DEBUG");
        if (!($user = $sm->getSessionUser())) {
            $debug->log("Anonymous Session", "SMBasic", "DEBUG");
            return false;
        }

        if (isset($_SESSION)) {
            if (!empty($sm->getData("uid"))) {
                $debug->log("Session VAR ID:" . $sm->getData("uid"), "SMBasic", "DEBUG");
            }
        } else {
            $debug->log("Session ins't set", "SMBasic", "DEBUG");
        }

        $query = $db->select_all("sessions", array("session_uid" => "{$user['uid']}"), "LIMIT 1");
        $session = $db->fetch($query);
        if ($session) {

            $debug->log("Session DB IP: {$session['session_ip']}", "SMBasic", "DEBUG");
            $debug->log("Session DB Browser: {$session['session_browser']}", "SMBasic", "DEBUG");
            $debug->log("Session DB Create: {$session['session_created']}", "SMBasic");
            $debug->log("Session DB Expire:" . $tUtil->format_date("{$session['session_expire']}", true) . "", "SMBasic", "DEBUG");
        }
        $debug->log("PHP Session expire: " . ini_get('session.gc_maxlifetime'), "SMBasic", "DEBUG");
        if (isset($_COOKIE)) {
            $debug->log("Cookie State is set", "SMBasic", "DEBUG");
            foreach ($_COOKIE as $key => $val) {
                $debug->log("Cookie array $key -> $val", "SMBasic", "DEBUG");
            }
        } else {
            $debug->log("Cookie not set", "SMBasic", "DEBUG");
        }
        $user = $sm->getSessionUSer();
        if ($user) {
            $debug->log("User ID: {$user['uid']}", "SMBasic", "DEBUG");
            $debug->log("Username: {$user['username']}", "SMBasic", "DEBUG");
            $debug->log("isAdmin: {$user['isAdmin']}", "SMBasic", "DEBUG");
        }
    }
    

/*

function SMBasic_create_reg_mail($active) {
    global $LNG, $cfg;

    if ($active > 1) {
        if ($cfg['FRIENDLY_URL']) {
            $URL = $cfg['WEB_URL'] . "login&active=$active";
        } else {
            $URL = $cfg['CON_FILE'] . "?module=SMBasic&page=login&active=$active";
        }
        $msg = $LNG['L_REG_EMAIL_MSG_ACTIVE'] . "\n" . "$URL";
    } else {
        if ($cfg['FRIENDLY_URL']) {
            $URL = $cfg['WEB_URL'] . "login";
        } else {
            $URL = $cfg['CON_FILE'] . "?module=SMBasic&page=login";
        }
        $msg = $LNG['L_REG_EMAIL_MSG_WELCOME'] . "\n" . "$URL";
    }
    return $msg;
}


*/