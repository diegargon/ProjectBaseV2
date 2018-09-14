<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_show_page() {
    global $cfg, $LNG, $tpl, $sm, $ml, $acl_auth, $filter, $tUtil, $frontend;

    $news_data = [];

    $editor = new Editor();

    if ((empty($_GET['nid'])) || ($nid = $filter->get_int("nid", 8, 1)) == false ||
            (empty($_GET['lang'])) || ($lang = $filter->get_AZChar("lang", 2, 2)) == false) {
        return $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    }

    if ($cfg['allow_multiple_pages'] && !empty($_GET['npage'])) {
        $page = $filter->get_int("npage", 11, 1);
    } else {
        $page = 1;
    }

    $user = $sm->getSessionUser();
    if ($filter->get_int("admin")) {
        if (!$user || (defined("ACL") && !$acl_auth->acl_ask("admin_all||news_admin"))) {
            return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
        }
        if (!defined('ACL') && $user['isAdmin'] != 1) {
            return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
        }
    }
    news_process_admin_actions();

    if (defined('MULTILANG') && $lang != null) {
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $lang) {
                $lang_id = $site_lang['lang_id'];
                break;
            }
        }
    } else {
        $lang_id = 1;
    }
    if (($news_data = get_news_byId($nid, $lang_id, $page)) == false) {
        return false;
    }
    //HEAD MOD
    //$cfg['news_stats'] ? news_stats($nid, $lang, $page, $news_data['visits']) : false;
    $cfg['PAGE_TITLE'] = $news_data['title'];
    $cfg['news_meta_opengraph'] ? news_add_social_meta($news_data) : false;
    $cfg['PAGE_DESC'] = $news_data['title'] . ":" . $news_data['lead'];
    //END HEAD MOD

    $news_data['news_admin_nav'] = news_nav_options($news_data);
    $cfg['allow_multiple_pages'] ? $news_data['pager'] = news_pager($news_data) : false;

    $news_data['title'] = str_replace('\r\n', '', $news_data['title']);
    $news_data['lead'] = str_replace('\r\n', PHP_EOL, $news_data['lead']);
    $news_data['news_url'] = "view_news.php?nid={$news_data['nid']}";
    $news_data['date'] = $tUtil->format_date($news_data['date']);
    $news_data['author'] = $news_data['author'];
    $news_data['author_uid'] = $news_data['author_id'];

    //!isset($news_parser) ? $news_parser = new parse_text : false;
    $news_data['text'] = $editor->parse($news_data['text']);

    if (!empty($news_data['translator_id'])) {
        $translator = $sm->getUserByID($news_data['translator_id']);
        $news_data['translator'] = "<a rel='nofollow' href='/{$cfg['WEB_LANG']}/profile&viewprofile={$translator['uid']}'>{$translator['username']}</a>";
    }
    $author = $sm->getUserByID($news_data['author_id']);
    $cfg['PAGE_AUTHOR'] = $author['username'];
    $news_data['author_avatar'] = $author['avatar'];

    if ($cfg['display_news_source'] && ($news_source = get_news_source_byID($news_data['nid'])) != false) {
        $news_data['news_sources'] = news_format_source($news_source);
    }
    if ($cfg['display_news_related'] && ($news_related = news_get_related($news_data['nid'])) != false) {
        $related_content = "<span>{$LNG['L_NEWS_RELATED']}:</span>";
        foreach ($news_related as $related) {
            $related_content .= "<li><a rel='nofollow' target='_blank' href='{$related['link']}'>{$related['link']}</a></li>";
        }
        $news_data['news_related'] = $related_content;
    }
    $cfg['news_breadcrum'] ? $news_data['news_breadcrum'] = getNewsCatBreadcrumb($news_data) : false;

    do_action("news_show_page", $news_data);

    if ($cfg['ITS_BOT'] && $cfg['INCLUDE_MICRODATA']) {
        $news_data['ITEM_OL'] = "itemscope itemtype=\"http://schema.org/BreadcrumbList\"";
    } else {
        $news_data['ITEM_OL'] = "";
    }
    if ($cfg['ITS_BOT'] && $cfg['INCLUDE_DATA_STRUCTURE']) {
        preg_match("/src=\"(.*?)\"/i", $news_data['text'], $matchs);
        $news_data['ITEM_MAINIMAGE'] = $matchs[1];
        $news_data['ITEM_CREATED'] = preg_replace("/ /", "T", $news_data['created']) . "Z";
        $news_data['ITEM_MODIFIED'] = preg_replace("/ /", "T", $news_data['last_edited']) . "Z";
        $cats = explode(" ", trim(strip_tags($news_data['news_breadcrum'])));
        if (!empty($cats)) {
            $news_data['ITEM_SECTIONS'] = "";
            foreach ($cats as $cat) {
                $news_data['ITEM_SECTIONS'] .= "\"articleSection\": \"" . trim($cat) . "\",\n";
            }
        }
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("News", "news_body_struct", $news_data));
    }
    if ($cfg['news_page_sidenews']) {
        //require_once("news_portal.php");
        $getnews_config['category'] = 0;
        $getnews_config['fontpage'] = 1;
        $getnews_config['cathead'] = 1;
        $getnews_config['headlines'] = 1;
        $news_data['SIDE_NEWS'] = get_news($getnews_config);
    }
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_body", $news_data));
}

