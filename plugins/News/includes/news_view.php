<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_show_page() {
    global $cfg, $LNG, $tpl, $sm, $ml, $acl_auth, $filter, $frontend;

    $news_data = [];
    $editor = new Editor();

    if ((empty($_GET['nid'])) || ($nid = $filter->get_int("nid", 8, 1)) == false) {
        return $frontend->message_box(['msg' => "L_NEWS_NOT_EXIST"]);
    }

    if (!empty($_GET['news_lang_id'])) {
        $news_lang_id = $filter->get_int("news_lang_id", 2, 1);
    } else {
        (defined('MULTILANG')) ? $news_lang_id = $ml->getSessionLangID() : $news_lang_id = 1;
    }

    ($cfg['allow_multiple_pages'] && !empty($_GET['npage'])) ? $page = $filter->get_int("npage", 11, 1) : $page = 1;

    if (!is_array($news_data = get_news_byId($nid, $news_lang_id, $page))) { //Not array, its a error
        $frontend->message_box(['msg' => $news_data]);
        return false;
    }

    $news_perms = get_news_perms("view_news", $news_data);
    if (!$news_perms['news_view']) {
        return $frontend->message_box(["msg" => "L_E_NOVIEWACCESS"]);
    }

    news_process_admin_actions($news_data, $news_perms);

    if ($cfg['news_moderation'] && $news_data['moderation'] && !$news_perms['news_moderation']) {
        return $frontend->message_box(["msg" => "L_NEWS_ERROR_WAITINGMOD"]);
    }

    $news_data['news_admin_nav'] = news_nav_options($news_data, $news_perms);
    $cfg['allow_multiple_pages'] ? $news_data['pager'] = news_pager($news_data) : false;

    $news_data['title'] = str_replace('\r\n', '', $news_data['title']);
    $news_data['lead'] = str_replace('\r\n', PHP_EOL, $news_data['lead']);
    $news_data['news_url'] = "view_news.php?nid={$news_data['nid']}";
    $news_data['date'] = format_date($news_data['date']);
    $news_data['author'] = $news_data['author'];
    $news_data['author_uid'] = $news_data['author_id'];
    $news_data['text'] = $editor->parse($news_data['text']);

    if (!empty($news_data['translator_id'])) {
        $translator = $sm->getUserByID($news_data['translator_id']);
        $news_data['translator'] = "<a rel='nofollow' href='/{$cfg['WEB_LANG']}/profile&viewprofile={$translator['uid']}'>{$translator['username']}</a>";
    }
    $author = $sm->getUserByID($news_data['author_id']);

    //HEAD MOD
    //$cfg['news_stats'] ? news_stats($nid, $lang, $page, $news_data['visits']) : false;
    $cfg['PAGE_TITLE'] = $news_data['title'];
    $cfg['news_meta_opengraph'] ? news_add_social_meta($news_data) : false;
    $cfg['PAGE_DESC'] = $news_data['title'] . ":" . $news_data['lead'];
    $cfg['PAGE_AUTHOR'] = $author['username'];
    //END HEAD MOD
    !empty($author['avatar']) ? $news_data['author_avatar'] = $author['avatar'] : null;

    if ($cfg['display_news_source'] && ($news_source = get_news_source_byID($news_data['nid'])) != false) {
        $news_data['news_sources'] = news_format_source($news_source);
    }
    if ($cfg['display_news_related'] && ($news_related = news_get_related($news_data['nid'])) != false) {
        $related_content = "";
        foreach ($news_related as $related) {
            $related['link'] = urldecode($related['link']);
            $related_content .= "<li><a rel='nofollow' target='_blank' href='{$related['link']}'>{$related['link']}</a></li>";
        }
        $news_data['news_related'] = $related_content;
    }
    $cfg['news_breadcrum'] ? $news_data['news_breadcrum'] = getNewsCatBreadcrumb($news_data) : false;

    do_action("news_show_page", $news_data);

    ($cfg['ITS_BOT'] && $cfg['INCLUDE_MICRODATA']) ? $news_data['ITEM_OL'] = 1 : null;

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

    /* OLD REPLACE
      if ($cfg['news_page_sidenews']) {
      //require_once("news_portal.php");
      $getnews_config['category'] = 0;
      $getnews_config['fontpage'] = 1;
      $getnews_config['cathead'] = 1;
      $getnews_config['headlines'] = 1;
      $news_data['SIDE_NEWS'] = get_news($getnews_config);
      }

     */
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("News", "news_body", $news_data));
}

