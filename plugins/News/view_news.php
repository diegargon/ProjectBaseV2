<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)	
 */
!defined('IN_WEB') ? exit : true;

require_once('includes/news_common.php');
require_once('includes/news_view.php');

do_action('news_page_begin');

if (!($plugins->expressStartProvider('EDITOR')) || !($plugins->expressStartProvider('CATS'))) {
    $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}

if ($plugins->checkEnabled('NewsComments')) {
    $plugins->expressStart('NewsComments');
}

if ($plugins->checkEnabled('Multilang')) {
    $plugins->expressStart('Multilang');
}


if ($cfg['news_vote_disable_by_stress'] && is_server_stressed()) {
    
} else {   
    if (!$cfg['ITS_BOT'] && $cfg['news_vote_enable'] &&
            $plugins->checkEnabledProvider('RATINGS') &&
            $plugins->expressStartProvider('RATINGS')
    ) {
        $tpl->addScriptFile('StdRatings', 'rate', 'BOTTOM');
        register_action('news_show_page', 'newsvote_news_addrate');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
                ( ($filter->postStrictChars('rate_section')) == 'news_rate')
        ) {
 
            if (rating_rate_getPost('news_rate')) {
                die('[{"status": "6", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
            } else {
                die('[{"status": "4", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
            }
        }
    }
}

$tpl->getCssFile('News');
$tpl->getCssFile('News', 'News-mobile');

do_action('begin_newsshow');

news_show_page();

