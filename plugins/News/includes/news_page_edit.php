<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_edit() {
    global $cfg, $LNG, $acl_auth, $tpl, $filter, $sm;

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
    if (!news_check_edit_authorized($news_data)) {
        return false; // error already setting in news_check....
    }
    $news_data['news_form_title'] = $LNG['L_NEWS_EDIT_NEWS'];

    
    if ($user && !defined('ACL') && $user['isFounder']) {
        $news_data['author_readonly'] = "readonly=\"readonly\"";
        $news_data['can_add_related'] = 1;
        $news_data['can_add_source'] = 1;
    } else {
        $news_data['author_readonly'] = "";       
    }        
    
    if ($news_data['news_auth'] == "admin" || $news_data['news_auth'] == "author") {
        $news_data['select_categories'] = news_getCatsSelect($news_data);
        if (($news_source = get_news_source_byID($news_data['nid'])) != false) {
            $news_data['news_source'] = $news_source['link'];
        }
        if ($cfg['display_news_related'] && ($news_related = news_get_related($news_data['nid']))) {
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

function news_check_edit_authorized(& $news_data) {
    global $cfg, $sm, $acl_auth;

    if (!($user = $sm->getSessionUser())) {
        return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
    } else {
        $news_data['tos_checked'] = 1;
    }
    if ((defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) || (!defined('ACL') && $user['isAdmin'])) {
        $news_data['news_auth'] = "admin";
        return $news_data;
    }
    if ((($news_data['author'] == $user['username']) && $cfg['NEWS_AUTHOR_CAN_EDIT'])) {
        $news_data['news_auth'] = "author";
        return $news_data;
    }
    if ((($news_data['translator'] == $user['username']) && $cfg['NEWS_TRANSLATOR_CAN_EDIT'])) {
        $news_data['news_auth'] = "translator";
        return $news_data;
    }

    return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
}

function news_form_edit_process() {
    global $LNG, $cfg, $frontend;

    $news_data = news_form_getPost();

    if (empty($news_data['nid']) || empty($news_data['lang_id']) || empty($news_data['page'])) {
        return $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    }
    if (!($news_orig = get_news_byId($news_data['nid'], $news_data['lang_id'], $news_data['page']))) {
        return false; // error already setting in get_news
    }
    if (!news_check_edit_authorized($news_orig)) {
        return false; // error already setting in news_check....
    }

    if (news_form_common_field_check($news_data) == false) {
        return false;
    }
    if ($news_orig['news_auth'] == "admin" || $news_orig['news_auth'] == "author") {
        if (news_form_extra_check($news_data) == false) {
            return false;
        }
    }
    //UPDATE or translate
    if ($news_orig['news_auth'] == "admin" || $news_orig['news_auth'] == "author") {
        if (news_full_update($news_data)) {
            die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
        } else {
            die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
        }
    } else if ($news_orig['news_auth'] == "translator") {
        if (news_limited_update($news_data)) {
            die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_UPDATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
        } else {
            die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
        }
    }

    return true;
}

function news_full_update($news_data) {
    global $cfg, $db, $ml, $filter;

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);
    } else {
        $lang_id = 1;
    }

    $query = $db->select_all("news", ["nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}"]);
    if (($num_pages = $db->num_rows($query)) <= 0) {
        return false;
    }
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = "";
    empty($news_data['featured']) ? $news_data['featured'] = 0 : false; //news_clean_featured($lang_id) ;
    !isset($news_data['news_translator']) ? $news_data['news_translator'] = "" : false;


    $set_ary = [
        "lang_id" => $lang_id, "title" => $news_data['title'], "lead" => $news_data['lead'], "text" => $news_data['editor_text'],
        "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], "category" => $news_data['category'],
        "lang" => $news_data['lang'], "acl" => $acl, "translator" => $news_data['news_translator']
    ];

    do_action("news_fulledit_mod_set", $set_ary);

    $where_ary = [
        "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => "{$news_data['page']}"
    ];
    $db->update("news", $set_ary, $where_ary);
    //UPDATE ACL/CATEGORY/LANG/FEATURE on pages;
    if ($num_pages > 1) {
        $page_set_ary = [
            "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'],
            "category" => $news_data['category'], "lang" => $news_data['lang']
        ];
        $page_where_ary = [
            "nid" => "{$news_data['nid']}", "lang_id" => "{$news_data['lang_id']}", "page" => ["operator" => "!=", "value" => "{$news_data['page']}"]
        ];
        $db->update("news", $page_set_ary, $page_where_ary);
    }

    do_action("news_form_update", $news_data); //MOD
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
