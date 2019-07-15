<?php

/**
 *  News - News submit include
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */

/**
 * Build create new news form
 * 
 * @global aray $LNG
 * @global TPL $tpl
 * @global SessionManager $sm
 * @global SimpleFrontend $frontend
 * @global Multilang $ml
 * @global Plugins $plugins
 * @return string|boolean
 */
function news_new_form() {
    global $LNG, $tpl, $sm, $frontend, $ml, $plugins, $cfg;

    if (!($plugins->expressStartProvider('EDITOR')) || !($plugins->expressStartProvider('CATS'))) {
        $frontend->messageBox(['msg' => 'L_E_PL_CANTEXPRESS']);
        return false;
    }
    $plugins->expressStartProvider('NEWSMEDIAUPLOAD');

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

    if (!empty($user) && $user['uid'] > 0) {
        $form_data['author'] = $user['username'];
        $form_data['author_id'] = $user['uid'];
        $form_data['tos_checked'] = 1;
    }

    if (defined('ACL')) {
        $form_data['author_readonly'] = !news_perm_ask('w_news_change_author');
        $form_data['news_add_source'] = news_perm_ask('w_news_add_source');
        $form_data['news_add_related'] = news_perm_ask('w_news_add_related');
    } else {
        $form_data['author_readonly'] = !news_perm_ask('w_news_change_author');
        $form_data['news_add_source'] = news_perm_ask('w_news_add_source');
        $form_data['news_add_related'] = news_perm_ask('w_news_add_related');
    }
    if (defined('MULTILANG') && ($site_langs = $ml->getSiteLangsSelect('news_lang')) != false) {
        $form_data['select_langs'] = $site_langs;
    }
    $form_data['select_categories'] = news_getCatsSelect();
    if (empty($form_data['select_categories'])) {
        $frontend->messageBox(['msg' => 'L_NEWS_NOCATS']);
        return false;
    }
    ($cfg['news_allow_user_drafts']) ? $form_data['as_draft'] = 1 : null;

    $form_data['terms_url'] = $sm->getPage('terms');
    do_action('news_new_form_add', $form_data);

    /* EDITOR */

    $form_data['editor'] = $editor->getEditor();

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $form_data));

    return true;
}

/**
 * insert new news 
 * 
 * @global array $cfg
 * @global Database $db
 * @param array $news_data
 * @return boolean
 */
function news_create_new($news_data) {
    global $cfg, $db;

    $news_data['nid'] = $db->getNextNum('news', 'nid');

    defined('MULTILANG') ? $lang_id = $news_data['news_lang'] : $lang_id = 1;

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
        'title' => $db->escapeStrip($news_data['title']),
        'lead' => $db->escapeStrip($news_data['lead']),
        'text' => $db->escapeStrip($news_data['editor_text']),
        'featured' => $news_data['featured'],
        'author_id' => $news_data['author_id'],
        'category' => $news_data['category'],
        'as_draft' => empty($news_data['as_draft']) ? 0 : 1,
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
            'link' => $db->escapeStrip(urlencode($news_data['news_source'])),
            'extra' => $news_data['news_source_title']
        ];
        $db->insert('links', $insert_ary);
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $insert_ary = [
            'source_id' => $news_data['nid'],
            'plugin' => $plugin,
            'type' => 'related',
            'link' => $db->escapeStrip(urlencode($news_data['news_new_related'])),
            'extra' => $news_data['news_new_related_title']
        ];
        $db->insert('links', $insert_ary);
    }
    return true;
}

/**
 * Send new news process
 * 
 * @global array $LNG
 * @global array $cfg
 */
function news_submit_new_process() {
    global $LNG, $cfg;

    $news_data = news_form_getPost();

    news_submit_form_check($news_data);

    if (news_create_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_SUBMITED_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}
