<?php

/**
 *  News - News view template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * news show page
 * 
 * @global array $cfg
 * @global tpl $tpl
 * @global sm $sm
 * @global ml $ml
 * @global filter $filter
 * @global frontend $frontend
 * @global timeUtil $timeUtil
 * @return boolean
 */
function news_show_page() {
    global $cfg, $tpl, $ml, $sm, $filter, $frontend, $timeUtil, $LNG;

    $news_data = [];

    if ((empty($_GET['nid'])) || ($nid = $filter->getInt('nid')) == false) {
        $frontend->messageBox(['msg' => 'L_NEWS_NOT_EXIST']);
        return false;
    }
    if (!empty($_GET['news_lang_id'])) {
        $news_lang_id = $filter->getInt('news_lang_id');
    } else {
        $news_lang_id = 1;
    }

    ($cfg['allow_multiple_pages'] && !empty($_GET['npage'])) ? $page = $filter->getInt('npage') : $page = 1;

    if (!is_array($news_data = get_news_byId($nid, $news_lang_id, $page))) { //Not array, its a error
        $frontend->messageBox(['msg' => $news_data]);
        return false;
    }

    if (!news_perm_ask('r_news_view||w_news_adm_all||w_news_moderation')) {
        $frontend->messageBox(['msg' => 'L_E_NOVIEWACCESS']);
        return false;
    }

    news_catch_admin_actions($news_data);

    if ($news_data['as_draft'] && ($sm->getSessionUserId() != $news_data['author_id'])) {
        $frontend->messageBox(['msg' => 'L_NEWS_E_VIEW_DRAFT']);
        return false;
    }

    if ($cfg['news_moderation'] && $news_data['moderation'] && !news_perm_ask('w_news_moderation')) {
        $frontend->messageBox(['msg' => 'L_NEWS_ERROR_WAITINGMOD']);
        return false;
    }

    $editor = new Editor();

    $news_data['news_admin_nav'] = news_nav_options($news_data);
    $cfg['allow_multiple_pages'] ? $news_data['pager'] = news_pager($news_data) : null;

    //$news_data['title'] = str_replace('\r\n', '', $news_data['title']);
    //$news_data['lead'] = str_replace('\r\n', PHP_EOL, $news_data['lead']);
    $news_data['title'] = stripcslashes($news_data['title']);
    $news_data['lead'] = stripcslashes($news_data['lead']);
    $news_data['date'] = $timeUtil->formatDbDate($news_data['created']);
    $news_data['last_edited'] = $timeUtil->formatDbDate($news_data['last_edited']);
    $author_data = $sm->getUserByID($news_data['author_id']);
    $news_data['author'] = $author_data['username'];
    $news_data['author_uid'] = $news_data['author_id'];
    $news_data['text'] = $editor->parseText(htmlspecialchars($news_data['text']));

    if (isset($news_data['other_langs'])) {
        $other_langs = $news_data['other_langs'];
        $news_data['sel_other_langs'] = '<div class="other_langs">';
        foreach ($other_langs as $o_lang_id) {
            if ($cfg['FRIENDLY_URL']) {
                $o_lang_url = "/{$ml->getWebLang()}/news/{$news_data['nid']}/{$news_data['page']}/{$o_lang_id}/";
            } else {
                $o_lang_url = "/index.php?module=News&page=view_news.php?nid={$news_data['nid']}&npage={$news_data['page']}&news_lang={$o_lang_id}";
            }
            $news_data['sel_other_langs'] .= '<a href="' . $o_lang_url . '">' . $ml->idToName($o_lang_id) . '</a>';
        }
        $news_data['sel_other_langs'] .= '</div>';
    }
    if (!empty($news_data['translator_id'])) {
        $translator = $sm->getUserByID($news_data['translator_id']);
        $news_data['translator'] = '<a rel="nofollow" href="/' . $cfg['WEB_LANG'] . '/profile&viewprofile=' . $translator['uid'] . '">' . $translator['username'] . '</a>';
    }
    $author = $sm->getUserByID($news_data['author_id']);

    $cfg['news_stats'] ? news_stats($nid, $news_data['lang_id'], $news_data['page']) : null;
    //HEAD MOD       
    $cfg['PAGE_TITLE'] = $news_data['title'];
    $cfg['news_meta_opengraph'] ? news_add_social_meta($news_data) : null;
    $cfg['PAGE_DESC'] = $news_data['title'] . ":" . $news_data['lead'];
    $cfg['PAGE_AUTHOR'] = $author['username'];
    //END HEAD MOD
    !empty($author['avatar']) ? $news_data['author_avatar'] = $author['avatar'] : $news_data['author_avatar'] = $cfg['smbasic_default_img_avatar'];

    get_news_links($news_data);

    $cfg['news_breadcrum'] ? $news_data['news_breadcrum'] = getNewsCatBreadcrumb($news_data) : null;

    do_action('news_show_page', $news_data);

    ($cfg['ITS_BOT'] && $cfg['news_microdata']) ? $news_data['ITEM_OL'] = 1 : null;

    if ($cfg['ITS_BOT'] && $cfg['news_data_structure']) {
        $matchs = [];
        preg_match('/src=\"(.*?)\"/i', $news_data['text'], $matchs);
        if (isset($matchs) && !empty($matchs[1])) {
            $news_data['ITEM_MAINIMAGE'] = $matchs[1];
        }
        $news_data['ITEM_CREATED'] = preg_replace('/ /', 'T', $news_data['created']) . 'Z';
        $news_data['ITEM_MODIFIED'] = preg_replace('/ /', 'T', $news_data['last_edited']) . 'Z';
        $cats = explode(' ', trim(strip_tags($news_data['news_breadcrum'])));
        if (!empty($cats)) {
            $news_data['ITEM_SECTIONS'] = '';
            foreach ($cats as $cat) {
                $news_data['ITEM_SECTIONS'] .= '"articleSection": "' . trim($cat) . '",';
            }
        }
        $tpl->addtoTplVar('POST_ACTION_ADD_TO_BODY', $tpl->getTplFile('News', 'news_body_struct', $news_data));
    }

    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_body', $news_data));

    return true;
}

