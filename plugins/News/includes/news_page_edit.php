<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function news_edit($news_nid, $news_lang_id, $news_page) {
    global $LNG, $tpl, $frontend, $sm, $plugins;

    $plugins->expressStartProvider('NEWSMEDIAUPLOAD');
    
    if (!is_array($news_data = get_news_byId($news_nid, $news_lang_id, $news_page))) {
        $frontend->messageBox(['msg' => $news_data]);
        return false; // error already setting in get_news
    }

    $author_data = $sm->getUserByID($news_data['author_id']);
    $news_data['author'] = $author_data['username'];
    if (!empty($news_data['translator_id'])) {
        $translator_data = $sm->getUserByID($news_data['author_id']);
        $news_data['translator'] = $translator_data['username'];
    }

    $news_perms = get_news_perms('news_edit', $news_data);
    $news_data['author_readonly'] = !$news_perms['news_can_change_author'];
    $news_data['news_add_source'] = $news_perms['news_add_source'];
    $news_data['news_add_related'] = $news_perms['news_add_related'];


    if (!$news_perms['news_edit']) {
        return $frontend->messageBox(['msg' => 'L_E_NOEDITACCESS']);
    }

    $news_data['news_form_title'] = $LNG['L_NEWS_EDIT_NEWS'];

    $news_data['select_categories'] = news_getCatsSelect($news_data);
    if ($news_perms['news_add_source']) {
        if (($news_source = news_get_source($news_data['nid'])) != false) {
            $news_data['news_source'] = urldecode($news_source['link']);
        }
    }
    if ($news_perms['news_add_related']) {
        if (($news_related = news_get_related($news_data['nid']))) {
            $news_data['news_related'] = '';
            foreach ($news_related as $related) {
                $related['link'] = urldecode($related['link']);
                $news_data['news_related'] .= "<input type='text' class='news_link' name='news_related[{$related['link_id']}]' value='{$related['link']}' />\n";
            }
        }
    }
    if (defined('MULTILANG') && ($site_langs = news_get_available_langs($news_data)) != false) {
        $news_data['select_langs'] = $site_langs;
    }

    $editor = new Editor();
    $news_data['editor'] = $editor->getEditor(['text' => $news_data['text']]);
    //$news_data['terms_url'] = $cfg['TERMS_URL'];
    do_action('news_edit_form_add', $news_data);

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $news_data));
}

function news_form_edit_process() {
    global $LNG, $cfg;

    $news_data = news_form_getPost();


    $news_perms = get_news_perms('page_edit', $news_data);

    if (!$news_perms['news_edit']) {
        die('[{"status": "4", "msg": "' . $LNG['L_E_NOEDITACCESS'] . '"}]');
    }

    if (empty($news_data['nid']) || empty($news_data['old_news_lang_id']) || empty($news_data['page'])) {
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_NOT_EXIST'] . '"}]');
    }

    if (!is_array($news_orig = get_news_byId($news_data['nid'], $news_data['old_news_lang_id'], $news_data['page']))) {
        die('[{"status": "4", "msg": "' . $LNG[$news_orig] . '"}]');
    }

    news_submit_edit_form_check($news_data);
    if (news_form_news_update($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}
