<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_new_lang() {
    global $cfg, $LNG, $acl_auth, $tpl, $sm, $frontend, $filter, $sm;

    $user = $sm->getSessionUser();
    
    $nid = $filter->get_int("nid", 11, 1);
    $lang_id = $filter->get_int("lang_id", 4, 1);
    $page = $filter->get_int("npage", 11, 1);

    if (empty($nid) || empty($lang_id) || empty($page)) {
        return $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    }
    if (!($news_data = get_news_byId($nid, $lang_id, $page))) {
        return false; // error already setting in get_news
    }

    $news_data['news_form_title'] = $LNG['L_NEWS_NEWLANG'];

    $translator = $sm->getSessionUser();

    if (empty($translator) && $cfg['news_anon_translate']) {
        $translator['username'] = $LNG['L_NEWS_ANONYMOUS'];
        $translator['uid'] = 0;
    } else if (empty($translator)) {
        return $frontend->message_box(['msg' => "L_NEWS_NO_EDIT_PERMISS"]);
    }
    $news_data['translator'] = $translator['username'];
    $news_data['translator_id'] = $translator['uid'];
    $translator['uid'] > 0 ? $news_data['tos_checked'] = 1 : false;

    if ($user && !defined('ACL') && $user['isFounder']) {
        $news_data['author_readonly'] = "readonly=\"readonly\"";
    } else {
        $news_data['author_readonly'] = "";
    }
    $news_data['can_add_related'] = 0;
    $news_data['can_add_source'] = 0;

    if (($site_langs = news_get_missed_langs($news_data['nid'], $news_data['page'])) != false) {
        $news_data['select_langs'] = $site_langs;
    } else {
        return $frontend->message_box(['msg' => "L_NEWS_E_ALREADY_TRANSLATE_ALL"]);
    }
    $editor = new Editor();
    $news_data['editor'] = $editor->getEditor(['text' => $news_data['text']]);

    //$news_data['terms_url'] = $cfg['TERMS_URL'];
    do_action("news_newlang_form_add", $news_data);

    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_form", $news_data));
}

function news_form_newlang_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    if (!$user && !$cfg['news_anon_translate']) {
        return false;
    }

    $news_data = news_form_getPost();

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }

    if (news_translate($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_TRANSLATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}

function news_translate($news_data) {
    global $cfg, $db, $ml;

    $lang_id = $ml->iso_to_id($news_data['lang']);

    if (empty($news_data['nid']) || empty($lang_id)) {
        return false;
    }
    $query = $db->select_all("news", ["nid" => "{$news_data['nid']}", "lang_id" => "$lang_id", "page" => "{$news_data['page']}"]);
    if ($db->num_rows($query) > 0) { //already exist
        return false;
    }
    //GET original main news (page 1) for copy values
    $orig_news_nid = $filter->get_int("nid", 11, 1);
    $orig_news_lang = $filter->get_AZChar("lang", 2, 2);
    $orig_news_lang_id = $ml->iso_to_id($orig_news_lang);

    $query = $db->select_all("news", ["nid" => "$orig_news_nid", "lang_id" => "$orig_news_lang_id", "page" => 1], "LIMIT 1");
    $orig_news = $db->fetch($query);
    $moderation = $cfg['news_moderation'];

    $insert_ary = [
        "nid" => $news_data['nid'],
        "lang_id" => $lang_id,
        "page" => $news_data['page'],
        "translator" => $news_data['news_translator'],
        "translator_id" => $news_data['news_translator_id'],
        "title" => $news_data['title'],
        "lead" => $news_data['lead'],
        "text" => $news_data['text'],
        "author" => $orig_news['author'],
        "author_id" => $orig_news['author_id'],
        "category" => $orig_news['category'],
        "lang" => $news_data['lang'],
        "acl" => $orig_news['acl'],
        "moderation" => $moderation
    ];
    $db->insert("news", $insert_ary);

    return true;
}
