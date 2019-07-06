<?php

/**
 *  News - News new lang
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Create/Display new lang form
 * 
 * @global array $LNG
 * @global TPL $tpl
 * @global SessionManager $sm
 * @global Frontend $frontend
 * @param int $news_nid
 * @param int $news_lang_id
 * @param int $news_page  #Num Page
 * @return string|boolean #content
 */
function news_new_lang($news_nid, $news_lang_id, $news_page) {
    global $LNG, $tpl, $sm, $frontend;

    if (!is_array($news_data = get_news_byId($news_nid, $news_lang_id, $news_page))) {
        $frontend->messageBox(['msg' => $news_data]);
        return false;
    }

    $author_data = $sm->getUserByID($news_data['author_id']);
    $news_data['author'] = $author_data['username'];
    $news_data['author_readonly'] = !news_perm_ask('w_news_change_author');
    $news_data['news_add_source'] = news_perm_ask('w_news_add_source');
    $news_data['news_add_related'] = news_perm_ask('w_news_add_related');

    if (!news_perm_ask('w_news_translate')) {
        return $frontend->messageBox(['msg' => 'L_E_NOEDITACCESS']);
    }

    $news_data['news_form_title'] = $LNG['L_NEWS_NEWLANG'];

    $translator = $sm->getSessionUser();

    /*
      if (empty($translator) && $cfg['news_anon_translate']) {
      $translator['username'] = $LNG['L_NEWS_ANONYMOUS'];
      $translator['uid'] = 0;
      } else if (empty($translator)) {
      return $frontend->messageBox(['msg' => "L_NEWS_NO_EDIT_PERMISS"]);
      }

     */
    $news_data['translator'] = $translator['username'];
    $news_data['translator_id'] = $translator['uid'];
    $translator['uid'] > 0 ? $news_data['tos_checked'] = 1 : null;

    if (($site_langs = news_get_missed_langs($news_data['nid'], $news_data['page'])) != false) {
        $news_data['select_langs'] = $site_langs;
    } else {
        return $frontend->messageBox(['msg' => 'L_NEWS_E_ALREADY_TRANSLATE_ALL']);
    }
    $editor = new Editor();
    $news_data['editor'] = $editor->getEditor(['text' => $news_data['text']]);

    $news_data['terms_url'] = $sm->getPage('terms');
    do_action('news_newlang_form_add', $news_data);

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_form', $news_data));
}

/**
 * Create news with new lang process
 * @global array $LNG
 * @global array $cfg
 * @global SessionManager $sm
 * @return boolean
 */
function news_form_newlang_process() {
    global $LNG, $cfg, $sm;

    $user = $sm->getSessionUser();
    if ((!$user || $user['uid'] == 0) && !$cfg['news_anon_translate']) {
        return false;
    }

    $news_data = news_form_getPost();

    if (!news_perm_ask('w_news_translate')) {
        die('[{"status": "4", "msg": "' . $LNG['L_E_NOEDITACCESS'] . '"}]');
    }

    if (!news_newlang_form_process($news_data)) {
        return false;
    }

    if (news_newlang_submit($news_data)) {
        die('[{"status": "ok", "msg": "' . $LNG['L_NEWS_TRANSLATE_SUCCESSFUL'] . '", "url": "' . $cfg['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "1", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }
}

/**
 * Insert the new lang news after submit
 * 
 * @global array $cfg
 * @global Database $db
 * @global array $LNG
 * @param array $news_data
 * @return boolean
 */
function news_newlang_submit($news_data) {
    global $cfg, $db, $LNG;

    if (!defined('MULTILANG')) {
        die('[{"status": "10", "msg": "' . $LNG['L_NEWS_NOMULTILANG_SUPPORT'] . '"}]');
    }

    $query = $db->selectAll('news', ['nid' => $news_data['nid'], 'lang_id' => $news_data['news_lang'], 'page' => $news_data['page']]);
    if ($db->numRows($query) > 0) { //already exist
        die('[{"status": "10", "msg": "' . $LNG['L_NEWS_ALREADY_EXIST'] . '"}]');
    }
    //GET original main news (page 1) for copy values
    $orig_news_nid = $news_data['nid'];
    $orig_news_lang_id = $news_data['old_news_lang_id'];

    $query = $db->selectAll('news', ['nid' => $orig_news_nid, 'lang_id' => $orig_news_lang_id, 'page' => 1], 'LIMIT 1');
    $orig_news = $db->fetch($query);
    $moderation = $cfg['news_moderation'];

    $insert_ary = [
        'nid' => $news_data['nid'],
        'lang_id' => $news_data['news_lang'],
        'page' => $news_data['page'],
        'translator_id' => $news_data['news_translator_id'],
        'title' => $db->escapeStrip($news_data['title']),
        'lead' => $db->escapeStrip($news_data['lead']),
        'text' => $db->escapeStrip($news_data['editor_text']),
        'author_id' => $orig_news['author_id'],
        'category' => $orig_news['category'],
        'moderation' => $moderation
    ];
    $db->insert('news', $insert_ary);

    return true;
}

/**
 * Check news new lang  form process
 * 
 * @global array $LNG
 * @global array $cfg
 * @return boolean
 */
function news_newlang_form_process() {
    global $LNG, $cfg;

    $news_data = news_form_getPost();

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
    if (empty($news_data['news_lang']) || empty($news_data['nid'])) {
        die('[{"status": "8", "msg": "' . $LNG['L_NEWS_INTERNAL_ERROR'] . '"}]');
    }

    return true;
}
