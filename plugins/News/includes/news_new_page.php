<?php

/**
 *  News - News new page
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function news_new_page($news_nid, $news_lang_id, $news_page) {
    global $sm, $tpl, $LNG, $frontend, $plugins, $sm;

    $plugins->expressStartProvider('NEWSMEDIAUPLOAD');

    $editor = new Editor();
    $user = $sm->getSessionUser();

    if (empty($user) || $user['uid'] == 0) {
        $frontend->messageBox(['msg' => 'L_E_NOACCESS']);
        return false;
    }
    $user['uid'] > 0 ? $form_data['tos_checked'] = 1 : null;

    if (!is_array($news_data = get_news_byId($news_nid, $news_lang_id, 1))) { //get first page
        $frontend->messageBox(['msg' => $news_data]);
        return false;
    }

    if (!news_perm_ask('w_news_create_new_page')) {
        $frontend->messageBox(['msg' => 'L_E_NOEDITACCESS']);
        return false;
    }

    $form_data['author_readonly'] = !news_perm_ask('w_news_change_author');
    $form_data['news_add_source'] = news_perm_ask('w_news_add_source');
    $form_data['news_add_related'] = news_perm_ask('w_news_add_related');
    $form_data['news_form_title'] = $LNG['L_NEWS_CREATE_NEW_PAGE'];
    $form_data['author'] = $user['username'];
    $form_data['author_id'] = $user['uid'];
    $form_data['editor'] = $editor->getEditor();
    $form_data['terms_url'] = $sm->getPage('terms');

    do_action('news_newpage_form_add');

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $form_data));

    return true;
}

function news_newpage_form_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    if (empty($user) || $user['uid'] == 0) {
        return false;
    }
    $news_data = news_form_getPost();

    if (!news_perm_ask('w_news_edit')) {
        die('[{"status": "4", "msg": "' . $LNG['L_E_NOEDITACCESS'] . '"}]');
    }

    if ($news_data['nid'] == false) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    if ($news_data['title'] == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_ERROR'] . '"}]');
    }
    if ((strlen($news_data['title']) > $cfg['news_title_max_length']) ||
            (strlen($news_data['title']) < $cfg['news_title_min_length'])
    ) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    if (!empty($news_data['lead']) && (strlen($news_data['lead']) > $cfg['news_lead_max_length'])) {
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
    }
    if ($news_data['editor_text'] == false) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    if ((strlen($news_data['editor_text']) > $cfg['news_text_max_length']) ||
            (strlen($news_data['editor_text']) < $cfg['news_text_min_length'])
    ) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }
    if (empty($news_data['news_lang_id']) || empty($news_data['nid'])) {
        die('[{"status": "10", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    if (news_newpage_submit_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}

function news_newpage_submit_new($news_data) {
    global $db, $cfg;

    $query = $db->selectAll('news', ['nid' => $news_data['nid'], 'lang_id' => $news_data['news_lang_id'], 'page' => '1'], 'LIMIT 1');

    if (($num_pages = $db->numRows($query)) <= 0) {
        return false;
    }


    $news_father = $db->fetch($query);
    $num_pages = $news_father['num_pages'];
    $page_num = $num_pages + 1;

    $insert_ary = [
        'nid' => $news_father['nid'],
        'lang_id' => $news_father['lang_id'],
        'title' => $db->escapeStrip($news_data['title']),
        'text' => $db->escapeStrip($news_data['editor_text']),
        'featured' => $news_father['featured'],
        'author_id' => $news_father['author_id'],
        'category' => $news_father['category'],
        'moderation' => $cfg['news_moderation'],
        'page' => $page_num
    ];
    !empty($news_data['lead']) ? $insert_ary['lead'] = $db->escapeStrip($news_data['lead']) : null;
    $db->insert("news", $insert_ary);

    $num_pages++;
    $db->update('news', ['num_pages' => $num_pages], ['nid' => $news_father['nid'], 'lang_id' => $news_father['lang_id']], 'LIMIT ' . $num_pages);

    return true;
}
