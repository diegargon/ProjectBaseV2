<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once 'includes/news_form_common.php';
require_once 'includes/news_submit.inc.php';

$news_perms = get_news_perms("new_submit", null);

if (!$news_perms['news_submit_new']) {
    $frontend->message_box(["msg" => "L_E_NOACCESS"]);
    return false;
}

if (!empty($_POST['submitForm'])) {
    news_submit_new_process();
} else {
    $tpl->getCSS_filePath("News");
    $tpl->getCSS_filePath("News", "News-mobile");
    $tpl->getCSS_filePath("MiniEditor");
    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("MiniEditor", "editor");
    $tpl->AddScriptFile("News", "newsform");
    news_new_form($news_perms);
}
