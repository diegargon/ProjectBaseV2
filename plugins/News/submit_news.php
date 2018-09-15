<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once 'includes/news_form_common.php';
require_once 'includes/news_submit.inc.php';

$plugins->express_start_provider("EDITOR");
$plugins->express_start_provider("CATS");

$editor = new Editor();

if (!empty($_POST['editor_preview'])) {
    $editor->preview();
    die();
}

if (!empty($_POST['submitForm'])) {
    news_form_submit_process();
} else {
    $tpl->getCSS_filePath("News");
    $tpl->getCSS_filePath("MiniEditor");
    $tpl->getCSS_filePath("News", "News-mobile");
    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("MiniEditor", "editor");
    $tpl->AddScriptFile("News", "newsform");
    news_new_form($editor);
}
