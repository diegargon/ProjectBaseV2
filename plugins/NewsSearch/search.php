<?php

/**
 *  NewsSearch search file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

$plugins->expressStart('NewsSearch');

require_once __DIR__ . '/includes/NewsSearchPage.inc.php';

if (!empty($_GET['q'])) {
    $q = $filter->getUtf8Txt('q', $cfg['ns_max_s_text'], $cfg['ns_min_s_text']);

    if (empty($q)) {
        NS_build_search_page($q, $LNG['L_NS_SEARCH_ERROR']);
        return false;
    }
    $q = $db->escapeStrip($q);
    (defined('MULTILANG')) ? $where_ary['lang_id'] = $ml->getWebLangID() : $where_ary['lang_id'] = 1;
    $cfg['news_moderation'] ? $where_ary['moderation'] = 0 : null;
    $query = $db->search('news', 'title lead text', $q, $where_ary, " LIMIT {$cfg['ns_result_limit']} ");
    NS_build_result_page($query, $q);
} else if (!empty($_GET['searchTag'])) {
    $searchTag = $filter->getUtf8Txt('searchTag', $cfg['ns_tag_size_limit'], $cfg['ns_min_s_text']);

    if (empty($searchTag)) {
        $frontend->messageBox(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_SEARCH_ERROR']);
        return false;
    }
    $searchTag = $db->escapeStrip($searchTag);

    (defined('MULTILANG')) ? $where_ary['lang_id'] = $ml->getWebLangID() : $where_ary['lang_id'] = 1;

    $cfg['news_moderation'] ? $where_ary['moderation'] = 0 : null;
    $query = $db->search('news', 'tags', $searchTag, $where_ary, " LIMIT {$cfg['ns_result_limit']} ");
    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
} else {
    NS_build_search_page();
}
