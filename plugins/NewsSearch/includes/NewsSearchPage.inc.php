<?php

/**
 *  NewsSearch search include file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function NS_build_result_page(& $query, $q = false) {
    global $db, $cfg, $tpl, $frontend, $LNG;
    $content = '';

    if ($query && ($num_rows = $db->numRows($query)) > 0) {
        $counter = 0;
        while ($result = $db->fetch($query)) {
            $counter == 0 ? $result['TPL_FIRST'] = 1 : null;
            $counter == ($num_rows - 1 ) ? $result['TPL_LAST'] = 1 : null;
            $counter++;
            if ($cfg['FRIENDLY_URL']) {
                $friendly_title = news_friendly_title($result['title']);
                $result['url'] = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/news/{$result['nid']}/{$result['page']}/{$result['lang_id']}/$friendly_title";
            } else {
                $result['url'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=News&page=news&nid={$result['nid']}&lang={$cfg['WEB_LANG']}&npage={$result['page']}&news_lang_id={$result['news_lang']}";
            }
            $content .= $tpl->getTplFile('NewsSearch', 'NewsSearch-results', $result);
        }
        $tpl->addtoTplVar('ADD_TO_BODY', $content);
    } else {
        NS_build_search_page($q, $LNG['L_NS_NORESULT']);
    }

    return true;
}

function NS_build_search_page($q = false, $msg = false) {
    global $tpl;
    $q_data = [];
    (!empty($q)) ? $q_data['q'] = $q : null;
    (!empty($msg)) ? $q_data['msg'] = $msg : null;
    $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('NewsSearch', 'NewsSearch-page', $q_data));
}
