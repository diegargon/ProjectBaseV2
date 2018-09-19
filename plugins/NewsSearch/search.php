<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("NewsSearch");
require_once 'plugins/NewsSearch/includes/NewsSearchPage.inc.php';

if (!empty($_GET['q'])) {
    $q = S_GET_TEXT_UTF8("q", $cfg['NS_MAX_S_TEXT'], $cfg['NS_MIN_S_TEXT']);
    
    if (empty($q)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $q = $db->escape_strip($q);
    $where_ary['lang'] = $cfg['WEB_LANG'];
    $cfg['NEWS_MODERATION'] ? $where_ary['moderation'] = 0 : null;

    $query = $db->search("news", "title lead text", $q, $where_ary, " LIMIT {$cfg['NS_RESULT_LIMIT']} ");

    NS_build_result_page($query);
}

if (!empty($_GET["searchTag"])) {
    $searchTag = S_GET_TEXT_UTF8("searchTag", $cfg['NS_TAGS_SZ_LIMIT'], $cfg['NS_MIN_S_TEXT']);

    if (empty($searchTag)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $searchTag = $db->escape_strip($searchTag);
    $where_ary['lang'] = $cfg['WEB_LANG'];
    $cfg['NEWS_MODERATION'] ? $where_ary['moderation'] = 0 : null;
    $query = $db->search("news", "tags", $searchTag, $where_ary, " LIMIT {$cfg['NS_RESULT_LIMIT']} ");
    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
}
if (empty($_GET['q']) && empty($_GET["searchTag"])) {
    $msg['MSG'] = "L_NS_SEARCH_ERROR";
    NS_msgbox($msg);
}