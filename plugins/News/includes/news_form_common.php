<?php

/**
 *  News - Common form functions
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */

/**
 * Get html select formated category selection
 * 
 * TODO: Hay un posible error, si se muevo una subcategoria de padre, y el ID del padre es menor
 * el loop que crea a la lista no encuentra al padre por que aun no esta añadido, se añade antes al
 * hijo que al padre pero no encuentra al padre. Se solvento (supongo igual da otro) temporalmente
 *  ordenando por 'father'
 * 
 * @global array $cfg
 * @global Categories $ctgs
 * @global Multilang $ml
 * @param array $news_data
 * @param string $select_name
 * @return string|boolean #content
 */
function news_getCatsSelect($news_data = null, $select_name = 'news_category', $display_main_cat = 0) {
    global $cfg, $ctgs, $ml;

    defined('MULTILANG') ? $lang_id = $ml->getWebLangID() : $lang_id = 1;

    $select = '<select name="' . $select_name . '" id="news_category">';
    $fathers = [];

    $cats = $ctgs->getCategories('News');

    usort($cats, function($a, $b) {
        return $a['father'] - $b['father'];
    });
    if (!$cats) {
        return false;
    }
    foreach ($cats as $row) {
        $fathers_name = '';
        $added_one = false;

        if (array_key_exists($row['father'], $fathers)) {
            $fathers[$row['cid']] = $fathers[$row['father']] . $row['name'] . '->';
        } else {
            $fathers[$row['cid']] = $row['name'] . '->';
        }
        $row['father'] ? $fathers_name = $fathers[$row['father']] : null;

        if (($cfg['news_allow_submit_main_cats'] && $row['father'] == 0) || $row['father'] != 0 || $display_main_cat) {
            if (($news_data != null) && ($row['cid'] == $news_data['category'])) {
                $select .= "<option selected value='{$row['cid']}'>$fathers_name{$row['name']}</option>";
                $added_one = true;
            } else {
                $select .= "<option value='{$row['cid']}'>$fathers_name{$row['name']}</option>";
                $added_one = true;
            }
        }
    }
    $select .= '</select>';
    return $added_one ? $select : null;
}

/**
 * Std Get POST form data
 * 
 * @global Database $db
 * @global SecureFilter $filter
 * @global SessionManager $sm
 * @global array $cfg
 * @global array $LNG
 * @return array|boolean
 */
function news_form_getPost() {
    global $db, $filter, $sm, $cfg, $LNG;

    //GET
    $form_data['nid'] = $filter->getInt('nid');
    $form_data['news_lang_id'] = $filter->getInt('news_lang_id');
    $form_data['page'] = $filter->getInt('npage');
    //POST    
    $form_data['author_id'] = $filter->postInt('news_author_id', 10, 1);
    $form_data['title'] = $db->escapeStrip($filter->postUtf8Txt('news_title'));
    $form_data['lead'] = $db->escapeStrip($filter->postUtf8Txt('news_lead'));
    $form_data['editor_text'] = $db->escapeStrip($filter->postUtf8Txt('editor_text'));
    $form_data['category'] = $filter->postInt('news_category');
    $form_data['news_lang'] = $filter->postInt('news_lang', 8, 1);
    $form_data['news_translator_id'] = $filter->postInt('news_translator_id');
    $form_data['current_lang_id'] = $filter->postInt('current_lang_id', 255, 1);
    if ($cfg['news_allow_user_drafts']) {
        $form_data['as_draft'] = $filter->postInt('as_draft', 1, 1);
    } else {
        $form_data['as_draft'] = 0;
    }
    // force no remote check if its a draft
    if ($form_data['as_draft']) {
        $form_data['news_source'] = $filter->postUrl('news_source', 255, 1, 1);
        $form_data['news_new_related'] = $filter->postUrl('news_new_related', 255, 1, 1);
        $form_data['news_related'] = $filter->postUrl('news_related', 255, 1, 1);
    } else {
        $form_data['news_source'] = $filter->postUrl('news_source', 255, 1);
        $form_data['news_new_related'] = $filter->postUrl('news_new_related', 255, 1);
        $form_data['news_related'] = $filter->postUrl('news_related', 255, 1);
    }
    $form_data['news_source_title'] = $filter->postAZChar('news_source_title');
    $form_data['news_new_related_title'] = $filter->postAZChar('news_new_related_title');


    if (isset($_POST['news_related_title'])) {
        $news_rel_title_ary = $_POST['news_related_title'];

        foreach ($news_rel_title_ary as $id => $news_rel_title) {
            $form_data['news_related_title'][$id] = $filter->varAZChar($news_rel_title);
        }
    }
    //Author Changes
    if (news_perm_ask('w_news_change_author')) {
        $author = $filter->postUsername('news_author', $cfg['smbasic_max_username'], $cfg['smbasic_min_username']);
        if ($author != false) {
            if (!empty($author) && !($author_data = $sm->getUserByUsername($author))) {
                $form_data['author_id'] = false;
            } else {
                $form_data['author_id'] = $author_data['uid'];
            }
        } else if ($author == $LNG['L_NEWS_ANONYMOUS']) {
            $form_data['author_id'] = 0; //Change to anonymousM
        } else {
            $form_data['author_id'] = false;
        }
    }

    //TRANSLATOR CHANGES
    if (news_perm_ask('w_news_change_author')) {
        $translator = $filter->postUsername('news_translator', $cfg['smbasic_max_username'], $cfg['smbasic_min_username']);
        if ($translator != false) {
            if (!empty($translator) && !($translator_data = $sm->getUserByUsername($translator))) {
                $form_data['news_translator_id'] = false;
            } else {
                $form_data['news_translator_id'] = $translator_data['uid'];
            }
        } else if ($translator == $LNG['L_NEWS_ANONYMOUS']) {
            $form_data['news_translator_id'] = 0; //Change to anonymousM
        } else {
            $form_data['news_translator_id'] = false;
        }
    }

    return $form_data;
}

