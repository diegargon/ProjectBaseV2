<?php

/**
 *  NewsSearch include file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

//DIRECT
function NS_setTopNavSearchbox() {
    global $tpl, $cfg, $frontend;

    if ($cfg['FRIENDLY_URL']) {
        $sbox_data['searchUrl'] = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/search/";
    } else {
        $sbox_data['searchUrl'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=NewsSearch&page=search&lang={$cfg['WEB_LANG']}";
    }
    $frontend->addMenuItem('top_menu_right', $tpl->getTplFile('NewsSearch', 'NewsSearchBarbox', $sbox_data), 4);

    return true;
}

function NS_tag_add_form() {
    global $cfg, $tpl;
    $cfg['ns_tag_support'] ? $tpl->addtoTplVar('NEWS_FORM_BOTTOM_OPTION', NS_tags_option()) : null;
}

function NS_news_mod_insert(& $insert_ary) {
    global $db, $filter;

    $tags = $db->escapeStrip($filter->postUtf8Txt('news_tags'));
    !empty($tags) ? $insert_ary['tags'] = $tags : null;
}

function NS_news_tag_show_page(& $news_row) {
    global $LNG, $tpl, $cfg;

    if (!empty($news_row['tags'])) {
        $cfg['PAGE_KEYWORDS'] = $news_row['tags'];
        $exploted_tags = explode(',', $news_row['tags']);
        $tag_data = '<div class="tags"> <p>' . $LNG['L_NS_TAGS'] . ': ';
        foreach ($exploted_tags as $tag) {
            $tag = trim($tag);
            preg_replace('/\s+/', "%20", $tag);
            $link_tag = urldecode($tag);
            if ($cfg['FRIENDLY_URL']) {
                $tag_data .= "<a href=\"{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/searchTag/$link_tag\">$tag</a> ";
            } else {
                $tag_data .= "<a href=\"{$cfg['REL_PATH']}{$cfg['CON_FILE']}&lang={$cfg['WEB_LANG']}&searchTag=$link_tag\">$tag</a> ";
            }
        }
        $tag_data .= '</p></div>';
        $tpl->addtoTplVar('ADD_TO_NEWSSHOW_BOTTOM', $tag_data);
    } else {
        $cfg['PAGE_KEYWORDS'] = $news_row['title'];
    }
}

function NS_tags_edit_form_add($news_data) {
    global $tpl;
    $news_data['page'] == 1 ? $tpl->addtoTplVar('NEWS_FORM_BOTTOM_OPTION', NS_tags_option($news_data['tags'])) : null;
}

function NS_news_edit_set_tag(& $set_ary) {
    global $db, $filter;

    $tags = $db->escapeStrip($filter->postUtf8Txt('news_tags'));
    !empty($tags) ? $set_ary['tags'] = $tags : null;
}

//IN
function NS_tags_option($tags = null) {
    global $LNG, $cfg;

    $content = '<label for="news_tags">' . $LNG['L_NS_TAGS'] . '</label>';
    $content .= '<input  value="' . $tags . '" maxlength="' . $cfg['ns_tag_size_limit'] . '" id="news_tags" class="news_tags" name="news_tags" type="text" placeholder="' . $LNG['L_NS_TAGS_PLACEHOLDER'] . '" />';
    return $content;
}
