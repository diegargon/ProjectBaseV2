<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function news_block($block_conf) {
    global $tpl, $plugins, $cfg, $frontend;

    require_once (__DIR__ . '/news_common.php');

    if (!empty($block_conf['headlines']) || !empty($block_conf['lead'])) { //we going to show news['text'] need editor
        if (!($plugins->express_start_provider('EDITOR'))) {
            $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
            return false;
        }
    }
    if (!($plugins->express_start_provider('CATS'))) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }

    $tpl->getCssFile('News');
    $tpl->getCssFile('News', 'News-mobile');

    isset($block_conf['feautured']) ? $news_where['featured'] = 1 : null;
    isset($block_conf['frontend']) ? $news_where['frontend'] = 1 : null;
    isset($block_conf['childs']) ? $news_conf['childs'] = 1 : $news_conf['childs'] = 0;
    if (isset($block_conf['news_type'])) {
        if ($block_conf['news_type'] == 'headlines') {
            $news_conf['headlines'] = 1;
        } else if ($block_conf['news_type'] == 'lead') {
            $news_conf['lead'] = 1;
        }
    }
    isset($block_conf['limits']) ? $news_conf['limit'] = $block_conf['limits'] : null;

    $news_where['category'] = $block_conf['news_cat'];
    $news_where['lang_id'] = $block_conf['news_lang'];

    $news_db = get_news_query($news_where, $news_conf);
    $content = isset($block_conf['block_title']) ? "<h2>{$block_conf['block_title']}</h2>" : '';

    $lnews = layout_news('news_block', $news_db);
    foreach ($lnews as $lnews_row) {
        $content .= $lnews_row['html'];
    }

    return $content;
}

function news_block_conf($blocks_data = null) {
    global $filter, $cfg, $tpl;
    require_once (__DIR__ . '/news_common.php');
    require_once (__DIR__ . '/news_form_common.php');

    $tpl->getCssFile('News');
    $tpl->getCssFile('News', 'News-mobile');

    $block_conf = $filter->post_array('block_conf', 255, 1);
    $block_conf['admin_block'] = 0;

    $content['config'] = $block_conf;
    $form_data['categories_select'] = news_getCatsSelect(null, 'block_conf[news_cat]');

    if (defined('MULTILANG')) {
        global $ml;
        $langs = $ml->get_site_langs();
        if (count($langs) > 0) {
            $form_data['lang_select'] = '<select name="block_conf[news_lang]" id="news_lang">';
            foreach ($langs as $lang) {
                $form_data['lang_select'] .= "<option value='{$lang['lang_id']}'>{$lang['lang_name']}</option>";
            }
            $form_data['lang_select'] .= '</select>';
        }
    }
    $form_data['limits'] = '';
    for ($i = 1; $i <= $cfg['news_dflt_getnews_limit']; $i++) {
        $form_data['limits'] .= '<option value="' . $i . '">' . $i . '</option>';
    }

    $content['content'] = $tpl->getTplFile('News', 'news_block_conf', $form_data);

    $content['config'] = $block_conf;

    return $content;
}