/**
 * used when edit news
 * 
 * omit langs that already have this news translate
 * @global Multilang $ml
 * @global Database $db
 * @param array $news_data
 * @return boolean|string
 */
function news_get_available_langs($news_data) {
    global $ml, $db;

    $site_langs = $ml->getSiteLangs();
    if (empty($site_langs)) {
        return false;
    }

    $select = '<select name="news_lang" id="news_lang">';
    foreach ($site_langs as $site_lang) {
        if ($site_lang['lang_id'] == $news_data['lang_id']) {
            $select .= "<option selected value='{$site_lang['lang_id']}'>{$site_lang['lang_name']}</option>";
        } else {
            $query = $db->selectAll('news', ['nid' => $news_data['nid'], 'lang_id' => $site_lang['lang_id']], 'LIMIT 1');
            if ($db->numRows($query) <= 0) {
                $select .= "<option value='{$site_lang['lang_id']}'>{$site_lang['lang_name']}</option>";
            }
        }
    }
    $select .= '</select>';

    return $select;
}

/**
 * used when translate a news
 * 
 * omit all already translate langs, exclude original lang too. just show langs without the news translate
 * 
 * @global type $ml
 * @global type $db
 * @param type $nid
 * @param type $page
 * @return boolean
 */
function news_get_missed_langs($nid, $page) {
    global $ml, $db;

    $nolang = 1;

    $site_langs = $ml->getSiteLangs();
    if (empty($site_langs)) {
        return false;
    }

    $select = '<select name="news_lang" id="news_lang">';
    foreach ($site_langs as $site_lang) {
        $query = $db->selectAll('news', ["nid" => $nid, "lang_id" => $site_lang['lang_id'], "page" => "$page"], "LIMIT 1");
        if ($db->numRows($query) <= 0) {
            $select .= "<option value='{$site_lang['lang_id']}'>{$site_lang['lang_name']}</option>";
            $nolang = 0;
        }
    }
    $select .= '</select>';

    return (!empty($nolang)) ? false : $select;
}

/**
 * Used for submit and edit
 * 
 * @global array $LNG
 * @global array $cfg
 * @param array $news_data
 * @return boolean
 */
