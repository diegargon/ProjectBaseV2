<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * news_new_form

 */

function news_new_form($editor) {
    global $LNG, $cfg, $acl_auth, $tpl, $sm, $frontend, $ml;

    $form_data['news_form_title'] = $LNG['L_SUBMIT_NEWS'];

    $user = $sm->getSessionUser();
    if (empty($user) && $cfg['NEWS_SUBMIT_ANON']) {
        $form_data['author'] = $LNG['L_NEWS_ANONYMOUS'];
        $form_data['tos_checked'] = 0;
    } else if (empty($user)) {
        $frontend->message_box(["msg" => "L_E_NOACCESS"]);
        return false;
    }

    $user ? $form_data['author'] = $user['username'] : null;
    $user ? $form_data['tos_checked'] = 1 : null;

    $form_data['author_readonly'] = "";

    if (defined('MULTILANG') && ($site_langs = $ml->get_sitelangs_select("news_lang")) != false) {
        $form_data['select_langs'] = $site_langs;
    }

    /*
      if ($user && defined('ACL') && $acl_auth->acl_ask("news_admin||admin_all")) {
      $form_data['select_acl'] = $acl_auth->get_roles_select("news");
      $form_data['news_auth'] = "admin";
      } else {
      $form_data['author_readonly'] =  "readonly=\"readonly\"";
      }

     */
    if ($user && !defined('ACL') && $user['isFounder']) {
        $form_data['author_readonly'] = "readonly=\"readonly\"";
        $form_data['can_add_related'] = 1;
        $form_data['can_add_source'] = 1;
    }

    $form_data['select_categories'] = news_getCatsSelect();

    $form_data['terms_url'] = ""; // $cfg['TERMS_URL'];
    do_action("news_new_form_add", $form_data);

    /* EDITOR */

    $form_data['editor'] = $editor->getEditor();

    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_form", $form_data));
}

function news_create_new($news_data) {
    global $cfg, $ml, $db;

    $news_data['nid'] = $db->get_next_num("news", "nid");

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = 1;
    }
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = "";
    empty($news_data['featured']) ? $news_data['featured'] = 0 : null;

    if ($news_data['featured'] == 1 && $cfg['news_moderation'] == 1) {
        $moderation = 0;
    } else if ($cfg['news_moderation'] == 1) {
        $moderation = 1;
    } else {
        $moderation = 0;
    }

    $insert_ary = [
        "nid" => $news_data['nid'],
        "lang_id" => $lang_id,
        "page" => 1,
        "title" => $news_data['title'],
        "lead" => $news_data['lead'],
        "text" => $news_data['editor_text'],
        "featured" => $news_data['featured'],
        "author" => $news_data['author'],
        "author_id" => $news_data['author_id'],
        "category" => $news_data['category'],
        "lang" => $news_data['lang'],
        "acl" => $acl,
        "moderation" => $moderation
    ];

    do_action("news_mod_submit_insert", $insert_ary);
    $db->insert("news", $insert_ary);

    /* Custom / MOD */
    do_action("news_create_new_insert", $news_data);

    $plugin = "News";

    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $type = "source";
        $insert_ary = [
            "source_id" => $news_data['nid'],
            "plugin" => $plugin,
            "type" => $type,
            "link" => $news_data['news_source']
        ];
        $db->insert("links", $insert_ary);
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $type = "related";
        $insert_ary = [
            "source_id" => $news_data['nid'], "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        ];
        $db->insert("links", $insert_ary);
    }
    return true;
}

function news_form_submit_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    if (!$user && !$cfg['NEWS_SUBMIT_ANON']) {
        return false;
    }
    $news_data = news_form_getPost();

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }
    if (news_form_extra_check($news_data) == false) {
        return false;
    }
    if (news_create_new($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_SUBMITED_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}
