<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_block($block_conf) {
    global $tpl, $plugins, $tUtil, $cfg;

    require_once __DIR__ . '/news_common.php';

    $plugins->express_start_provider("EDITOR");
    $plugins->express_start_provider("CATS");
    $tpl->getCSS_filePath("News");
    $tpl->getCSS_filePath("News", "News-mobile");

    isset($block_conf['feautured']) ? $news_where['featured'] = 1 : null;
    isset($block_conf['frontend']) ? $news_where['frontend'] = 1 : null;
    isset($block_conf['childs']) ? $news_conf['get_childs'] = 1 : $news_conf['get_childs'] = 0;
    if (isset($block_conf['news_type'])) {
        if ($block_conf['news_type'] == "headlines") {
            $news_conf['headlines'] = 1;
        } else if ($block_conf['news_type'] == "lead") {
            $news_conf['lead'] = 1;
        }
    }
    isset($block_conf['limits']) ? $news_conf['limit'] = $block_conf['limits'] : null;

    $news_where['category'] = $block_conf['news_cat'];
    $news_where['lang_id'] = $block_conf['news_lang'];

    $news_db = get_news_query($news_where, $news_conf);

    $content = "";
    foreach ($news_db as $news_data) {
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_data['title']);
            $news_data['url'] = "/" . $cfg['WEB_LANG'] . "/news/{$news_data['nid']}/{$news_data['page']}/$friendly_title";
        } else {
            $news_data['url'] = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_data['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news_data['page']}";
        }


        $news_data['date'] = $tUtil->format_date($news_data['created']);
        $content .= $tpl->getTPL_file("News", "news_block", $news_data);
    }
    return $content;
}

function news_block_conf() {
    global $filter, $LNG, $cfg;
    require_once __DIR__ . '/news_common.php';
    require_once __DIR__ . '/news_form_common.php';

    $block_conf = $filter->post_array("block_conf");
    $block_conf['admin_block'] = 0;

    $content['config'] = $block_conf;
    $content['content'] = "<br/><label>Featured</label><input type='checkbox' name='block_conf[featured]'/>";
    $content['content'] .= "<br/><label>Frontpage</label><input type='checkbox' name='block_conf[frontpage]'/>";
    $content['content'] .= "<br/><label>Childs</label><input type='checkbox' name='block_conf[childs]'/>";
    $content['content'] .= "<br/><select name='block_conf[news_type]' id='news_type'>";
    $content['content'] .= "<option value='headlines'>Titles</option>";
    $content['content'] .= "<option value='lead'>Titles / lead</option>";
    $content['content'] .= "<option value='full'>Full news</option>";
    $content['content'] .= "</select>";
    $content['content'] .= "<br><label>" . $LNG['L_NEWS_CATEGORY'] . "</label>";
    $content['content'] .= "<br/>" . news_getCatsSelect(null, "block_conf[news_cat]");
    $content['content'] .= "<br><label>" . $LNG['L_NEWS_LANG'] . "</label>";
    $content['content'] .= "<br/>";
    if (defined('MULTILANG')) {
        global $ml;
        $langs = $ml->get_site_langs();
        if (count($langs) > 0) {
            $content['content'] .= "<select name='block_conf[news_lang]' id='news_lang'>";
            foreach ($langs as $lang) {
                $content['content'] .= "<option value='{$lang['lang_id']}'>{$lang['lang_name']}</option>";
            }
            $content['content'] .= "</select>";
        }
    }
    $content['content'] .= "<br><label>Limit</label>";
    $content['content'] .= "<br/><select name='block_conf[limits]'>";

    for ($i = 1; $i <= $cfg['news_dflt_getnews_limit']; $i++) {
        $content['content'] .= "<option value='$i'>$i</option>";
    }
    $content['content'] .= "/<select>";

    $content['config'] = $block_conf;

    return $content;
}
