<?php

/**
 *  News - News Perm file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function news_perm_ask($perms) {
    global $sm, $acl_auth;

    $user = $sm->getSessionUser();

    if ($user['isFounder']) {
        return true;
    }

    if (defined('ACL')) {
        return $acl_auth->acl_ask($perms);
    } else {
        return news_noacl_ask($perms);
    }
}

function news_noacl_ask($perms_demand) {
    global $sm, $debug;

    $user = $sm->getSessionUser();

    if ($user['isFounder'] || $user['isAdmin']) {
        return true;
    }

    if (preg_match("/\|\|/", $perms_demand)) {
        $or_split = preg_split("/\|\|/", $perms_demand);
    } else {
        $or_split[] = $perms_demand;
    }


    foreach ($or_split as $or_split_perm) {
        $auth = false;
        if (!preg_match("/\&\&/", $or_split_perm)) {
            if (empty($user)) {
                $auth = news_noacl_anon_check($or_split_perm);
            } else {
                $auth = news_noacl_user_check($or_split_perm);
            }
            defined('DEBUG') ? $debug->log("News NOACL 1 {$or_split_perm} result->{$auth} ", 'News', 'DEBUG') : false;
            if ($auth) {
                defined('DEBUG') ? $debug->log('News NOACL result OR ->true', 'News', 'NOTICE') : false;
                return true;
            } //first OR true, no need check the others
        } else { //&& check all except if any its false
            $and_split = preg_split("/\&\&/", $or_split_perm);

            foreach ($and_split as $and_split_perm) {
                if (empty($user)) {
                    return news_noacl_anon_check($and_split_perm);
                } else {
                    return news_noacl_user_check($and_split_perm);
                }
                defined('DEBUG') ? $debug->log("News NOACL 3 -> \"$and_split_perm\" -> $auth  ", 'News', 'DEBUG') : false;
                if ($auth == false) {
                    defined('DEBUG') ? $debug->log("News NOACL 4 -> \"$and_split_perm\" -> Break", 'News', 'DEBUG') : false;
                    break; //if any && perm its false, not check the next perms are false
                }
            }
        }

        if ($auth == true) {
            defined('DEBUG') ? $debug->log('NEWS NOACL result AND->true ', 'News', 'NOTICE') : false;
            return true;
        } else {
            defined('DEBUG') ? $debug->log('NEWS NOACL F result->false', 'News', 'DEBUG') : false;
        }
    }

    return false;
}

function news_noacl_user_check($perm) {
    global $cfg;

    if ($perm == 'w_news_create' && ($cfg['news_allow_submit_anon'] || $cfg['news_allow_submit_users'])) {
        return true;
    }
    if ($perm == 'r_news_view' && ($cfg['news_view_user'] || $cfg['news_view_anon'])) {
        return true;
    }

    if ($perm == 'w_news_translate' && ($cfg['news_anon_translate'] || $cfg['news_users_translate'])) {
        return true;
    }
    if ($perm == 'w_news_own_translate' && ($cfg['news_translate_own_news'] || $cfg['news_users_translate'] || $cfg['news_anon_translate'] )) {
        return true;
    }

    if ($perm == 'w_news_edit_own' && $cfg['news_allow_users_edit_own_news']) {
        return true;
    }

    if ($perm == 'w_news_delete' && $cfg['news_allow_users_delete']) {
        return true;
    }
    if ($perm == 'w_news_delete_own' && $cfg['news_allow_users_delete_own_news']) {
        return true;
    }
    if ($perm == 'w_news_add_source' && $cfg['add_news_source']) {
        return true;
    }
    if ($perm == 'w_news_add_related' && $cfg['add_news_related']) {
        return true;
    }
    return false;
}

function news_noacl_anon_check($perm) {
    global $cfg;

    if ($perm == 'w_news_create' && ($cfg['news_allow_submit_anon'])) {
        return true;
    }
    if ($perm == 'r_news_view' && ($cfg['news_view_anon'])) {
        return true;
    }
    if ($perm == 'w_news_translate' && $cfg['news_anon_translate']) {
        return true;
    }

    return false;
}
