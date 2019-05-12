<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

$plugins->express_start("NewsSearch");

require_once __DIR__ . '/includes/NewsSearchPage.inc.php';

if (!empty($_GET['q'])) {
    $q = $filter->get_UTF8_txt("q", $cfg['ns_max_s_text'], $cfg['ns_min_s_text']);

    if (empty($q)) {
        $frontend->messageBox(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_SEARCH_ERROR']);
    }
    $q = $db->escape_strip($q);

    (defined('MULTILANG')) ? $where_ary['lang_id'] = $ml->getWebLangID() : $where_ary['lang_id'] = 1;

    $cfg['news_moderation'] ? $where_ary['moderation'] = 0 : null;

    $query = $db->search("news", "title lead text", $q, $where_ary, " LIMIT {$cfg['ns_result_limit']} ");

    NS_build_result_page($query);
}

if (!empty($_GET["searchTag"])) {
    $searchTag = $filter->get_UTF8_txt("searchTag", $cfg['ns_tag_size_limit'], $cfg['ns_min_s_text']);

    if (empty($searchTag)) {
        $frontend->messageBox(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_SEARCH_ERROR']);
    }
    $searchTag = $db->escape_strip($searchTag);

    (defined('MULTILANG')) ? $where_ary['lang_id'] = $ml->getWebLangID() : $where_ary['lang_id'] = 1;

    $cfg['news_moderation'] ? $where_ary['moderation'] = 0 : null;
    $query = $db->search("news", "tags", $searchTag, $where_ary, " LIMIT {$cfg['ns_result_limit']} ");
    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
}
if (empty($_GET['q']) && empty($_GET["searchTag"])) {
    $frontend->messageBox(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_SEARCH_ERROR']);
}