/**
 * Catch admin actions
 * 
 * @global filter $filter
 * @param array $news_data
 * @param array $perms
 * @return boolean
 */
function news_catch_admin_actions(&$news_data) {
    global $filter;

    $news_lang_id = $filter->getInt('news_lang_id');
    $news_nid = $filter->getInt('nid');
    $news_page = $filter->getInt('npage');

    if (empty($news_lang_id) || empty($news_nid)) {
        return false;
    }
    /* DELETE */
    if (!empty($_GET['news_delete']) && news_perm_ask('w_news_delete')) {
        news_delete($news_nid, $news_lang_id);
        $srv_referer = $filter->srvReferer();
        header('Location: /');
    }
    if (!empty($_GET['news_delete_page']) && news_perm_ask('w_news_delete')) {
        news_delete($news_nid, $news_lang_id, $news_page);
        $srv_referer = $filter->srvReferer();
        header('Location: /');
    }
    /* APPROVE */
    if (!empty($_GET['news_approved']) && news_perm_ask('w_news_moderation')) {
        news_approved($news_nid, $news_lang_id, $news_page);
        $news_data['moderation'] = 0;
    }
    /* FEATURE */
    if (isset($_GET['news_featured']) && news_perm_ask('w_news_featured')) {
        empty($_GET['news_featured']) ? $news_featured = 0 : $news_featured = 1;
        news_featured($news_nid, $news_lang_id, $news_featured);
        $news_data['featured'] = $news_featured;
    }
    /* FRONTPAGE */
    if (isset($_GET['news_frontpage']) && news_perm_ask('w_news_frontpage')) {
        empty($_GET['news_frontpage']) ? $news_frontpage = 0 : $news_frontpage = 1;
        news_frontpage($news_nid, $news_lang_id);
        $news_data['frontpage'] = $news_frontpage;
    }

    return true;
}

/**
 * News navigation options
 * 
 * @global array $LNG
 * @global array $cfg
 * @param array $news
 * @param array $perms
 * @return array
 */