function news_process_admin_actions(&$news_data, $perms) {
    global $filter;

    $news_lang_id = $filter->get_int("news_lang_id", 2, 1);
    $news_nid = $filter->get_int("nid", 11, 1);

    if (empty($news_lang_id) || empty($news_nid)) {
        return false;
    }

    /* DELETE */
    if (!empty($_GET['news_delete']) && $perms['news_delete']) {
        news_delete($news_nid, $news_lang_id);
        header("Location: /");
    }
    /* APPROVE */
    if (!empty($_GET['news_approved']) && $perms['news_moderation']) {
        news_approved($news_nid, $news_lang_id);
        $news_data['moderation'] = 0;
    }
    /* FEATURE */
    if (isset($_GET['news_featured']) && $perms['news_feature']) {
        empty($_GET['news_featured']) ? $news_featured = 0 : $news_featured = 1;
        news_featured($news_nid, $news_lang_id, $news_featured);
        $news_data['featured'] = $news_featured;
    }
    /* FRONTPAGE */
    if (isset($_GET['news_frontpage']) && $perms['news_frontpage']) {
        empty($_GET['news_frontpage']) ? $news_frontpage = 0 : $news_frontpage = 1;
        news_frontpage($news_nid, $news_lang_id, $news_frontpage);
        $news_data['frontpage'] = $news_frontpage;
    }

    return true;
}

