<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once('includes/news_common.php');

do_action('section_page_begin');

$tpl->getCSS_filePath('News');
$tpl->getCSS_filePath('News', 'News-mobile');

if (!($plugins->express_start_provider('CATS'))) {
    $frontend->message_box(['msg' => 'L_E_PL_CANTEXPRESS']);
    return false;
}

if (empty($category_list = $filter->get_UTF8_txt('section')) || preg_match("/\s+/", $category_list)) {
    return $frontend->message_box(['msg' => 'L_NEWS_E_SEC_NOEXISTS']);
}

if (!$category_id = $ctgs->getCatIDbyName_path('News', $category_list)) {
    return $frontend->message_box(['msg' => 'L_NEWS_E_SEC_NOEXISTS']);
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
    return $frontend->message_box(['title' => 'L_NEWS_SEC_EMPTY_TITLE', 'msg' => 'L_NEWS_SEC_EMPTY']);
}
$column = '';
$i = 1;
foreach ($news_db as $news) {
    //ARTICLE DATA
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news['title']);
        $article_data['url'] = '/' . $cfg['WEB_LANG'] . "/news/{$news['nid']}/{$news['page']}/{$news['lang_id']}/$friendly_title";
    } else {
        $article_data['url'] = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news['page']}&news_lang_id={$news['lang_id']}";
    }
    $article_data['title'] = $news['title'];
    $article_data['lead'] = $news['lead'];
    $article_data['featured'] = $news['featured'];
    $article_data['date'] = format_date($news['created']);
    if (!empty($column[$i])) {
        $column[$i] .= $tpl->getTPL_file('News', 'news_section_article', $article_data);
    } else {
        $column[$i] = $tpl->getTPL_file('News', 'news_section_article', $article_data);
    }
    //
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
        $content .= $tpl->getTPL_file('News', 'news_section', $section_data);
        $section_data['TPL_CTRL'] ++;
    }
}

$tpl->addto_tplvar('ADD_TO_BODY', $content);