function news_nav_options($news) {
    global $LNG, $cfg, $sm;

    $content = '';
    $news_url_args = "&nid={$news['nid']}&news_lang_id={$news['lang_id']}&npage={$news['page']}";

    $view_news_url = "/{$cfg['CON_FILE']}?module=News&page=view_news" . $news_url_args;
    $edit_news_url = "/{$cfg['CON_FILE']}?module=News&page=edit_news" . $news_url_args;

    $user = $sm->getSessionUser();

    //Only admin can change but show link disabled to all in frontpage, and feature
    /* FEATURE */

    if (news_perm_ask('w_news_feature||w_news_adm_all')) {
        if ($news['page'] == 1) {
            if ($news['featured'] == 1) {
                $content .= '<li><a class="link_active" rel="nofollow" href="' . $view_news_url . '&news_featured=0&featured_value=0">' . $LNG['L_NEWS_FEATURED'] . '</a></li>';
            } else {
                $content .= '<li><a rel="nofollow" href="' . $view_news_url . '&news_featured=1&featured_value=1">' . $LNG['L_NEWS_FEATURED'] . '</a></li>';
            }
        }
    } else if ($news['featured'] == 1 && $news['page'] == 1) {
        $content .= '<li><a class="link_active" rel="nofollow" href="">' . $LNG['L_NEWS_FEATURED'] . '</a></li>';
    }

    /* FRONTPAGE */

    if (news_perm_ask('w_news_frontpage||w_news_adm_all')) {
        if ($news['page'] == 1) {
            if ($news['frontpage']) {
                $content .= '<li><a class="link_active" rel="nofollow" href="' . $view_news_url . '&news_frontpage=0">' . $LNG['L_NEWS_FRONTPAGE'] . '</a></li>';
            } else {
                $content .= '<li><a rel="nofollow" href="' . $view_news_url . '&news_frontpage=1">' . $LNG['L_NEWS_FRONTPAGE'] . '</a></li>';
            }
        }
    } else if ($news['frontpage'] && $news['page'] == 1) {
        $content .= '<li><a class="link_active" rel="nofollow" href="">' . $LNG['L_NEWS_FRONTPAGE'] . '</a></li>';
    }

    /* EDIT */

    if ($user['uid'] != 0 && $user['uid'] == $news['author_id']) {
        if (news_perm_ask('w_news_edit_own||w_news_adm_all')) {
            $content .= '<li><a rel="nofollow" href="' . $edit_news_url . '&newsedit=1">' . $LNG['L_NEWS_EDIT'] . '</a></li>';
        }
    } else {
        if (news_perm_ask('w_news_edit||w_news_adm_all')) {
            $content .= '<li><a rel="nofollow" href="' . $edit_news_url . '&newsedit=1">' . $LNG['L_NEWS_EDIT'] . '</a></li>';
        }
    }

    /* CREATE NEW PAGE */

    if ($cfg['allow_multiple_pages'] && (( $user['uid'] == $news['author_id'] && $user['uid'] != 0) || $user['isFounder'])) {
        if (news_perm_ask('w_news_add_pages')) {
            $content .= '<li><a rel="nofollow" href="' . $edit_news_url . '&newpage=1">' . $LNG['L_NEWS_NEW_PAGE'] . '</a></li>';
        }
    }

    // TRANSLATE  TODO: ANON TRANSLATE?

    if ($user['uid'] == $news['author_id'] && $user['uid'] != 0) {
        if (news_perm_ask('w_news_own_translate||w_news_translate||w_news_adm_all')) {
            $content .= "<li><a rel='nofollow' href='$edit_news_url&news_new_lang=1'>{$LNG['L_NEWS_NEWLANG']}</a></li>";
        }
    } else {
        if (news_perm_ask('w_news_translate||w_news_adm_all')) {
            $content .= "<li><a rel='nofollow' href='$edit_news_url&news_new_lang=1'>{$LNG['L_NEWS_NEWLANG']}</a></li>";
        }
    }

    /* DELETE NEWS (ALL) */
    if ($user['uid'] == $news['author_id'] && $user['uid'] != 0) {
        if (news_perm_ask('w_news_delete_own||w_news_adm_all||w_news_delete')) {
            if ($news['page'] <= 1) {
                $content .= "<li><a rel='nofollow' href='$view_news_url&news_delete=1' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE']}</a></li>";
            } else {
                $content .= "<li><a rel='nofollow' href='$view_news_url&news_delete_page={$news['page']}' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE_PAGE']}</a></li>";
            }
        }
    } else {
        if (news_perm_ask('w_news_delete||w_news_adm_all')) {
            if ($news['page'] <= 1) {
                $content .= "<li><a rel='nofollow' href='$view_news_url&news_delete=1' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE']}</a></li>";
            } else {
                $content .= "<li><a rel='nofollow' href='$view_news_url&news_delete_page={$news['page']}' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE_PAGE']}</a></li>";
            }
        }
    }

    /* APPROVE */
    if ($news['page'] == 1 && $news['moderation'] == 1) {
        if (news_perm_ask('w_news_moderation||w_news_adm_all')) {
            $content .= "<li><a rel='nofollow' href='$view_news_url&news_approved=1'>{$LNG['L_NEWS_APPROVED']}</a></li>";
        }
    }

    return $content;
}

