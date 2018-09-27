<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once 'includes/news_form_common.php';
require_once 'includes/news_submit.inc.php';

$news_perms = get_news_perms("new_submit", null);

if (!$news_perms['news_submit_new']) {
    $frontend->messageBox(["msg" => "L_E_NOACCESS"]);
    return false;
}

if (!empty($_POST['submitForm'])) {
    news_submit_new_process();
} else {
    $tpl->getCssFile("News");
    $tpl->getCssFile("News", "News-mobile");
    $tpl->getCssFile("MiniEditor");
    $tpl->addScriptFile("standard", "jquery", "TOP", null);
    $tpl->addScriptFile("MiniEditor", "editor");
    $tpl->addScriptFile("News", "newsform");
    news_new_form($news_perms);
}
