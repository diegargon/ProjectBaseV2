<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function get_news_query($where, $q_conf = null, $order = null) {
    global $cfg, $db, $ml, $ctgs;

    empty($q_conf['limit']) ? $limit = " LIMIT " . $cfg['news_dflt_getnews_limit'] : $limit = " LIMIT " . $q_conf['limit'];
    empty($order) ? $order = "ORDER BY created DESC" : $order = "ORDER BY " . $order;
    empty($q_conf['page']) ? $where['page'] = 1 : $where['page'] = $q_conf['page'];
   
    if (!isset($where['moderation']) && $cfg['news_moderation'] == 1) {
        $where['moderation'] = 0;
    }

    if (!isset($where['disabled'])) {
        $where['disabled'] = 0;
    }
    if (!isset($q_conf['childs']) || $q_conf['childs'] == 1) {
        $childs_cats_ids = $ctgs->getCatChildsId("News", $where['category']);
        $where['category'] = ["value" => "({$where['category']}$childs_cats_ids)", "operator" => "IN"];
    }

    if (isset($q_conf['headlines'])) {
        $what = "nid, lang_id, title, created, page, featured, visits";
    } else if (isset($q_conf['lead'])) {
        $what = "nid, lang_id, title, lead, page, created, featured, visits";
    } else {
        $what = "nid, lang_id, title, lead, text, page, author_id, created, last_edited, featured, visits, translator_id, tags";
    }
    $extra = $order . $limit;
    $result = $db->select("news", $what, $where, $extra);
    return $db->fetch_all($result);
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
    global $db;
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