function news_submit_form_check($news_data) {
    global $LNG, $cfg;

    //USERNAME/AUTHOR
    if ($news_data['author_id'] == false) {
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
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && news_perm_ask('w_news_add_source')) {
        die('[{"status": "11", "msg": "' . $LNG['L_NEWS_E_SOURCE'] . '"}]');
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && news_perm_ask('w_news_add_related')) {
        die('[{"status": "12", "msg": "' . $LNG['L_NEWS_E_RELATED'] . '"}]');
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && news_perm_ask('w_news_add_related')) {
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

/**
 * News form update
 * 
 * @global Database $db
 * @global SecureFilter $filter
 * @param array $news_data
 * @return boolean
 */
function news_form_news_update($news_data) {
    global $db, $filter;

    empty($news_data['featured']) ? $news_data['featured'] = 0 : null;
    //!isset($news_data['news_translator']) ? $news_data['news_translator'] = "" : null;
    !defined('MULTILANG') ? $news_data['news_lang'] = 1 : null;

    $set_ary = [
        'title' => $db->escapeStrip($news_data['title']),
        'lead' => $db->escapeStrip($news_data['lead']),
        'text' => $db->escapeStrip($news_data['editor_text']),
        'author_id' => $news_data['author_id'],
        'category' => $news_data['category'],
        'as_draft' => $news_data['as_draft'] ? $news_data['as_draft'] : 0,
    ];

    //if parent as draft force as draft
    if (($news_data['page'] > 1) && (!isset($news_data['as_draft']) || $news_data['as_draft'] == 0 )) {
        $previous_page = $news_data['page'] - 1;
        $news_parent = get_news_byId($news_data['nid'], $news_data['current_lang_id'], $previous_page);
        ($news_parent['as_draft']) ? $set_ary['as_draft'] = 1 : null;
    }

    if (!empty($news_data['news_lang']) && ($news_data['current_lang_id'] != $news_data['news_lang'])) {
        $set_ary['lang_id'] = $news_data['news_lang'];
    }

    empty($news_data['news_lang']) ? $news_data['news_lang'] = $news_data['current_lang_id'] : null; // add lang id if edit a non one npage
    do_action('news_form_update_set', $set_ary);

    $where_ary = [
        'nid' => $news_data['nid'], 'lang_id' => $news_data['current_lang_id'], 'page' => $news_data['page']
    ];

    $db->update('news', $set_ary, $where_ary);

    //if lang change on main change on childs to ovoid orphan pages
    if (!empty($news_data['news_lang']) && ($news_data['current_lang_id'] != $news_data['news_lang'])) {
        $db->update('news', ['lang_id' => $news_data['news_lang']], ['nid' => $news_data['nid'], 'lang_id' => $news_data['news_lang']]);
    }
    do_action('news_form_update', $news_data);
    //
    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $source_id = $news_data['nid'];
        $plugin = 'News';
        $type = 'source';

        $query = $db->selectAll('links', ['source_id' => $source_id, 'type' => $type, 'plugin' => $plugin], 'LIMIT 1');
        if ($db->numRows($query) > 0) {

            $db->update('links', ['link' => $db->escapeStrip(urldecode($news_data['news_source'])), 'extra' => $news_data['news_source_title']], ['source_id' => $source_id, 'type' => $type, 'plugin' => $plugin]);
        } else {
            $insert_ary = [
                'source_id' => $source_id,
                'plugin' => $plugin,
                'type' => $type,
                'link' => $db->escapeStrip(urldecode($news_data['news_source'])),
                'extra' => !empty($news_data['news_source_title']) ? $db->escapeStrip($news_data['news_source_title']) : '',
            ];
            $db->insert('links', $insert_ary);
        }
    } else {
        $source_id = $news_data['nid'];
        $plugin = 'News';
        $type = 'source';
        $db->delete('links', ['source_id' => $source_id, 'type' => $type, 'plugin' => $plugin], 'LIMIT 1');
    }
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $source_id = $news_data['nid'];
        $plugin = 'News';
        $type = 'related';
        $insert_ary = [
            'source_id' => $source_id,
            'plugin' => $plugin,
            'type' => $type,
            'link' => $db->escapeStrip(urldecode($news_data['news_new_related'])),
            'extra' => $news_data['news_new_related_title'],
        ];
        $db->insert('links', $insert_ary);
    }
    //OLD RELATED

    if (!empty($news_data['news_related'])) {
        foreach ($news_data['news_related'] as $link_id => $value) {
            if ($filter->varInt($link_id)) { //value its checked on post $link_id no             
                if (empty($value)) {
                    $db->delete('links', ['link_id' => $link_id], 'LIMIT 1');
                } else {
                    $db->update('links', ['link' => $db->escapeStrip(urldecode($value)), 'extra' => $news_data['news_related_title'][$link_id]], ['link_id' => $link_id], 'LIMIT 1');
                }
            }
        }
    }
    return true;
}