function news_pager($news_page) {
    global $cfg;

    if ($news_page['num_pages'] < 2) {
        return false;
    }

    $content = '<div id="pager"><ul>';

    $news_page['page'] == 1 ? $a_class = 'class="active"' : $a_class = '';
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$cfg['WEB_LANG']}/news/{$news_page['nid']}/1/{$news_page['lang_id']}/$friendly_title'>1</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&news_lang_id={$news_page['lang_id']}&npage=1&news_lang_id={$news_page['lang_id']}'>1</a></li>";
    }

    $pager = page_pager($cfg['news_pager_max'], $news_page['num_pages'], $news_page['page']);
    for ($i = $pager['start_page']; $i < $pager['limit_page']; $i++) {
        $news_page['page'] == $i ? $a_class = 'class="active"' : $a_class = '';
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_page['title']);
            $content .= "<li><a $a_class href='/{$cfg['WEB_LANG']}/news/{$news_page['nid']}/$i/{$news_page['lang_id']}/$friendly_title'>$i</a></li>";
        } else {
            $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&npage=$i&news_lang_id={$news_page['lang_id']}'>$i</a></li>";
        }
    }
    $news_page['page'] == $news_page['num_pages'] ? $a_class = 'class="active"' : $a_class = '';
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$cfg['WEB_LANG']}/news/{$news_page['nid']}/{$news_page['num_pages']}/{$news_page['lang_id']}/$friendly_title'>{$news_page['num_pages']}</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&npage={$news_page['num_pages']}&news_lang_id={$news_page['lang_id']}'>{$news_page['num_pages']}</a></li>";
    }
    $content .= '</ul></div>';

    return $content;
}

function page_pager($max_pages, $num_pages, $actual_page) {
    $addition = 0;
    $middle = (round(($max_pages / 2), 0, PHP_ROUND_HALF_DOWN) );
    $start_page = $actual_page - $middle;

    if ($start_page < 2) {
        if ($start_page < 0) {
            $addition = ($start_page * -1) + 2;
        } else if ($start_page == 0) {
            $addition = $start_page + 2;
        } else {
            $addition = $start_page;
        }
        $start_page = 2;
    }

    $limit_page = $actual_page + $middle + $addition;
    $limit_page > $num_pages ? $limit_page = $num_pages : null;

    if (($max_pages + $start_page) > $limit_page) {
        $start_page = $start_page - (($max_pages + $start_page) - $limit_page);
    }
    $start_page < 2 ? $start_page = 2 : null;

    $pager['start_page'] = $start_page;
    $pager['limit_page'] = $limit_page;

    return $pager;
}

