<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function get_news($news_select, $xtr_data = null) {
    global $cfg, $db, $tpl, $ml, $LNG, $ctgs;
    $content = "";

    !isset($news_select['limit']) ? $news_select['limit'] = 0 : null;
    !isset($news_select['headlines']) ? $news_select['headlines'] = 0 : null;
    !isset($news_select['cathead']) ? $news_select['cathead'] = 0 : null;
    !isset($news_select['excl_portal_featured']) ? $news_select['excl_portal_featured'] = 0 : null;
    !isset($news_select['excl_firstcat_featured']) ? $news_select['excl_firstcat_featured'] = 0 : null;

    $where_ary['page'] = 1;

    if (defined('MULTILANG')) {
        $lang_id = $where_ary['lang_id'] = $ml->getSessionLangId();
    } else {
        $lang_id = 1;
    }

    $excluded_news = [];

    if ($news_select['excl_portal_featured']) {
        $featured_ary = [
            "featured" => 1,
            "page" => 1,
            "lang_id" => "$lang_id",
        ];
        $featured_query = $db->select_all("news", $featured_ary, "ORDER BY featured_date DESC LIMIT {$cfg['NEWS_PORTAL_FEATURED_LIMIT']}");
        while ($featured_news = $db->fetch($featured_query)) {
            $excluded_news[] = $featured_news['nid'];
        }
    }
    $childs_id = "";
    if (!empty($news_select['get_childs'])) {
        $childs_id = $ctgs->getCatChildsID("News", $news_select['category']);
    }
    if ($news_select['excl_firstcat_featured'] && !empty($news_select['category'])) {
        $featured_ary = [
            "featured" => 1,
            "page" => 1,
            "lang_id" => "$lang_id",
        ];
        $featured_ary['category'] = ["value" => "({$news_select['category']}$childs_id)", "operator" => "IN"];
        $featured_query = $db->select_all("news", $featured_ary, "ORDER BY featured_date DESC LIMIT 1");
        $featured_news = $db->fetch($featured_query);

        !empty($featured_news) ? $where_ary['nid'] = ["value" => $featured_news['nid'], "operator" => "<>"] : null;
    }

    $cfg['news_moderation'] == 1 ? $where_ary['moderation'] = 0 : null;

    isset($news_select['featured']) ? $where_ary['featured'] = $news_select['featured'] : null;
    isset($news_select['frontpage']) ? $where_ary['frontpage'] = $news_select['frontpage'] : null;
    isset($news_select['featured']) && !empty($news_select['featured']) ? $q_extra = " ORDER BY featured_date DESC" : $q_extra = " ORDER BY date DESC";
    $news_select['limit'] > 0 ? $q_extra .= " LIMIT {$news_select['limit']}" : null;

    if (!empty($news_select['category']) && !empty($news_select['get_childs'])) {
        $where_ary['category'] = ["value" => "({$news_select['category']}$childs_id)", "operator" => "IN"];
    } else if (!empty($news_select['category'])) {
        $where_ary['category'] = $news_select['category'];
    }

    $query = $db->select_all("news", $where_ary, $q_extra);
    if ($db->num_rows($query) <= 0) {
        return false;
    }

    if ($news_select['cathead']) {
        $catname = null;
        if (defined('MULTILANG') && !empty($news_select['category'])) {
            $catname = "<h2>";
            !empty($news_select['featured']) ? $catname .= $LNG['L_NEWS_FEATURED'] . ": " : null;
            $catname .= get_category_name($news_select['category'], $lang_id) . "</h2>";
        } else if (!empty($news_select['category'])) {
            $catname = "<h2>";
            $news_select['featured'] ? $catname .= $LNG['L_NEWS_FEATURED'] . ": " : null;
            $catname .= get_category_name($news_select['category']) . "</h2>";
        }

        if (empty($news_select['category']) && ( isset($news_select['frontpage']) && $news_select['frontpage'] == 0) && empty($news_select['featured'])) {
            $catname = "<h2>" . $LNG['L_NEWS_BACKPAGE'] . "</h2>";
        } else if (empty($news_select['category']) && !isset($news_select['frontpage']) && empty($news_select['featured'])) {
            $catname = "<h2>" . $LNG['L_NEWS_FRONTPAGE'] . "</h2>";
        } else if (empty($news_select['category']) && !empty($news_select['featured'])) {
            $catname = "<h2 class='featured_category'>{$LNG['L_NEWS_FEATURED']}</h2>";
        }
        $content .= $catname;
    }

    $save_img_selector = $cfg['img_selector'];
    empty($news_select['featured']) ? $cfg['img_selector'] = "thumbs" : null; //no thumb for featured image
    while ($news_row = $db->fetch($query)) {
        if (($news_data = fetch_news_data($news_row)) != false) {
            $news_select['headlines'] ? $news_data['headlines'] = 1 : null;
            if (!empty($news_select['featured'])) {
                do_action("news_featured_mod", $news_data);
                $news_data['numcols_class_extra'] = "featured_col" . $cfg['NEWS_PORTAL_FEATURED_LIMIT'];
                $content .= $tpl->getTPL_file("News", "news_featured", $news_data);
            } else {
                if (!in_array($news_data['nid'], $excluded_news)) {
                    do_action("news_get_news_mod", $news_data);
                    $content .= $tpl->getTPL_file("News", "news_preview", $news_data);
                }
            }
        }
    }
    $db->free($query);
    $cfg['img_selector'] = $save_img_selector;

    return $content;
}

