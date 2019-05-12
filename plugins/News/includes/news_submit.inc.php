<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 * 
 * news_new_form
 */

function news_new_form($news_perms) {
    global $LNG, $tpl, $sm, $frontend, $ml, $plugins;

    if (!($plugins->express_start_provider('EDITOR')) || !($plugins->express_start_provider('CATS'))) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }
    $plugins->express_start_provider('NEWSMEDIAUPLOAD');

    $editor = new Editor();

    if (!empty($_POST['editor_preview'])) {
        $editor->showPreview();
        die();
    }

    $form_data['news_form_title'] = $LNG['L_SUBMIT_NEWS'];
    $user = $sm->getSessionUser();

    if ((empty($user) || $user['uid'] == 0)) {
        $form_data['author'] = $LNG['L_NEWS_ANONYMOUS'];
        $form_data['author_id'] = 0;
        $form_data['tos_checked'] = 0;
    }

    if ($user && $user['uid'] > 0) {
        $form_data['author'] = $user['username'];
        $form_data['author_id'] = $user['uid'];
        $form_data['tos_checked'] = 1;
    }
    $form_data['author_readonly'] = !$news_perms['news_can_change_author'];
    $form_data['news_add_source'] = $news_perms['news_add_source'];
    $form_data['news_add_related'] = $news_perms['news_add_related'];

    if (defined('MULTILANG') && ($site_langs = $ml->deprecated_get_sitelangs_select('news_lang')) != false) {
        $form_data['select_langs'] = $site_langs;
    }

    $form_data['select_categories'] = news_getCatsSelect();
    if (empty($form_data['select_categories'])) {
        $frontend->messageBox(['msg' => 'L_NEWS_NOCATS']);
        return false;
    }
    $form_data['terms_url'] = ''; // $cfg['TERMS_URL'];    
    do_action('news_new_form_add', $form_data);

    /* EDITOR */

    $form_data['editor'] = $editor->getEditor();

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $form_data));
}

function news_create_new($news_data) {
    global $cfg, $ml, $db;

    $news_data['nid'] = $db->get_next_num('news', 'nid');

    defined('MULTILANG') ? $lang_id = $ml->iso_to_id($news_data['news_lang']) : $lang_id = 1;

    empty($news_data['featured']) ? $news_data['featured'] = 0 : null;

    if ($news_data['featured'] == 1 && $cfg['news_moderation'] == 1) {
        $moderation = 0;
    } else if ($cfg['news_moderation'] == 1) {
        $moderation = 1;
    } else {
        $moderation = 0;
    }

    $insert_ary = [
        'nid' => $news_data['nid'],
        'lang_id' => $lang_id,
        'page' => 1,
        'title' => $db->escape_strip($news_data['title']),
        'lead' => $db->escape_strip($news_data['lead']),
        'text' => $db->escape_strip($news_data['editor_text']),
        'featured' => $news_data['featured'],
        'author_id' => $news_data['author_id'],
        'category' => $news_data['category'],
        'moderation' => $moderation
    ];

    do_action('news_submit_insert', $insert_ary);
    $db->insert('news', $insert_ary);

    /* Custom / MOD */
    do_action('news_create_new_insert', $news_data);

    $plugin = 'News';

    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $insert_ary = [
            'source_id' => $news_data['nid'],
            'plugin' => $plugin,
            'type' => 'source',
            'link' => $db->escape_strip(urlencode($news_data['news_source']))
        ];
        $db->insert('links', $insert_ary);
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $insert_ary = [
            'source_id' => $news_data['nid'], 'plugin' => $plugin,
            'type' => 'related', 'link' => $db->escape_strip(urlencode($news_data['news_new_related'])),
        ];
        $db->insert('links', $insert_ary);
    }
    return true;
}

function news_submit_new_process() {
    global $LNG, $cfg;

    $news_data = news_form_getPost();

    news_submit_edit_form_check($news_data);

    if (news_create_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_SUBMITED_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}
