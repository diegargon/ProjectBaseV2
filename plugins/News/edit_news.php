<?php

/**
 *  News - Edit news
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if (!$plugins->expressStartProvider('EDITOR')) {
    $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}
$editor = new Editor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['editor_preview'])) {
        $editor->showPreview();
        die();
    }
}

$news_nid = $filter->getInt('nid');
$news_lang_id = $filter->getInt('news_lang_id');
$news_page = $filter->getInt('npage');

if (empty($news_nid) || empty($news_lang_id) || empty($news_page)) {
    $frontend->messageBox(['msg' => 'L_NEWS_NOT_EXIST']);
    return false;
}

require_once ('includes/news_common.php');
require_once ('includes/news_form_common.php');

if ($plugins->checkEnabled('Multilang')) {
    $plugins->expressStart('Multilang');
}

if (!isset($_POST['submitForm'])) {

    if (!$plugins->expressStartProvider('CATS')) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }

    $tpl->getCssFile('News');
    $tpl->getCssFile('MiniEditor');
    $tpl->getCssFile('News', 'News-mobile');
    $tpl->addScriptFile('standard', 'jquery', 'TOP', 0);
    $tpl->addScriptFile('MiniEditor', 'editor');
    $tpl->addScriptFile('News', 'newsform');
    if ($cfg['news_side_scroll']) {
        $tpl->addScriptFile('News', 'news_scroll', 'BOTTOM');
    }
}

if (!empty($_GET['newsedit'])) {
    do_action('begin_newsedit');
    require_once ('includes/news_page_edit.php');

    if (!empty($_POST['submitForm'])) {
        news_form_edit_process();
    } else if (!empty($_POST['btnEditorSave'])) {
        news_save_text_only();
    } else {
        news_edit($news_nid, $news_lang_id, $news_page);
    }
} else if (defined('MULTILANG') && !empty($_GET['news_new_lang'])) {
    do_action('begin_news_new_lang');
    require_once ('includes/news_new_lang.php');

    if (!empty($_POST['submitForm'])) {
        news_form_newlang_process();
    } else {
        news_new_lang($news_nid, $news_lang_id, $news_page);
    }
} else if (!empty($_GET['newpage'])) {
    do_action('begin_news_new_page');
    require_once ('includes/news_new_page.php');

    if (!empty($_POST['submitForm'])) {
        news_newpage_form_process();
    } else {
        news_new_page($news_nid, $news_lang_id, $news_page);
    }
}
