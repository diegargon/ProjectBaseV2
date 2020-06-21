<?php

/**
 *  News - News common file
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function get_news_query($where, $q_conf = null, $order = null) {
    global $cfg, $db, $ctgs;

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
    return $db->fetchAll($result);
}

function layout_news($template, $news) {
    global $cfg, $tpl, $timeUtil;

    $lnews = [];

    foreach ($news as $news_row) {
        if ($cfg['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_row['title']);
            $news_row['url'] = $cfg['REL_PATH'] . $cfg['WEB_LANG'] . "/news/{$news_row['nid']}/{$news_row['page']}/{$news_row['lang_id']}/$friendly_title";
        } else {
            $news_row['url'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=News&page=view_news&nid={$news_row['nid']}&lang=" . $cfg['WEB_LANG'] . "&npage={$news_row['page']}&news_lang_id={$news_row['lang_id']}";
        }

        $news_row['STATIC_SRV_URL'] = $cfg['STATIC_SRV_URL'];
        $news_row['date'] = $timeUtil->formatDbDate($news_row['created']);

        if (isset($news_row['text'])) {
            $news_main_image = news_get_main_image($news_row);
            $main_image = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $cfg['img_selector'] . DIRECTORY_SEPARATOR, $news_main_image);
            $thumb_image = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR, $news_main_image);

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
    $match_regex = '/\[(img|localimg).*?\](.*?)\[\/(img|localimg)\]/';
    $match = false;
    preg_match($match_regex, $news_body, $match);
    return !empty($match[0]) ? $match[0] : null;
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

function get_news_byId($nid, $news_lang, $page = null) {
    global $db, $ml;
    empty($page) ? $page = 1 : null;

    $where_ary = ['nid' => $nid, 'page' => $page];
    if (defined('MULTILANG')) {
        $query = $db->selectAll('news', $where_ary, 'LIMIT ' . count($ml->getSiteLangs()));
    } else {
        $query = $db->selectAll('news', $where_ary, 'LIMIT 1');
    }
    if ($db->numRows($query) < 1) {
        return 'L_NEWS_DELETE_NOEXISTS';
    }

    if ($db->numRows($query) == 1) {
        $news_row = $db->fetch($query);
        $db->free($query);
        return $news_row;
    }

    //Get the exact news and return other news  if other langs exists
    while ($news_row = $db->fetch($query)) {
        if ($news_row['lang_id'] == $news_lang) {
            $news_exact_row = $news_row;
        } else {
            $nlangs[] = $news_row['lang_id'];
        }
    }

    if (!isset($news_exact_row)) {
        return 'L_NEWS_NOLANG';
    }

    isset($nlangs) ? $news_exact_row['other_langs'] = $nlangs : null;

    return $news_exact_row;
}

function get_news_links(& $news_data) {
    global $db;

    $query = $db->selectAll('links', ['source_id' => $news_data['nid']]);
    if ($db->numRows($query) < 1) {
        return false;
    } else {
        $news_data['news_related'] = '';
        while ($news_link = $db->fetch($query)) {
            if ($news_link['type'] == 'related') {
                $link = urldecode($news_link['link']);
                $title = !empty($news_link['extra']) ? $news_link['extra'] : $link;
                $news_data['news_related'] .= '<li><a rel="nofollow" target="_blank" href="' . $link . '">' . $title . '</a></li>';
            } else if ($news_link['type'] == 'source') {
                $news_data['news_sources'] = news_format_source($news_link);
            }
        }
    }
}

function news_get_source($nid) {
    global $db;

    $query = $db->selectAll('links', ['source_id' => $nid, 'type' => 'source'], 'LIMIT 1');
    if ($db->numRows($query) <= 0) {
        return false;
    } else {
        $source_link = $db->fetch($query);
    }
    $db->free($query);

    return $source_link;
}

function news_get_related($nid) {
    global $db;

    $query = $db->selectAll('links', ['source_id' => $nid, 'plugin' => 'News', 'type' => 'related']);
    if ($db->numRows($query) <= 0) {
        return false;
    } else {
        while ($relate_row = $db->fetch($query)) {
            $related[] = $relate_row;
        }
    }

    return $related;
}