function news_process_admin_actions() {
    global $cfg, $acl_auth, $sm, $ml, $filter;

    //if we enter with &admin=1 already passing the admin check in news_show_page, check if not enter with admin=1 , do again and remove if?
    $user = $sm->getSessionUser();
    if (!$filter->get_int("admin")) {
        if (!$user || (defined("ACL") && !$acl_auth->acl_ask("admin_all || news_admin"))) {
            return false;
        }
        if (!defined("ACL") && $user['isAdmin'] != 1) {
            return false;
        }
    }
    if (!empty($_GET['news_delete'])) {
        $delete_nid = $filter->get_int("nid", 11, 1);
        $delete_lang = $filter->get_AZChar("lang", 2, 2);
        if (!empty($delete_nid) && !empty($delete_lang)) {
            defined('MULTILANG') ? $delete_lang_id = $ml->iso_to_id($delete_lang) : $delete_lang_id = 1;
            news_delete($delete_nid, $delete_lang_id);
            $filter->get_AZChar("backlink") == "home" ? header("Location: /{$cfg['WEB_LANG']}") : header("Location: " . S_SERVER_URL("HTTP_REFERER") . "");
        }
    }
    if (!empty($_GET['news_approved']) && !empty($_GET['lang_id']) &&
            $_GET['news_approved'] > 0 && $_GET['lang_id'] > 0) {
        news_approved($filter->get_int("news_approved"), $filter->get_int("lang_id"));
    }
    if (isset($_GET['news_featured']) && !empty($_GET['lang_id'] && !empty($_GET['nid']))) {
        empty($_GET['news_featured']) ? $news_featured = 0 : $news_featured = 1;
        news_featured($filter->get_int("nid", 11, 1), $news_featured, $filter->get_int("lang_id"));
    }
    if (isset($_GET['news_frontpage']) && !empty($_GET['lang_id'])) {
        news_frontpage($filter->get_int("nid", 11, 1), $filter->get_int("lang_id"), $filter->get_int("news_frontpage", 1, 1));
    }

    return true;
}

