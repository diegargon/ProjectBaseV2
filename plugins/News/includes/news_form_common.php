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

        if (($cfg['news_allow_send_main_cats'] && $row['father'] == 0) || $row['father'] != 0) {
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
