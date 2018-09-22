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
/*
 * TODO:  VERY MESSY TEMPLATE LOGIC FIX
 */
$limit = $cfg['news_section_getnews_limit'];

$q_opt = ['lead' => 1, 'childs' => 1, 'limit' => $limit];
$q_where = ['category' => $category_id];

$user = $sm->getSessionUser();

/*
 * 0 or empty mean he select ALL, not set (NULL) mean he not configure, show web lang by default
 */
if (!empty($user['news_lang'])) {
    $q_where['lang_id'] = $user['news_lang'];
} else if (!isset($user['news_lang'])) {
    defined('MULTILANG') ? $lang_id = $ml->get_web_lang_id() : $lang_id = 1;
}

$news_db = get_news_query($q_where, $q_opt);
$num_items = count($news_db);

if ($num_items < $cfg['news_section_getnews_limit']) {
    $per_section = $num_items / $cfg['news_section_sections'];
} else {
    $per_section = $cfg['news_section_getnews_limit'] / $cfg['news_section_sections'];
}

$whole = floor($per_section);
$fraction = $per_section - $whole;

if ($fraction > 0) { // Fraction mean +1 article in first section
    $section_limit = ++$whole;
} else {
    $section_limit = $whole;
}
//echo $num_items . "-" . $whole . "-" . $fraction . "-" . $section_limit;

$counter = 1;
$num_items_section = 1;

$section_data['TPL_CTRL'] = 1;
$section_data['START_SECTION'] = 1;
$section_data['END_SECTION'] = 0;
$section_data['SECTIONS'] = $cfg['news_section_sections'];
$content = '';

foreach ($news_db as $news) {
    $num_items == $counter ? $section_data['TPL_FOOT'] = 1 : $section_data['TPL_FOOT'] = 0;
    ($counter == $section_limit || $counter == $num_items || $num_items_section >= $section_limit ) ? $section_data['END_SECTION'] = 1 : null;
    // echo "<br>" . $counter ."-". $section_limit . "-" . $num_items . "-" . $num_items_section ."<br>";
    //ARTICLE DATA
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news['title']);
        $section_data['url'] = '/' . $cfg['WEB_LANG'] . "/news/{$news['nid']}/{$news['page']}/{$news['lang_id']}/$friendly_title";
    } else {
        $section_data['url'] = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news['page']}&news_lang_id={$news['lang_id']}";
    }
    $section_data['title'] = $news['title'];
    $section_data['lead'] = $news['lead'];
    $section_data['featured'] = $news['featured'];
    $section_data['date'] = format_date($news['created']);
    $content .= $tpl->getTPL_file('News', 'news_section', $section_data);
    //
    $section_data['START_SECTION'] = 0;
    if ($section_data['END_SECTION'] == 1) {
        $section_data['START_SECTION'] = 1;
        $section_data['END_SECTION'] = 0;
        $num_items_section = 1;
    } else {
        $num_items_section++;
    }
    /*
     * first if fraction add +1 news on column then nex colums -1
     * when detect new section by news_items_section set to 1 again reduce the section_limit
     */
    $num_items_section == 1 && $fraction > 0 ? $section_limit-- : null;
    $counter++;
    $section_data['TPL_CTRL'] = $counter;
}

$tpl->addto_tplvar('ADD_TO_BODY', $content);