function news_delete($nid, $lang_id, $page = null) {
    global $db;

    $delete_ary = [
        'nid' => $nid,
        'lang_id' => $lang_id
    ];

    if (!empty($page) && $page > 1) {
        $delete_ary['page'] = $page;
        $LIMIT = 'LIMIT 1';
    } else {
        $LIMIT = null;
    }

    $db->delete('news', $delete_ary);

    if (!empty($page) && $page > 1) {
        $query = $db->select('news', 'num_pages', ['nid' => $nid, 'lang_id' => $lang_id], 'LIMIT 1');
        $news_data = $db->fetch($query);
        $num_pages = --$news_data['num_pages'];
        $db->update('news', ['num_pages' => $num_pages], ['nid' => $nid, 'lang_id' => $lang_id]);
    }

    //check if other lang exist if not delete all links and call for delete other possible mod data.
    $query = $db->selectAll('news', ['nid' => $nid], 'LIMIT 1'); //check if other lang
    if ($db->numRows($query) <= 0) {
        $db->delete('links', ['plugin' => 'News', 'source_id' => $nid]);
        do_action('news_delete_mod', $nid);
    }
    return true;
}

function news_approved($nid, $lang_id, $news_page) {
    global $db;

    if (empty($nid) || empty($lang_id) || empty($news_page)) {
        return false;
    }
    $db->update('news', ['moderation' => 0], ['nid' => $nid, 'lang_id' => $lang_id, 'page' => $news_page], 'LIMIT 1');

    return true;
}

function news_featured($nid, $lang_id, $featured) {
    global $db, $timeUtil;

    $time = $timeUtil->getTimeNow();

    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $update_ary = ['featured' => $featured];
    $featured == 1 ? $update_ary['featured_date'] = $time : null;
    $db->update('news', $update_ary, ['nid' => $nid, 'lang_id' => $lang_id], 'LIMIT 1');

    return true;
}

function news_frontpage($nid, $lang_id) {
    global $db;

    if (empty($nid) || empty($lang_id) || $nid <= 0 && $lang_id <= 0) {
        return false;
    }

    $db->toggleField('news', 'frontpage', ['nid' => $nid, 'lang_id' => $lang_id]);
    return true;
}

function news_stats($nid, $lang_id, $page) {
    global $db, $sm;

    $user = $sm->getSessionUser();
    if (!$user['isAdmin']) {
        $db->plusOne('news', 'visits', ['nid' => $nid, 'lang_id' => $lang_id, 'page' => $page], 'LIMIT 1');
    }

    //$cfg['news_adv_stats'] ? news_adv_stats($nid, $lang) : null;
}

function news_adv_stats($nid, $lang_id) {
    global $db, $sm, $filter;

    $user = $sm->getSessionUser();
    empty($user) ? $user['uid'] = 0 : null; //Anon        
    $ip = $filter->srvRemoteAddr();
    $hostname = gethostbyaddr($ip);
    $where_ary = [
        'type' => 'user_visits_page',
        'plugin' => 'News',
        'lang' => $lang_id,
        'rid' => $nid,
        'uid' => $user['uid']
    ];
    $user['uid'] == 0 ? $where_ary['ip'] = $ip : null;

    $query = $db->selectAll('adv_stats', $where_ary, 'LIMIT 1');

    $user_agent = $filter->srvUserAgent();
    $referer = $filter->srvReferer();

    if ($db->numRows($query) > 0) {
        $user_adv_stats = $db->fetch($query);
        $counter = ++$user_adv_stats['counter'];
        $db->update('adv_stats', ['counter' => $counter, 'user_agent' => $user_agent, 'referer' => $referer], ['advstatid' => $user_adv_stats['advstatid']]);
    } else {
        $insert_ary = [
            'plugin' => 'News',
            'type' => 'user_visits_page',
            'rid' => $nid,
            'lang' => $lang_id,
            'uid' => $user['uid'],
            'ip' => $ip,
            'hostname' => $hostname,
            'user_agent' => $user_agent,
            'referer' => $referer,
            'counter' => 1
        ];
        $db->insert('adv_stats', $insert_ary);
    }

    if ((!empty($referer)) && ( (strpos($referer, '://' . $_SERVER['SERVER_NAME']) ) === false)) {
        $query = $db->selectAll('adv_stats', ['type' => 'referers_only', 'referer' => $referer], 'LIMIT 1');
        if ($db->numRows($query) > 0) {
            $allreferers = $db->fetch($query);
            $counter = ++$allreferers['counter'];
            $db->update('adv_stats', ['counter' => $counter], ['advstatid' => $allreferers['advstatid']]);
        } else {
            $insert_ary = [
                'plugin' => 'News',
                'type' => 'referers_only',
                'referer' => $referer,
                'counter' => 1,
            ];
            $db->insert('adv_stats', $insert_ary);
        }
    }
}

