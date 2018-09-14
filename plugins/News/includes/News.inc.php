<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function submit_news_menu() {
    global $LNG, $cfg;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($cfg['FRIENDLY_URL']) {
        $data .= "{$cfg['WEB_LANG']}/submit_news";
    } else {
        $data .= "{$cfg['CON_FILE']}?module=News&page=submit_news&lang={$cfg['WEB_LANG']}";
    }
    $data .= "'>" . $LNG['L_SUBMIT_NEWS'] . "</a>";
    $data .= "</li>";

    return $data;
}