function fetch_news_data($row) {
    global $cfg, $acl_auth, $tUtil, $editor;

    /*
      if ($cfg['NEWS_ACL_PREVIEW_CHECK'] && defined('ACL') &&
      !empty($acl_auth) && !empty($row['acl']) && !$acl_auth->acl_ask($row['acl'])) {
      return false;
      }
     */
    $news['nid'] = $row['nid'];
    $news['title'] = $row['title'];
    $news['lead'] = $row['lead'];
    $news['date'] = $tUtil->format_date($row['date']);
    $news['alt_title'] = htmlspecialchars($row['title']);

    if ($cfg['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($row['title']);
        $news['url'] = "/" . $cfg['WEB_LANG'] . "/news/{$row['nid']}/{$row['page']}/$friendly_title";
    } else {
        $news['url'] = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$row['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$row['page']}";
    }
    $mainimage = news_determine_main_image($row);
    if (!empty($mainimage)) {
        $news['mainimage'] = $editor->parse($mainimage);
    }

    return $news;
}

function news_determine_main_image($news) {
    $news_body = $news['text'];
    $match_regex = "/\[(img|localimg).*\](.*)\[\/(img|localimg)\]/";
    $match = false;
    preg_match($match_regex, $news_body, $match);

    return !empty($match[0]) ? $match[0] : false;
}

function news_get_related($nid) {
    global $db;

    $query = $db->select_all("links", array("source_id" => $nid, "plugin" => "News", "type" => "related"));
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        while ($relate_row = $db->fetch($query)) {
            $related[] = $relate_row;
        }
    }

    return $related;
}

function get_news_byId($nid, $lang_id, $page = null) {
    global $cfg, $acl_auth, $db, $filter, $frontend;
    empty($page) ? $page = 1 : false;

    $where_ary = ["nid" => "$nid", "lang_id" => "$lang_id", "page" => "$page"];
    $query = $db->select_all("news", $where_ary, "LIMIT 1");

    if ($db->num_rows($query) <= 0) {
        $query = $db->select_all("news", ["nid" => $nid, "page" => $page], "LIMIT 1");
        return $db->num_rows($query) > 0 ? "L_NEWS_NOLANG" : "L_NEWS_DELETE_NOEXISTS";
    }
    $news_row = $db->fetch($query);

    /*
      if ('ACL' && !empty($news_row['acl']) && !$acl_auth->acl_ask($news_row['acl'])) {
      return $frontend->message_box(['msg' => "L_E_NOACCESS"]);
      }
     */
    $db->free($query);

    return $news_row;
}

function get_news_source_byID($nid) {
    global $db;

    $query = $db->select_all("links", ["source_id" => "$nid", "type" => "source"], "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        $source_link = $db->fetch($query);
    }
    $db->free($query);

    return $source_link;
}

function news_friendly_title($title) {
    //FIX: better way for clean all those character?
    $friendly_filter = ['"', '\'', '?', '$', ',', '.', '‘', '’', ':', ';', '[', ']', '{', '}', '*', '!', '¡', '¿', '+', '<', '>', '#', '@', '|', '~', '%', '&', '(', ')', '=', '`', '´', '/', 'º', 'ª', '\\'];
    $friendly = str_replace(' ', "-", $title);
    $friendly = str_replace($friendly_filter, "", $friendly);

    return $friendly;
}