function news_nav_options($news, $perms) {
    global $LNG, $cfg;
    $content = "";
    $news_url_args = "&nid={$news['nid']}&news_lang_id={$news['lang_id']}&npage={$news['page']}";

    $view_news_url = "/{$cfg['CON_FILE']}?module=News&page=view_news" . $news_url_args;
    $edit_news_url = "/{$cfg['CON_FILE']}?module=News&page=edit_news" . $news_url_args;

    //Only admin can change but show link disabled to all in frontpage, and feature

    /* FEATURE */
    if ($perms['news_feature'] && $news['page'] == 1) {
        if ($news['featured'] == 1) {
            $content .= "<li><a class='link_active' rel='nofollow' href='$view_news_url&news_featured=0&featured_value=0'>{$LNG['L_NEWS_FEATURED']}</a></li>";
        } else {
            $content .= "<li><a rel='nofollow' href='$view_news_url&news_featured=1&featured_value=1'>{$LNG['L_NEWS_FEATURED']}</a></li>";
        }
    } else if ($news['featured'] == 1 && $news['page'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LNG['L_NEWS_FEATURED']}</a></li>";
    }

    /* FRONTPAGE */
    if ($perms['news_frontpage'] && $news['page'] == 1) {
        if ($news['frontpage'] == 1) {
            $content .= "<li><a class='link_active' rel='nofollow' href='$view_news_url&news_frontpage=0'>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
        } else {
            $content .= "<li><a rel='nofollow' href='$view_news_url&news_frontpage=1'>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
        }
    } else if ($news['frontpage'] && $news['page'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LNG['L_NEWS_FRONTPAGE']}</a></li>";
    }

    /* EDIT */
    if ($perms['news_edit']) {
        $content .= "<li><a rel='nofollow' href='$edit_news_url&newsedit=1'>{$LNG['L_NEWS_EDIT']}</a></li>";
    }

    /* CREATE NEW PAGE */
    if ($perms['news_create_new_page']) {
        $content .= "<li><a rel='nofollow' href='$edit_news_url&newpage=1'>{$LNG['L_NEWS_NEW_PAGE']}</a></li>";
    }

    /* TRANSLATE */

    // TRANSLATE ADMIN, ANON IF, REGISTERED IF
    //if ($cfg['news_anon_translate'] || $admin || ($user && defined('ACL') && $cfg['NEWS_TRANSLATE_REGISTERED'] 

    if ($perms['news_translate']) {
        $content .= "<li><a rel='nofollow' href='$edit_news_url&news_new_lang=1'>{$LNG['L_NEWS_NEWLANG']}</a></li>";
    }

    /* DELLETE */
    if ($news['page'] == 1 && $perms['news_delete']) {
        $content .= "<li><a rel='nofollow' href='$view_news_url&news_delete=1' onclick=\"return confirm('{$LNG['L_NEWS_CONFIRM_DEL']}')\">{$LNG['L_NEWS_DELETE']}</a></li>";
    }

    /* APPROVE */
    if ($news['page'] == 1 && $news['moderation'] && $cfg['news_moderation'] && $perms['news_moderation']) {
        $content .= "<li><a rel='nofollow' href='$view_news_url&news_approved=1'>{$LNG['L_NEWS_APPROVED']}</a></li>";
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
        $content .= "<li><a $a_class href='/{$cfg['WEB_LANG']}}/news/{$news_page['nid']}/1/{$news_page['lang_id']}/$friendly_title'>1</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&news_lang_id={$news_page['lang_id']}&npage=1&news_lang_id={$news_page['lang_id']}'>1</a></li>";
    }

    $pager = page_pager($cfg['news_pager_max'], $num_pages, $news_page['page']);

    for ($i = $pager['start_page']; $i < $pager['limit_page']; $i++) {
        $news_page['page'] == $i ? $a_class = "class='active'" : $a_class = '';
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_page['title']);
            $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$i/{$news_page['lang_id']}/$friendly_title'>$i</a></li>";
        } else {
            $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&npage=$i&news_lang_id={$news_page['lang_id']}'>$i</a></li>";
        }
    }
    $news_page['page'] == $num_pages ? $a_class = "class='active'" : $a_class = '';
    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$num_pages/{$news_page['lang_id']}/$friendly_title'>$num_pages</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_page['nid']}&lang={$cfg['WEB_LANG']}&npage=$num_pages&news_lang_id={$news_page['lang_id']}'>$num_pages</a></li>";
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
    $db->update("news", ["moderation" => 0], ["nid" => $nid, "lang_id" => $lang_id]);

    return true;
}

function news_featured($nid, $lang_id, $featured) {
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
    $match_regex = "/\[.*img.*\](.*)\[\/.*img\]/";
    $match = "";
    preg_match($match_regex, $news['text'], $match);
    if (!empty($match[1])) {
        $url = preg_replace('/\[S\]/si', $cfg['img_selector'] . "/", $match[1]);
        $cfg['IMG_UPLOAD_DIR'] = "news_img"; //TODO
        $news['mainimage'] = $cfg['STATIC_SRV_URL'] . $cfg['IMG_UPLOAD_DIR'] . "/" . $url;
    }
    $content = $tpl->getTPL_file("News", "NewsSocialmeta", $news);
    $tpl->addto_tplvar("META", $content);
}

function getNewsCatBreadcrumb($news_data) {
    global $cfg, $ctgs;
    $content = "";

    $categories = $ctgs->getCategories("News");
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

function news_format_source($link) {
    $link['link'] = urldecode($link['link']);
    if ($link['type'] == 'source') {
        $url = parse_url($link['link']);
        $domain = $url['host'];
        $result = "<a rel='nofollow' target='_blank' href='{$link['link']}'>$domain</a>";
    } else {
        return false;
    }

    return $result;
}
