<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

require_once('includes/news_common.php');

do_action('section_page_begin');

$tpl->getCssFile('News');
$tpl->getCssFile('News', 'News-mobile');

if (!($plugins->expressStartProvider('CATS'))) {
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

$user = $sm->getSessionUser();
if (!empty($user['news_lang'])) {
    $news_langs = unserialize($user['news_lang']);
    $w_langs_ids = '';
    foreach ($news_langs as $langs) {
        !empty($w_langs_ids) ? $w_langs_ids .= ',' : null;
        $w_langs_ids .= $langs;
    }
    $q_where['lang_id'] = ['value' => "({$w_langs_ids})", 'operator' => 'IN'];
} else if (!isset($user['news_lang'])) {
    defined('MULTILANG') ? $lang_id = $ml->getWebLangID() : $lang_id = 1;
}

$news_db = get_news_query($q_where, $q_opt);
if (($num_news = count($news_db)) < 1) {
    return $frontend->messageBox(['title' => 'L_NEWS_SEC_EMPTY_TITLE', 'msg' => 'L_NEWS_SEC_EMPTY']);
}
if ($tpl->checkTplFileExists('News', $cfg['news_section_tpl'] . "-" . $category_id)) {
    $lnews = layout_news($cfg['news_section_tpl'] . "-" . $category_id, $news_db);
} else {
    $lnews = layout_news($cfg['news_section_tpl'], $news_db);
}
$content = '';
$cats = [];

foreach ($lnews as $lnews_row) {
    $cats[] = $lnews_row['category'];
}
$cats = array_unique($cats);

$news_data['news'] = '';

foreach ($cats as $cat) {
    $cat_news = news_extract_bycat($lnews, $cat);
    $CATHEAD = 1;

    foreach ($cat_news as $cat_news_row) {

        if ($CATHEAD) {
            $news_data['news'] .= '<div class="news_category_container"><h2 class="section_head">' . $ctgs->getCatNameByID($cat) . '</h2>';
            if ($cfg['news_section_img'] && !empty($ctgs->getCatURLByID($cat))) {
                $news_data['news'] .= '<div class="news_section_img"><img src="' . $ctgs->getCatURLByID($cat) . '" width="' . $cfg['news_section_img_width'] . '"></div>';
            }
            $CATHEAD = 0;
        }
        $news_data['news'] .= $cat_news_row['html'];
    }
    $news_data['news'] .= '</div>';
}

$content .= $tpl->getTplFile('News', 'news_section', $news_data);

$tpl->addtoTplVar('ADD_TO_BODY', $content);
