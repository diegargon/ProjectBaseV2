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


$q_opt = ['lead' => 1, 'childs' => 1, 'limit' => $cfg['news_section_getnews_limit'], 'main_image' => 1];
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
if (($num_news = count($news_db)) < 1) {
    return $frontend->messageBox(['title' => 'L_NEWS_SEC_EMPTY_TITLE', 'msg' => 'L_NEWS_SEC_EMPTY']);
}

$lnews = layout_news('news_section_article', $news_db);

$content = '';
$cats = [];

foreach ($lnews as $lnews_row) {
    $cats[] = $lnews_row['category'];
}
$cats = array_unique($cats);

$TPL_CTRL = 1;
$TPL_FOOT = 0;

$per_section = $num_news / $cfg['news_section_sections'];
$num_section = 1;
$section = [];
$news_counter = 1;

foreach ($cats as $cat) {
    $cat_news = news_extract_bycat($lnews, $cat);
    $CATHEAD = 1;

    foreach ($cat_news as $cat_news_row) {

        !isset($section[$num_section]) ? $section[$num_section] = '' : false;

        if ($CATHEAD) {
            $section[$num_section] .= '<h2 class="section_head">' . $ctgs->getCatNameByID($cat) . '</h2>';
            $CATHEAD = 0;
        }
        $section[$num_section] .= $cat_news_row['html'];
        $news_counter++;
    }
    $num_section++;
}

$section_data = [];
for ($i = 1; $i <= $cfg['news_section_sections']; $i++) {
    if (!empty($section[$i])) {
        $section_data['section_' . $i] = $section[$i];
    }
}
$section_data['NUM_SECTIONS'] = $cfg['news_section_sections'];
$content .= $tpl->getTplFile('News', 'news_section', $section_data);

$tpl->addtoTplVar('ADD_TO_BODY', $content);
