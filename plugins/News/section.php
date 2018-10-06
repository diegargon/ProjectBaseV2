<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once('includes/news_common.php');

do_action('section_page_begin');

$tpl->getCssFile('News');
$tpl->getCssFile('News', 'News-mobile');

if (!($plugins->express_start_provider('CATS'))) {
    $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}

if (empty($category_list = $filter->get_UTF8_txt('section')) || preg_match("/\s+/", $category_list)) {
    return $frontend->messageBox(['msg' => 'L_NEWS_E_SEC_NOEXISTS']);
}

if (!$category_id = $ctgs->getCatIdByNamePath('News', $category_list)) {
    return $frontend->messageBox(['msg' => 'L_NEWS_E_SEC_NOEXISTS']);
}

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ': ' . $category_list;
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ': ' . $category_list;
//END HEAD MOD


$q_opt = ['lead' => 1, 'childs' => 1, 'limit' => $cfg['news_section_getnews_limit']];
$q_where = ['category' => $category_id];

/*
 * 0 or empty mean he select ALL, not set (NULL) mean he not configure, show web lang by default
 */

$user = $sm->getSessionUser();
if (!empty($user['news_lang'])) {
    $q_where['lang_id'] = $user['news_lang'];
} else if (!isset($user['news_lang'])) {
    defined('MULTILANG') ? $lang_id = $ml->get_web_lang_id() : $lang_id = 1;
}

$news_db = get_news_query($q_where, $q_opt);
if (count($news_db) < 1) {
    return $frontend->messageBox(['title' => 'L_NEWS_SEC_EMPTY_TITLE', 'msg' => 'L_NEWS_SEC_EMPTY']);
}

$lnews = layout_news('news_section_article', $news_db);

$column = [];
$i = 1;

foreach ($lnews as $lnews_row) {
    if (!empty($column[$i])) {
        $column[$i] .= $lnews_row['html'];
    } else {
        $column[$i] = $lnews_row['html'];
    }

    $i++;
    $i > $cfg['news_section_sections'] ? $i = 1 : null;
}

$content = '';

$section_data['TPL_CTRL'] = 1;
$section_data['TPL_FOOT'] = 0;
$section_data['NUM_SECTIONS'] = $cfg['news_section_sections'];

for ($i = 1; $i <= $cfg['news_section_sections']; $i++) {
    if (!empty($column[$i])) {
        $section_data[$i] = $column[$i];
        $i == $cfg['news_section_sections'] ? $section_data['TPL_FOOT'] = 1 : null;
        $content .= $tpl->getTplFile('News', 'news_section', $section_data);
        $section_data['TPL_CTRL'] ++;
    }
}

$tpl->addtoTplVar('ADD_TO_BODY', $content);