function news_add_social_meta($news) {
    global $tpl, $cfg, $filter, $ml, $ctgs;
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
    $news['url'] = $protocol . $_SERVER['HTTP_HOST'] . $filter->srvRequestUri();
    $match_regex = '/\[.*img.*\](.*)\[\/.*img\]/';
    $match = '';
    preg_match($match_regex, $news['text'], $match);
    if (!empty($match[1])) {
        $url = preg_replace('/\[S\]/si', $cfg['img_selector'] . '/', $match[1]);
        $cfg['IMG_UPLOAD_DIR'] = 'news_img'; //TODO i forget why add a TODO here :)
        $news['mainimage'] = $cfg['STATIC_SRV_URL'] . $cfg['IMG_UPLOAD_DIR'] . '/' . $url;
    }
    defined('MULTILANG') ? $news['iso_lang'] = $ml->idToIso($news['lang_id']) : $news['iso_lang'] = $cfg['WEB_LANG'];
    $news['cat_name'] = $ctgs->getCatNameByID($news['category']);

    $tpl->addtoTplVar('META', $tpl->getTplFile('News', 'NewsSocialmeta', $news));
}

function getNewsCatBreadcrumb($news_data) {
    global $cfg, $ctgs;
    $content = '';

    $categories = $ctgs->getCategories('News');
    $news_cat_id = $news_data['category'];

    if ($categories[$news_cat_id]['father'] != 0) {
        $cat_list = '';
        $cat_check = $categories[$news_cat_id]['father'];
        do {
            $cat_list = $categories[$cat_check]['name'] . ',' . $cat_list;
            $cat_check = $categories[$cat_check]['father'];
        } while ($cat_check != 0);

        $cat_list = $cat_list . $categories[$news_cat_id]['name'];
        $cat_ary = explode(',', $cat_list);

        $breadcrumb = '';
        $cat_path = '';
        $list_counter = 1;
        foreach ($cat_ary as $cat) {
            $ITEM_LI = 'itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
            $ITEM_HREF = 'itemprop="item"';
            $ITEM_NAME = 'itemprop="name"';
            $ITEM_POS = '<meta itemprop="position" content="' . $list_counter . '" />';
            $cat_path .= $cat;
            !empty($breadcrumb) ? $breadcrumb .= $cfg['news_breadcrum_separator'] : null;
            $cat = preg_replace('/\_/', ' ', $cat);
            $breadcrumb .= '<li ' . $ITEM_LI . '>';
            $breadcrumb .= '<a ' . $ITEM_HREF . ' href="/' . $cfg['WEB_LANG'] . '/section/' . $cat_path . '">';
            $breadcrumb .= '<span ' . $ITEM_NAME . '>' . $cat . '</span></a>' . $ITEM_POS . '</li>';
            $cat_path .= $cfg['categories_separator'];
            $list_counter++;
        }
        $content .= $breadcrumb;
    }

    return $content;
}

function news_format_source($link) {
    $link['link'] = urldecode($link['link']);
    if ($link['type'] == 'source') {
        $url = parse_url($link['link']);
        $domain = $url['host'];
        $title = !empty($link['extra']) ? $link['extra'] : $domain;
        $result = '<a rel="nofollow" target="_blank" href="' . $link['link'] . '">' . $title . '</a>';
    } else {
        return false;
    }

    return $result;
}

function newsvote_news_addrate($news) {
    global $tpl;

    $ratings_data = ratings_get_ratings($news['nid'], 'news_rate');
    $rating_content = ratings_get_content('news_rate', $news['nid'], $news['author_id'], $news['lang_id'], $ratings_data);
    $tpl->addtoTplVar('ADD_NEWS_INFO_POST_AVATAR', $rating_content);
}
