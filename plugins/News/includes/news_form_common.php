<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */


function news_getCatsSelect($news_data = null) {
    global $db, $acl_auth, $sm, $cfg, $ctgs;

    $user = $sm->getSessionUser();

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

    $user = $sm->getSessionUser();
    //Admin can change author (if the author not exists use admin one.
    if (($user && !defined('ACL') && $user['isFounder']) || ( $user && defined('ACL') && ( $acl_auth->acl_ask('news_admin||admin_all') ) == true)) {
        if (($form_data['author'] = $filter->post_strict_chars("news_author", 25, 3)) != false && ($form_data['author'] != $user['username'])) {
            if (($selected_user = $sm->getUserByUsername($form_data['author']))) {
                $form_data['author_id'] = $selected_user['uid'];
            } else {
                unset($form_data['author']); //clear use session username
            }
        }
    }

    if (empty($form_data['author']) || empty($form_data['author_id'])) {
        if (!empty($user)) {
            $form_data['author'] = $user['username'];
            $form_data['author_id'] = $user['uid'];
        } else {
            $form_data['author'] = $LNG['L_NEWS_ANONYMOUS'];
            $form_data['author_id'] = 0;
        }
    }

    $form_data['nid'] = $filter->get_int("nid", 11, 1);
    $form_data['lang_id'] = $filter->get_int("lang_id", 8, 1);
    $form_data['page'] = $filter->get_int("npage", 11, 1);
    $form_data['title'] = $db->escape_strip($filter->post_UTF8_txt("news_title"));
    $form_data['lead'] = $db->escape_strip($filter->post_UTF8_txt("news_lead"));
    $form_data['editor_text'] = $db->escape_strip($filter->post_UTF8_txt("editor_text"));
    $form_data['category'] = $filter->post_int("news_category", 8);
    $form_data['featured'] = $filter->post_int("news_featured", 1, 1);
    $form_data['lang'] = $filter->post_AZChar("news_lang", 2);
    $form_data['acl'] = $filter->post_strict_chars("news_acl");
    $form_data['news_source'] = $filter->post_url("news_source");
    $form_data['news_new_related'] = $filter->post_url("news_new_related");
    $form_data['news_related'] = $filter->post_url("news_related");
    $form_data['news_translator'] = $filter->post_strict_chars("news_translator", 25, 3);
    $form_data['news_translator_id'] = $filter->post_int("news_translator_id", 11, 1);

    return $form_data;
}

function news_form_common_field_check($news_data) {
    global $cfg, $LNG;

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

    return true;
}

function news_form_extra_check(&$news_data) {
    global $cfg, $LNG;
    //CATEGORY
    if ($news_data['category'] == false) {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
    //Source check valid if input
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && $cfg['NEWS_SOURCE']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_SOURCE'] . '"}]');
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && $cfg['NEWS_RELATED']) {
        die('[{"status": "7", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && $cfg['NEWS_RELATED']) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    /* Custom /Mod Validators */
    if (($return = do_action("news_form_add_check", $news_data)) && !empty($return)) {
        die('[{"status": "9", "msg": "' . $return . '"}]');
    }
    //FEATURED NOCHECK ATM
    //ACL NO CHECK ATM

    return true;
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
