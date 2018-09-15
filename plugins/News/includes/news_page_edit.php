<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_edit($news_nid, $news_lang_id, $news_page) {
    global $cfg, $LNG, $acl_auth, $tpl, $filter, $frontend, $sm;

    if (!is_array($news_data = get_news_byId($news_nid, $news_lang_id, $news_page))) {
        $frontend->message_box(["msg" => $news_data]);
        return false; // error already setting in get_news
    }

    $news_perms = get_news_perms("page_edit", $news_data);
    $news_data['author_readonly'] = !$news_perms['news_can_change_author'];
    $news_data['news_add_source'] = $news_perms['news_add_source'];
    $news_data['news_add_related'] = $news_perms['news_add_related'];

    if (!$news_perms['news_edit']) {
        return $frontend->message_box(["msg" => "L_E_NOEDITACCESS"]);
    }

    $news_data['news_form_title'] = $LNG['L_NEWS_EDIT_NEWS'];

    $news_data['select_categories'] = news_getCatsSelect($news_data);
    if ($news_perms['news_add_source']) {
        if (($news_source = get_news_source_byID($news_data['nid'])) != false) {
            $news_data['news_source'] = $news_source['link'];
        }
    }
    if ($news_perms['news_add_related']) {
        if (($news_related = news_get_related($news_data['nid']))) {
            $news_data['news_related'] = "";
            foreach ($news_related as $related) {
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
    do_action("news_edit_form_add", $news_data);

    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_form", $news_data));
}

function news_form_edit_process() {
    global $LNG, $cfg, $frontend;

    $news_data = news_form_getPost();

    if (empty($news_data['nid']) || empty($news_data['old_news_lang_id']) || empty($news_data['page'])) {
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_NOT_EXIST'] . '"}]');
    }


    //echo $news_data['nid'] . $news_data['old_news_lang_id'] . $news_data['page'];
    if (!is_array($news_orig = get_news_byId($news_data['nid'], $news_data['old_news_lang_id'], $news_data['page']))) {
        die('[{"status": "4", "msg": "' . $LNG[$news_orig] . '"}]');
    }

    news_edit_form_check($news_data);
    if (news_full_update($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}

function news_edit_form_check($news_data) {
    global $LNG, $cfg;

    //USERNAME/AUTHOR
    if ($news_data['author'] == false) {
        die('[{"status": "2", "msg": "' . $LNG['L_NEWS_ERROR_INCORRECT_AUTHOR'] . '"}]');
    }
    //TITLE
    if ($news_data['title'] == false) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_ERROR'] . '"}]');
    }
    if ((mb_strlen($news_data['title'], $cfg['CHARSET']) > $cfg['news_title_max_length']) ||
            (mb_strlen($news_data['title'], $cfg['CHARSET']) < $cfg['news_title_min_length'])
    ) {
        die('[{"status": "3", "msg": "' . $LNG['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    //LEAD
    if (isset($_GET['npage']) && $_GET['npage'] > 1) {
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['news_lead_max_length'])) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    } else {
        if ($news_data['lead'] == false) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_ERROR'] . '"}]');
        }
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['news_lead_max_length']) ||
                (mb_strlen($news_data['lead'], $cfg['CHARSET']) < $cfg['news_lead_min_length'])
        ) {
            die('[{"status": "4", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    }
    //TEXT

    if ($news_data['editor_text'] == false) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    $text_size = mb_strlen($news_data['editor_text'], $cfg['CHARSET']);

    if (($text_size > $cfg['news_text_max_length']) || ($text_size < $cfg['news_text_min_length'])) {
        die('[{"status": "5", "msg": "' . $LNG['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }
    //CATEGORY
    if ($news_data['category'] == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    //Source check valid if input
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && $cfg['display_news_source']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_SOURCE'] . '"}]');
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && $cfg['display_news_related']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && $cfg['display_news_related']) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    // Custom /Mod Validators 
    if (($return = do_action("news_form_add_check", $news_data)) && !empty($return)) {
        die('[{"status": "9", "msg": "' . $return . '"}]');
    }
    //FEATURED NOCHECK ATM
    //ACL NO CHECK ATM

    return true;
}

function news_full_update($news_data) {
    global $cfg, $db, $ml, $filter;

    empty($news_data['featured']) ? $news_data['featured'] = 0 : false;
    !isset($news_data['news_translator']) ? $news_data['news_translator'] = "" : false;

    if (defined('MULTILANG')) {
        $news_lang_id = $ml->iso_to_id($news_data['news_lang']);
    } else {
        $news_lang_id = 1;
    }

    $set_ary = [
        "title" => $news_data['title'],
        "lead" => $news_data['lead'],
        "text" => $news_data['editor_text'],
        "author" => $news_data['author'],
        "author_id" => $news_data['author_id'],
        "category" => $news_data['category'],
    ];

    if ($news_data['old_news_lang_id'] != $news_lang_id) {
        $set_ary["lang_id"] = $news_lang_id;
        $set_ary['lang'] = $news_data['news_lang'];
    }

    do_action("news_fulledit_mod_set", $set_ary);

    $where_ary = [
        "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['old_news_lang_id']}", "page" => "{$news_data['page']}"
    ];

    $db->update("news", $set_ary, $where_ary);

    do_action("news_form_update", $news_data); //MOD
    //
    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $source_id = $news_data['nid'];
        $plugin = "News";
        $type = "source";

        $query = $db->select_all("links", ["source_id" => $source_id, "type" => $type, "plugin" => $plugin], "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            $db->update("links", ["link" => $news_data['news_source']], ["source_id" => $source_id, "type" => $type, "plugin" => $plugin]);
        } else {
            $insert_ary = [
                "source_id" => $source_id, "plugin" => $plugin,
                "type" => $type, "link" => $news_data['news_source'],
            ];
            $db->insert("links", $insert_ary);
        }
    } else {
        $source_id = $news_data['nid'];
        $plugin = "News";
        $type = "source";
        $db->delete("links", ["source_id" => $source_id, "type" => $type, "plugin" => $plugin], "LIMIT 1");
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $source_id = $news_data['nid'];
        $plugin = "News";
        $type = "related";
        $insert_ary = [
            "source_id" => $source_id, "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        ];
        $db->insert("links", $insert_ary);
    }
    //OLD RELATED
    if (!empty($news_data['news_related'])) {
        foreach ($news_data['news_related'] as $link_id => $value) {
            if ($filter->var_int($link_id)) { //value its checked on post $link_id no 
                if (empty($value)) {
                    $db->delete("links", ["link_id" => $link_id], "LIMIT 1");
                } else {
                    $db->update("links", ["link" => $value], ["link_id" => $link_id], "LIMIT 1");
                }
            }
        }
    }
    return true;
}

function news_limited_update($news_data) {
    global $cfg, $db, $ml;

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = 1;
    }

    $query = $db->select_all("news", ["nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"]);
    if (($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }

    $set_ary = [
        "lang_id" => $lang_id, "title" => $news_data['title'], "lead" => $news_data['lead'], "text" => $news_data['text'],
        "lang" => $news_data['lang']
    ];
    do_action("news_limitededit_mod_set", $set_ary);
    $where_ary = [
        "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => "{$news_data['page']}"
    ];
    $db->update("news", $set_ary, $where_ary);

    return true;
}
