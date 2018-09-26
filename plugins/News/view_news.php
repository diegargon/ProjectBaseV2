<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia	
 */
!defined('IN_WEB') ? exit : true;

require_once("includes/news_common.php");
require_once("includes/news_view.php");

do_action("news_page_begin");

if (!($plugins->express_start_provider("EDITOR")) || !($plugins->express_start_provider("CATS"))) {
    $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}

if ($plugins->check_enabled('NewsComments')) {
    $plugins->express_start('NewsComments');
}

$tpl->getCSS_filePath("News");
$tpl->getCSS_filePath("News", "News-mobile");

do_action("begin_newsshow");

news_show_page();

