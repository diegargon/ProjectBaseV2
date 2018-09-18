<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!$plugins->express_start_provider("EDITOR")) {
    $frontend->message_box(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}
$editor = new Editor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['editor_preview'])) {
        $editor->preview();
        die();
    }
}

$news_nid = $filter->get_int("nid", 11, 1);
$news_lang_id = $filter->get_int("news_lang_id", 4, 1);
$news_page = $filter->get_int("npage", 11, 1);

if (empty($news_nid) || empty($news_lang_id) || empty($news_page)) {
    $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    return false;
}

require_once("includes/news_common.php");
require_once ("includes/news_form_common.php");
if (!isset($_POST['submitForm'])) {

    if (!$plugins->express_start_provider("CATS")) {
        $frontend->message_box(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }

    $tpl->getCSS_filePath("News");
    $tpl->getCSS_filePath("MiniEditor");
    $tpl->getCSS_filePath("News", "News-mobile");
    $tpl->AddScriptFile("standard", "jquery", "TOP", null);
    $tpl->AddScriptFile("MiniEditor", "editor");
    $tpl->AddScriptFile("News", "newsform");
}

if (!empty($_GET['newsedit'])) {
    do_action("begin_newsedit");
    require_once ("includes/news_page_edit.php");

    if (!empty($_POST['submitForm'])) {
        news_form_edit_process();
    } else {
        news_edit($news_nid, $news_lang_id, $news_page);
    }

    return;
}

if (defined('MULTILANG') && !empty($_GET['news_new_lang'])) {
    do_action("begin_news_new_lang");
    require_once ("includes/news_new_lang.php");

    if (!empty($_POST['submitForm'])) {
        news_form_newlang_process();
    } else {
        news_new_lang($news_nid, $news_lang_id, $news_page);
    }

    return;
}

if (!empty($_GET['newpage'])) {
    do_action("begin_newspage");
    require_once ("includes/news_new_page.php");

    if (!empty($_POST['submitForm'])) {
        news_newpage_form_process();
    } else {
        news_new_page($news_nid, $news_lang_id, $news_page);
    }

    return;
} 
