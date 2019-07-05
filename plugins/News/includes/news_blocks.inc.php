<?php

/**
 *  News - News block template
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function news_block($block_conf) {
    global $tpl, $plugins, $frontend;

    require_once (__DIR__ . '/news_common.php');

    if (!empty($block_conf['headlines']) || !empty($block_conf['lead'])) { //we going to show news['text'] need editor
        if (!($plugins->expressStartProvider('EDITOR'))) {
            $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
            return false;
        }
    }
    if (!($plugins->expressStartProvider('CATS'))) {
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

    if (!empty($blocks_data)) {
        $block_conf = $blocks_data['blockconf'];
        $content['config'] = $block_conf;
        $_blockconf = unserialize($block_conf);
        $news_data['category'] = $_blockconf['news_cat'];
        $form_data['block_title'] = $_blockconf['block_title'];
        !empty($_blockconf['featured']) && $_blockconf['featured'] == 'on' ? $form_data['featured_chk'] = 1 : null;
        !empty($_blockconf['frontpage']) && $_blockconf['frontpage'] == 'on' ? $form_data['frontpage_chk'] = 1 : null;
        !empty($_blockconf['childs']) && $_blockconf['childs'] == 'on' ? $form_data['childs_chk'] = 1 : null;
        if ($_blockconf['news_type'] == 'lead') {
            $form_data['lead_sel'] = 1;
        } else if ($_blockconf['news_type'] == 'head') {
            $form_data['head_sel'] = 1;
        } else if ($_blockconf['news_type'] == 'full') {
            $form_data['full_sel'] = 1;
        }

        $form_data['categories_select'] = news_getCatsSelect($news_data, 'block_conf[news_cat]');

        if (defined('MULTILANG')) {
            global $ml;
            $langs = $ml->getSiteLangs();
            if (!empty($langs) && count($langs) > 0) {
                $form_data['lang_select'] = '<select name="block_conf[news_lang]" id="news_lang">';
                foreach ($langs as $lang) {
                    if ($lang['lang_id'] == $_blockconf['news_lang']) {
                        $form_data['lang_select'] .= "<option selected value='{$lang['lang_id']}'>{$lang['lang_name']}</option>";
                    } else {
                        $form_data['lang_select'] .= "<option value='{$lang['lang_id']}'>{$lang['lang_name']}</option>";
                    }
                }
                $form_data['lang_select'] .= '</select>';
            }
        }
        $form_data['limits'] = '';
        for ($i = 1; $i <= $cfg['news_dflt_getnews_limit']; $i++) {
            if ($_blockconf['limits'] == $i) {
                $form_data['limits'] .= '<option selected value="' . $i . '">' . $i . '</option>';
            } else {
                $form_data['limits'] .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        $content['content'] = $tpl->getTplFile('News', 'news_block_conf', $form_data);
        $content['config'] = $block_conf;
    } else {
        $block_conf = $filter->postArray('block_conf', 255, 1);
        $block_conf['admin_block'] = 0;

        $form_data['categories_select'] = news_getCatsSelect(null, 'block_conf[news_cat]');

        if (defined('MULTILANG')) {
            global $ml;
            $langs = $ml->getSiteLangs();
            if (!empty($langs) && count($langs) > 0) {
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
    }
    return $content;
}
