<?php

/*
 *  Copyright @ 2016 - 2018Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_page() {
    global $cfg, $acl_auth, $sm, $tpl, $LNG, $frontend, $filter, $plugins, $sm;

   $editor = new Editor();
    $user = $sm->getSessionUser();
    $nid = $filter->get_int("nid", 11, 1);
    $lang_id = $filter->get_int("lang_id", 4, 1);
    
   if ($user && !defined('ACL') && $user['isFounder']) {
        $form_data['author_readonly'] = "readonly=\"readonly\"";
    } else {
        $form_data['author_readonly'] = "";
    }    

    if (empty($nid) || empty($lang_id)) {
        return $frontend->message_box(["msg" => "L_NEWS_NOT_EXIST"]);
    }
    if (!($news_data = get_news_byId($nid, $lang_id, 1))) { //get first page
        return false; // error already setting in get_news
    }

    if (!($user = $sm->getSessionUser())) {
        return $frontend->message_box(["msg" => "L_E_NOACCESS"]);
    }

    $user['uid'] > 0 ? $form_data['tos_checked'] = 1 : false;

    if (( $news_data['author_id'] == $user['uid']) || (defined('ACL') && $acl_auth->acl_ask("news_admin||admin_all")) || (!defined('ACL') && $user['isAdmin'])
    ) {
        //Do nothing
    } else {
        return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
    }

    $form_data['news_form_title'] = $LNG['L_NEWS_CREATE_NEW_PAGE'];
    $form_data['author'] = $user['username'];
    $form_data['editor'] = $editor->getEditor();
    //$form_data['terms_url'] = $cfg['TERMS_URL'];
    $form_data['can_add_related'] = 0;
    $form_data['can_add_source'] = 0;

    
    do_action("news_newpage_form_add");

    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("News", "news_form", $form_data));
}

function news_newpage_form_process() {
    global $LNG, $cfg, $sm;

    if (!($user = $sm->getSessionUser())) {
        return false;
    }
    $news_data = news_form_getPost();

    if ($news_data['title'] == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_ERROR'] . '"}]');
    }
    if ((strlen($news_data['title']) > $cfg['NEWS_TITLE_MAX_LENGHT']) ||
            (strlen($news_data['title']) < $cfg['NEWS_TITLE_MIN_LENGHT'])
    ) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    if (!empty($news_data['lead']) && (strlen($news_data['lead']) > $cfg['news_lead_max_length'])) {
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
    }
    if ($news_data['text'] == false) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    if ((strlen($news_data['text']) > $cfg['news_lead_min_length']) ||
            (strlen($news_data['text']) < $cfg['news_lead_min_length'])
    ) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }
    if (empty($news_data['lang_id']) || empty($news_data['nid'])) {
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

    $query = $db->select_all("news", ["nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"], "ORDER BY page");

    if (($num_pages = $db->num_rows($query)) <= 0) {
        return $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    }
    $news_father = $db->fetch($query);

    $insert_ary = [
        "nid" => $news_father['nid'],
        "lang_id" => $news_father['lang_id'],
        "title" => $news_data['title'],
        "text" => $news_data['text'],
        "featured" => $news_father['featured'],
        "author" => $news_father['author'],
        "author_id" => $news_father['author_id'],
        "category" => $news_father['category'],
        "lang" => $news_father['lang'],
        "acl" => $news_father['acl'],
        "moderation" => $cfg['news_moderation'],
        "page" => ++$num_pages
    ];
    !empty($news_data['lead']) ? $insert_ary['lead'] = $news_data['lead'] : false;
    $db->insert("news", $insert_ary);

    return true;
}
