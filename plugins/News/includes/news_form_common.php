<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

function news_getCatsSelect($news_data = null) {
    global $db, $acl_auth, $sm, $cfg, $ctgs;

    if (defined('MULTILANG')) {
        global $ml;
        $lang_id = $ml->getSessionLangId();
    } else {
        $lang_id = 1;
    }
    $select = "<select name='news_category' id='news_category'>";
    $fathers = [];

    $cats = $ctgs->getCategories("News");

    foreach ($cats as $row) {
        $fathers_name = "";

        if (array_key_exists($row['father'], $fathers)) {
            $fathers[$row['cid']] = $fathers[$row['father']] . $row['name'] . "->";
        } else {
            $fathers[$row['cid']] = $row['name'] . "->";
        }
        $row['father'] ? $fathers_name = $fathers[$row['father']] : null;

        if (($cfg['news_allow_submit_main_cats'] && $row['father'] == 0) || $row['father'] != 0) {
            if (($news_data != null) && ($row['cid'] == $news_data['category'])) {
                $select .= "<option selected value='{$row['cid']}'>$fathers_name{$row['name']}</option>";
            } else {
                $select .= "<option value='{$row['cid']}'>$fathers_name{$row['name']}</option>";
            }
        }
    }
    $select .= "</select>";
    return $select;
}

function news_form_getPost() {
    global $acl_auth, $sm, $LNG, $db, $filter;

    //GET
    $form_data['nid'] = $filter->get_int("nid", 11, 1);
    $form_data['old_news_lang_id'] = $filter->get_int("news_lang_id", 8, 1);
    $form_data['news_lang_id'] = $filter->get_int("news_lang_id", 8, 1);
    $form_data['page'] = $filter->get_int("npage", 11, 1);
    //POST
    $form_data['author'] = $filter->post_strict_chars("news_author", 25, 3);
    $form_data['author_id'] = $filter->post_int("news_author_id", 11, 1);
    $form_data['title'] = $db->escape_strip($filter->post_UTF8_txt("news_title"));
    $form_data['lead'] = $db->escape_strip($filter->post_UTF8_txt("news_lead"));
    $form_data['editor_text'] = $db->escape_strip($filter->post_UTF8_txt("editor_text"));
    $form_data['category'] = $filter->post_int("news_category", 8);
    $form_data['news_lang'] = $filter->post_AZChar("news_lang", 2, 2);
    $form_data['news_source'] = $filter->post_url("news_source");
    $form_data['news_new_related'] = $filter->post_url("news_new_related");
    $form_data['news_related'] = $filter->post_url("news_related");
    $form_data['news_translator'] = $filter->post_strict_chars("news_translator", 25, 3);
    $form_data['news_translator_id'] = $filter->post_int("news_translator_id", 11, 1);

    return $form_data;
}

//used when edit news, omit langs that already have this news translate
function news_get_available_langs($news_data) {
    global $cfg, $ml, $db;

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) {
        return false;
    }

    empty($news_data['lang']) ? $match_lang = $news_data['lang'] : $match_lang = $cfg['WEB_LANG'];

    $select = "<select name='news_lang' id='news_lang'>";
    foreach ($site_langs as $site_lang) {
        if ($site_lang['iso_code'] == $match_lang) {
            $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        } else {
            $query = $db->select_all("news", ["nid" => $news_data['nid'], "lang_id" => $site_lang['lang_id']], "LIMIT 1");
            if ($db->num_rows($query) <= 0) {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            }
        }
    }
    $select .= "</select>";

    return $select;
}

//used when translate a news, omit all already translate langs, exclude original lang too. just show langs without the news translate
function news_get_missed_langs($nid, $page) {
    global $ml, $db;

    $nolang = 1;

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) {
        return false;
    }

    $select = "<select name='news_lang' id='news_lang'>";
    foreach ($site_langs as $site_lang) {
        $query = $db->select_all("news", ["nid" => $nid, "lang_id" => $site_lang['lang_id'], "page" => "$page"], "LIMIT 1");
        if ($db->num_rows($query) <= 0) {
            $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            $nolang = 0;
        }
    }
    $select .= "</select>";

    return (!empty($nolang)) ? false : $select;
}

function news_submit_edit_form_check($news_data) {
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
        die('[{"status": "4", "msg": "' . $LNG['L_NEWS_TITLE_MINMAX_ERROR'] . '"}]');
    }
    //LEAD
    if (isset($_GET['npage']) && $_GET['npage'] > 1) {
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['news_lead_max_length'])) {
            die('[{"status": "5", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    } else {
        if ($news_data['lead'] == false) {
            die('[{"status": "6", "msg": "' . $LNG['L_NEWS_LEAD_ERROR'] . '"}]');
        }
        if ((mb_strlen($news_data['lead'], $cfg['CHARSET']) > $cfg['news_lead_max_length']) ||
                (mb_strlen($news_data['lead'], $cfg['CHARSET']) < $cfg['news_lead_min_length'])
        ) {
            die('[{"status": "7", "msg": "' . $LNG['L_NEWS_LEAD_MINMAX_ERROR'] . '"}]');
        }
    }
    //TEXT

    if ($news_data['editor_text'] == false) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_TEXT_ERROR'] . '"}]');
    }
    $text_size = mb_strlen($news_data['editor_text'], $cfg['CHARSET']);

    if (($text_size > $cfg['news_text_max_length']) || ($text_size < $cfg['news_text_min_length'])) {
        die('[{"status": "9", "msg": "' . $LNG['L_NEWS_TEXT_MINMAX_ERROR'] . '"}]');
    }
    //CATEGORY
    if ($news_data['category'] == false) {
        die('[{"status": "10", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    //Source check valid if input    
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && $cfg['display_news_source']) {
        die('[{"status": "11", "msg": "' . $LNG['L_NEWS_E_SOURCE'] . '"}]');
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && $cfg['display_news_related']) {
        die('[{"status": "12", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && $cfg['display_news_related']) {
        die('[{"status": "13", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    // Custom /Mod Validators 
    if (($return = do_action("news_form_add_check", $news_data)) && !empty($return)) {
        die('[{"status": "14", "msg": "' . $return . '"}]');
    }
    //FEATURED NOCHECK ATM
    //ACL NO CHECK ATM

    return true;
}

function news_form_news_update($news_data) {
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
