<?php

/*
 *  Copyright @ 2016 - 2018Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_page($news_nid, $news_lang_id, $news_page) {
    global $cfg, $acl_auth, $sm, $tpl, $LNG, $frontend, $filter, $plugins, $sm;

    $editor = new Editor();
    $user = $sm->getSessionUser();

    if (!($user = $sm->getSessionUser())) {
        return $frontend->messageBox(["msg" => "L_E_NOACCESS"]);
    }
    $user['uid'] > 0 ? $form_data['tos_checked'] = 1 : false;

    if (!is_array($news_data = get_news_byId($news_nid, $news_lang_id, 1))) { //get first page
        $frontend->messageBox(['msg' => $news_data]);
        return false;
    }

    $news_perms = get_news_perms("new_page");

    if (!$news_perms['news_create_new_page']) {
        return $frontend->messageBox(["msg" => "L_E_NOEDITACCESS"]);
    }

    $form_data['author_readonly'] = !$news_perms['news_can_change_author'];
    $form_data['news_add_source'] = $news_perms['news_add_source'];
    $form_data['news_add_related'] = $news_perms['news_add_related'];
    $form_data['news_form_title'] = $LNG['L_NEWS_CREATE_NEW_PAGE'];
    $form_data['author'] = $user['username'];
    $form_data['editor'] = $editor->getEditor();
    //$form_data['terms_url'] = $cfg['TERMS_URL'];

    do_action("news_newpage_form_add");

    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_form", $form_data));
}

function news_newpage_form_process() {
    global $LNG, $cfg, $sm;

    if (!($user = $sm->getSessionUser())) {
        return false;
    }
    $news_data = news_form_getPost();

    $news_perms = get_news_perms("new_page");

    if (!$news_perms['news_edit']) {
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
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
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

    $query = $db->select_all("news", ["nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['news_lang_id']}", "page" => "1"], "LIMIT 1");

    if (($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }

    $news_father = $db->fetch($query);

    $insert_ary = [
        "nid" => $news_father['nid'],
        "lang_id" => $news_father['lang_id'],
        "title" => $db->escape_strip($news_data['title']),
        "text" => $db->escape_strip($news_data['editor_text']),
        "featured" => $news_father['featured'],
        "author_id" => $news_father['author_id'],
        "category" => $news_father['category'],
        "moderation" => $cfg['news_moderation'],
        "page" => ++$num_pages
    ];
    !empty($news_data['lead']) ? $insert_ary['lead'] = $db->escape_strip($news_data['lead']) : false;
    $db->insert("news", $insert_ary);

    return true;
}
