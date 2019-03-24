<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function get_news_query($where, $q_conf = null, $order = null) {
    global $cfg, $db, $ml, $ctgs;

    empty($q_conf['limit']) ? $limit = ' LIMIT ' . $cfg['news_dflt_getnews_limit'] : $limit = ' LIMIT ' . $q_conf['limit'];
    empty($order) ? $order = 'ORDER BY created DESC' : $order = 'ORDER BY ' . $order;
    empty($q_conf['page']) ? $where['page'] = 1 : $where['page'] = $q_conf['page'];

    if (!isset($where['moderation']) && $cfg['news_moderation'] == 1) {
        $where['moderation'] = 0;
    }

    if (!isset($where['disabled'])) {
        $where['disabled'] = 0;
    }
    if (!isset($q_conf['childs']) || $q_conf['childs'] == 1) {
        $childs_cats_ids = $ctgs->getCatChildsId('News', $where['category']);
        $where['category'] = ['value' => "({$where['category']}$childs_cats_ids)", 'operator' => 'IN'];
    }

    if (isset($q_conf['headlines'])) {
        $what = 'nid, lang_id, title, category, created, page, featured, visits';
        isset($q_conf['main_image']) ? $what .= ', text' : null;
    } else if (isset($q_conf['lead'])) {
        $what = 'nid, lang_id, title, category, lead, page, created, featured, visits';
        isset($q_conf['main_image']) ? $what .= ', text' : null;
    } else {
        $what = 'nid, lang_id, title, category, lead, text, page, author_id, created, last_edited, featured, visits, translator_id, tags';
    }
    $extra = $order . $limit;
    $result = $db->select('news', $what, $where, $extra);
    return $db->fetch_all($result);
}

function layout_news($template, $news) {
    global $cfg, $tpl;

    $lnews = [];

    foreach ($news as $news_row) {
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_row['title']);
            $news_row['url'] = '/' . $cfg['WEB_LANG'] . "/news/{$news_row['nid']}/{$news_row['page']}/{$news_row['lang_id']}/$friendly_title";
        } else {
            $news_row['url'] = "/{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_row['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news_row['page']}&news_lang_id={$news_row['lang_id']}";
        }

        $news_row['date'] = format_date($news_row['created']);
        if (isset($news_row['text'])) {
            $main_image = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $cfg['img_selector'] . DIRECTORY_SEPARATOR, news_get_main_image($news_row));
            $thumb_image = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR, news_get_main_image($news_row));
            $news_row['main_image'] = preg_replace('~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si', '$2', $main_image);
            $news_row['thumb_image'] = preg_replace('~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si', '$2', $thumb_image);
        }
        $news_row['html'] = $tpl->getTplFile('News', $template, $news_row);

        $lnews[] = $news_row;
    }

    return $lnews;
}

function news_get_main_image($news) {
    $news_body = $news['text'];
    $match_regex = '/\[(img|localimg).*\](.*)\[\/(img|localimg)\]/';
    $match = false;
    preg_match($match_regex, $news_body, $match);

    return !empty($match[0]) ? $match[0] : false;
}

function news_extract_bycat($news, $cat) {
    $r_news = [];
    foreach ($news as $news_row) {
        if ($news_row['category'] == $cat) {
            $r_news[] = $news_row;
        }
    }
    return $r_news;
}

function get_news_byId($nid, $lang_id, $page = null) {
    global $db;
    empty($page) ? $page = 1 : false;

    $where_ary = ['nid' => $nid, 'lang_id' => $lang_id, 'page' => $page];
    $query = $db->select_all('news', $where_ary, 'LIMIT 1');

    //TODO ONE SELECT ALL LANG AND THEN CHOOSE IF EXISTS OR NOT EXIST
    if ($db->num_rows($query) < 1) { //IF NOT FOUND CHECK IN OTHER LANG        
        $query = $db->select_all('news', ['nid' => $nid, 'page' => $page], 'LIMIT 1');
        return $db->num_rows($query) > 0 ? 'L_NEWS_NOLANG' : 'L_NEWS_DELETE_NOEXISTS';
    }
    $news_row = $db->fetch($query);

    /*
      if ('ACL' && !empty($news_row['acl']) && !$acl_auth->acl_ask($news_row['acl'])) {
      return $frontend->messageBox(['msg' => "L_E_NOACCESS"]);
      }
     */
    $db->free($query);

    return $news_row;
}

function get_news_links(& $news_data) {
    global $db;

    $query = $db->select_all('links', ['source_id' => $news_data['nid']]);
    if ($db->num_rows($query) < 1) {
        return false;
    } else {
        $news_data['news_related'] = '';
        while ($news_link = $db->fetch($query)) {
            if ($news_link['type'] == 'related') {
                $link = urldecode($news_link['link']);
                $news_data['news_related'] .= '<li><a rel="nofollow" target="_blank" href="' . $link . '">' . $link . '</a></li>';
            } else if ($news_link['type'] == 'source') {
                $news_data['news_sources'] = news_format_source($news_link);
            }
        }
    }
}

function news_get_source($nid) {
    global $db;

    $query = $db->select_all('links', ['source_id' => $nid, 'type' => 'source'], 'LIMIT 1');
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        $source_link = $db->fetch($query);
    }
    $db->free($query);

    return $source_link;
}

function news_get_related($nid) {
    global $db;

    $query = $db->select_all('links', ['source_id' => $nid, 'plugin' => 'News', 'type' => 'related']);
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        while ($relate_row = $db->fetch($query)) {
            $related[] = $relate_row;
        }
    }

    return $related;
}
