<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function news_edit($news_nid, $news_lang_id, $news_page) {
    global $LNG, $tpl, $frontend, $sm, $plugins;

    $user = $sm->getSessionUser();

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

    $news_data['author_readonly'] = !news_perm_ask('w_news_change_author');
    $news_data['news_add_source'] = news_perm_ask('w_news_add_source');
    $news_data['news_add_related'] = news_perm_ask('w_news_add_related');


    if (!news_perm_ask('w_news_edit')) {
        if (!($user['uid'] == $news_data['author_id']) && news_perm_ask('w_news_edit_own')
        ) {
            $frontend->messageBox(['msg' => 'L_E_NOEDITACCESS']);
            return false;
        }
    }

    $news_data['news_form_title'] = $LNG['L_NEWS_EDIT_NEWS'];

    $news_data['select_categories'] = news_getCatsSelect($news_data);
    if (news_perm_ask('w_news_add_source')) {
        if (($news_source = news_get_source($news_data['nid'])) != false) {
            $news_data['news_source'] = urldecode($news_source['link']);
        }
    }
    if (news_perm_ask('w_news_add_related')) {
        if (($news_related = news_get_related($news_data['nid']))) {
            $news_data['news_related'] = '';
            foreach ($news_related as $related) {
                $related['link'] = urldecode($related['link']);
                $news_data['news_related'] .= "<input type='text' class='news_link' name='news_related[{$related['link_id']}]' value='{$related['link']}' />\n";
            }
        }
    }
    if (defined('MULTILANG') && ($site_langs = news_get_available_langs($news_data)) != false) {
        if ($news_data['page'] == 1) {
            $news_data['select_langs'] = $site_langs;
            $news_data['current_lang_id'] = '<input type="hidden" name="current_lang_id" value="' . $news_data['lang_id'] . '"/>';
        } else {
            $news_data['current_lang_id'] = '<input type="hidden" name="current_lang_id" value="' . $news_data['lang_id'] . '"/>';
        }
    }

    $editor = new Editor();
    $news_data['editor'] = $editor->getEditor(['text' => $news_data['text']]);
    $news_data['terms_url'] = $sm->getPage('terms');
    do_action('news_edit_form_add', $news_data);

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $news_data));

    return true;
}

function news_form_edit_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    $news_data = news_form_getPost();

    if (!news_perm_ask('w_news_edit')) {
        if (!($user['uid'] == $news_data['author_id']) && news_perm_ask('w_news_edit_own')
        ) {

            die('[{"status": "4", "msg": "' . $LNG['L_E_NOEDITACCESS'] . '"}]');
        }
    }
    if (!news_perm_ask('w_news_edit')) {
        die('[{"status": "4", "msg": "' . $LNG['L_E_NOEDITACCESS'] . '"}]');
    }

    if (empty($news_data['nid']) || empty($news_data['current_lang_id']) || empty($news_data['page'])) {
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_NOT_EXIST'] . '"}]');
    }

    if (!is_array($news_orig = get_news_byId($news_data['nid'], $news_data['current_lang_id'], $news_data['page']))) {
        die('[{"status": "4", "msg": "' . $LNG[$news_orig] . '"}]');
    }

    news_submit_form_check($news_data);
    if (news_form_news_update($news_data)) {
        empty($news_data['news_lang']) ? $news_data['news_lang'] = $news_data['current_lang_id'] : null; // add lang id if edit a non one npage
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_data['title']);

            $back_url = '/' . $cfg['WEB_LANG'] . "/news/{$news_data['nid']}/{$news_data['page']}/{$news_data['news_lang']}/$friendly_title";
        } else {
            $back_url = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_data['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news_data['page']}&news_lang_id={$news_data['lang_id']}";
        }

        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $back_url . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}