function news_nav_options($news) {
    global $LNG, $cfg, $sm, $acl_auth;
    $content = "";
    $news_url = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news['nid']}&lang={$news['lang']}&npage={$news['page']}&lang_id={$news['lang_id']}";
    $user = $sm->getSessionUser();
    
    // EDIT && NEW PAGE: ADMIN, AUTHOR or Translator
    if (( $user && defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) || ( $user && !defined('ACL') && $user['isAdmin'] == 1)) {
        $admin = 1;
    } else {
        $admin = 0;
    }
    //Only admin but show disabled to all
    if ($admin && $news['featured'] == 1 && $news['page'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href='$news_url&news_featured=0&featured_value=0&admin=1'>{$LNG['L_NEWS_FEATURED']}</a></li>";
    } else if ($admin && $news['page'] == 1) {
        $content .= "<li><a rel='nofollow' href='$news_url&news_featured=1&featured_value=1&admin=1'>{$LNG['L_NEWS_FEATURED']}</a></li>";
    } else if ($news['featured'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LNG['L_NEWS_FEATURED']}</a></li>";
    }
    if ($admin && $news['page'] == 1 && $news['frontpage'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href='$news_url&news_frontpage=0'>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
    } else if ($admin && $news['page'] == 1) {
        $content .= "<li><a rel='nofollow' href='$news_url&news_frontpage=1'>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
    } else if ($news['frontpage'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
    }

    if ($admin || $news['author'] == $user['username'] || !empty($news['translator'] && ($news['translator'] == $user['username']))) {
        $content .= "<li><a rel='nofollow' href='$news_url&newsedit=1'>{$LNG['L_NEWS_EDIT']}</a></li>";
    }
    //not translator
    if ($cfg['allow_multiple_pages'] && ( $admin || $news['author'] == $user['username'])) {
        $content .= "<li><a rel='nofollow' href='$news_url&newpage=1'>{$LNG['L_NEWS_NEW_PAGE']}</a></li>";
    }

    // TRANSLATE ADMIN, ANON IF, REGISTERED IF
    if (defined('MULTILANG')) {
        if ($cfg['news_anon_translate'] || $admin || ($user && defined('ACL') && $cfg['NEWS_TRANSLATE_REGISTERED'] && $acl_auth->acl_ask("registered_all")) || ($user && !defined('ACL') && $cfg['NEWS_TRANSLATE_REGISTERED'])
        ) {
            $content .= "<li><a rel='nofollow' href='$news_url&news_new_lang=1'>{$LNG['L_NEWS_NEWLANG']}</a></li>";
        }
    }
    if ($admin) {
        if ($news['moderation'] && $news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='$news_url&news_approved={$news['nid']}&admin=1'>{$LNG['L_NEWS_APPROVED']}</a></li>";
        }
        //TODO  Add a menu for enable/disable news
        //    //$content .= "<li><a href=''>{$LNG['L_NEWS_DISABLE']}</a></li>";
        if ($news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='$news_url&news_delete=1&admin=1&backlink=home' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE']}</a></li>";
        }
    }

    return $content;
}

function news_pager($news_page) {
    global $db, $cfg;

    $query = $db->select_all("news", ["nid" => $news_page['nid'], "lang_id" => $news_page['lang_id']]);
    if (($num_pages = $db->num_rows($query)) <= 1) {
        return false;
    }
    $content = "<div id='pager'><ul>";

    $news_page['page'] == 1 ? $a_class = "class='active'" : $a_class = '';
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/1/$friendly_title'>1</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=1'>1</a></li>";
    }

    $pager = page_pager($cfg['NEWS_PAGER_MAX'], $num_pages, $news_page['page']);

    for ($i = $pager['start_page']; $i < $pager['limit_page']; $i++) {
        $news_page['page'] == $i ? $a_class = "class='active'" : $a_class = '';
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_page['title']);
            $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$i/$friendly_title'>$i</a></li>";
        } else {
            $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=$i'>$i</a></li>";
        }
    }
    $news_page['page'] == $num_pages ? $a_class = "class='active'" : $a_class = '';
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$num_pages/$friendly_title'>$num_pages</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=$num_pages'>$num_pages</a></li>";
    }
    $content .= "</ul></div>";

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

function news_delete($nid, $lang_id) {
    global $db;

    $db->delete("news", ["nid" => $nid, "lang_id" => $lang_id]);
    $query = $db->select_all("news", ["nid" => $nid], "LIMIT 1"); //check if other lang
    if ($db->num_rows($query) <= 0) {
        $db->delete("links", ["plugin" => "News", "source_id" => $nid]);
        //ATM by default this fuction delete all "links" if no exists the same news in other lang, mod like 
        do_action("news_delete_mod", $nid);
    }
    return true;
}

function news_approved($nid, $lang_id) {
    global $db;

    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $db->update("news", array("moderation" => 0), ["nid" => $nid, "lang_id" => $lang_id]);

    return true;
}

function news_featured($nid, $featured, $lang_id) {
    global $db;

    //$time = format_date(time(), true);
    $time = date('Y-m-d H:i:s', time());

    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $update_ary = ["featured" => "$featured"];
    $featured == 1 ? $update_ary['featured_date'] = $time : false;
    $db->update("news", $update_ary, ["nid" => $nid, "lang_id" => $lang_id]);

    return true;
}

function news_frontpage($nid, $lang_id, $frontpage_state = 0) {
    global $db;

    if (empty($nid) || empty($lang_id) || $nid <= 0 && $lang_id <= 0) {
        return false;
    }
    $db->update("news", ["frontpage" => $frontpage_state], ["nid" => $nid, "lang_id" => $lang_id]);

    return true;
}

function news_stats($nid, $lang, $page, $visits) {
    global $db, $cfg;
    $db->update("news", ["visits" => ++$visits], ["nid" => "$nid", "lang" => "$lang", "page" => "$page"], "LIMIT 1");
    $cfg['news_adv_stats'] ? news_adv_stats($nid, $lang) : false;
}

