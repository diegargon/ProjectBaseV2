<?php

/*
 *  Copyright @ 2016 Diego Garcia	
 */
!defined('IN_WEB') ? exit : true;

do_action("news_page_begin");

$plugins->express_start_provider("EDITOR");
$plugins->express_start_provider("CATS");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['editor_preview'])) {
        $editor->preview();
        die();
    }
}

if (!empty($_GET['newsedit']) && !empty($_GET['lang_id'])) {
    do_action("begin_newsedit");
    $tpl->getCSS_filePath("News");
    $tpl->getCSS_filePath("MiniEditor");
    $tpl->getCSS_filePath("News", "News-mobile");
    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("MiniEditor", "editor");
    $tpl->AddScriptFile("News", "newsform");


    require_once ("includes/news_page_edit.php");
    require_once ("includes/news_form_common.php");
    require_once("includes/news_common.php");
    if (!empty($_POST['submitForm'])) {
        news_form_edit_process();
    } else {

        require_once("includes/news_form_common.php");
        news_edit();
    }
} else if (defined('MULTILANG') && !empty($_GET['news_new_lang'])) {
    do_action("begin_news_new_lang");
    $plugins->express_start_provider("EDITOR");

    require_once("includes/news_common.php");
    require_once ("includes/news_form_common.php");
    require_once ("includes/news_new_lang.php");
    if (!empty($_POST['submitForm'])) {
        news_form_newlang_process();
    } else {
        $tpl->getCSS_filePath("News");
        $tpl->getCSS_filePath("MiniEditor");
        $tpl->getCSS_filePath("News", "News-mobile");
        $tpl->AddScriptFile("standard", "jquery", "TOP", null);
        $tpl->AddScriptFile("MiniEditor", "editor");
        $tpl->AddScriptFile("News", "newsform");
        news_new_lang();
    }
} else if (!empty($_GET['newpage'])) {
    do_action("begin_newspage");

    require_once("includes/news_common.php");
    if (!empty($_POST['submitForm'])) {
        require_once ("includes/news_form_common.php");
        news_newpage_form_process();
    } else {
        $plugins->express_start_provider("EDITOR");
        $tpl->getCSS_filePath("News");
        $tpl->getCSS_filePath("MiniEditor");
        $tpl->getCSS_filePath("News", "News-mobile");
        $tpl->AddScriptFile("standard", "jquery", "TOP", null);
        $tpl->AddScriptFile("MiniEditor", "editor");
        $tpl->AddScriptFile("News", "newsform");
        require_once ("includes/news_new_page.php");

        news_new_page();
    }
} else {
    require_once("includes/news_common.php");
    require_once("includes/news_main_page.php");
    do_action("begin_newsshow");

    $tpl->getCSS_filePath("News");
    news_show_page();
}