function news_adv_stats($nid, $lang) {
    global $db, $sm, $filter;

    $plugin = "News";

    $user = $sm->getSessionUser();
    empty($user) ? $user['uid'] = 0 : false; //Anon        
    $ip = $filter->srv_remote_addr();
    $hostname = gethostbyaddr($ip);
    $where_ary = [
        "type" => "user_visits_page",
        "plugin" => "$plugin",
        "lang" => "$lang",
        "rid" => "$nid",
        "uid" => $user['uid']
    ];
    $user['uid'] == 0 ? $where_ary['ip'] = $ip : false;

    $query = $db->select_all("adv_stats", $where_ary, "LIMIT 1");

    $user_agent = S_SERVER_USER_AGENT();
    $referer = S_SERVER_URL("HTTP_REFERER");

    if ($db->num_rows($query) > 0) {
        $user_adv_stats = $db->fetch($query);
        $counter = ++$user_adv_stats['counter'];
        $db->update("adv_stats", ["counter" => "$counter", "user_agent" => "$user_agent", "referer" => "$referer"], ["advstatid" => $user_adv_stats['advstatid']]);
    } else {
        $insert_ary = [
            "plugin" => "$plugin",
            "type" => "user_visits_page",
            "rid" => "$nid",
            "lang" => "$lang",
            "uid" => $user['uid'],
            "ip" => "$ip",
            "hostname" => $hostname,
            "user_agent" => "$user_agent",
            "referer" => "$referer",
            "counter" => 1
        ];
        $db->insert("adv_stats", $insert_ary);
    }

    if ((!empty($referer)) && ( (strpos($referer, "://" . $_SERVER['SERVER_NAME']) ) === false)) {
        $query = $db->select_all("adv_stats", ["type" => "referers_only", "referer" => "$referer"], "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            $allreferers = $db->fetch($query);
            $counter = ++$allreferers['counter'];
            $db->update("adv_stats", ["counter" => "$counter"], ["advstatid" => $allreferers['advstatid']]);
        } else {
            $insert_ary = [
                "plugin" => "$plugin",
                "type" => "referers_only",
                "referer" => $referer,
                "counter" => 1,
            ];
            $db->insert("adv_stats", $insert_ary);
        }
    }
}

function news_add_social_meta($news) { // TODO: Move to plugin NewsSocialExtra
    global $tpl, $cfg, $filter;
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
    $news['url'] = $protocol . $_SERVER['HTTP_HOST'] . $filter->srv_request_uri();
    $news['PAGE_TITLE'] = $news['title'];
    $match_regex = "/\[.*img(.*)\[\/.*img\]/";
    $match = "";
    preg_match($match_regex, $news['text'], $match);
    if (!empty($match[1])) {
        $url = preg_replace('/\[S\]/si', $cfg['img_selector'] . "/", $match[1]);
        $news['mainimage'] = $cfg['STATIC_SRV_URL'] . $cfg['IMG_UPLOAD_DIR'] . "/" . $url;
    }
    $content = $tpl->getTPL_file("News", "NewsSocialmeta", $news);
    $tpl->addto_tplvar("META", $content);
}

function getNewsCatBreadcrumb($news_data) {
    global $db, $cfg;
    $content = "";

    $query = $db->select_all("categories", ["plugin" => "News", "lang_id" => $news_data['lang_id']]);
    while ($cat_row = $db->fetch($query)) {
        $categories[$cat_row['cid']] = $cat_row;
    }
    $news_cat_id = $news_data['category'];

    if ($categories[$news_cat_id]['father'] != 0) {
        $cat_list = "";
        $cat_check = $categories[$news_cat_id]['father'];
        do {
            $cat_list = $categories[$cat_check]['name'] . "," . $cat_list;
            $cat_check = $categories[$cat_check]['father'];
        } while ($cat_check != 0);

        $cat_list = $cat_list . $categories[$news_cat_id]['name'];
        $cat_ary = explode(",", $cat_list);

        $breadcrumb = "";
        $cat_path = "";
        $list_counter = 1;
        foreach ($cat_ary as $cat) {
            if ($cfg['ITS_BOT'] && $cfg['INCLUDE_MICRODATA']) {
                $ITEM_LI = "itemprop=\"itemListElement\" itemscope itemtype=\"http://schema.org/ListItem\"";
                $ITEM_HREF = "itemscope itemtype=\"http://schema.org/Thing\" itemprop=\"item\"";
                $ITEM_NAME = "itemprop=\"name\"";
                $ITEM_POS = "<meta itemprop=\"position\" content=\"$list_counter\" />";
            } else {
                $ITEM_LI = "";
                $ITEM_HREF = "";
                $ITEM_NAME = "";
                $ITEM_POS = "";
            }
            $cat_path .= $cat;
            !empty($breadcrumb) ? $breadcrumb .= $cfg['news_breadcrum_separator'] : null;
            $cat = preg_replace('/\_/', ' ', $cat);
            $breadcrumb .= "<li $ITEM_LI>";
            $breadcrumb .= "<a $ITEM_HREF href='/{$cfg['WEB_LANG']}/section/$cat_path'>";
            $breadcrumb .= "<span $ITEM_NAME>$cat</span></a>$ITEM_POS</li> ";
            $cat_path .= ".";
            $list_counter++;
        }
        $content .= $breadcrumb;
    }

    return $content;
}
