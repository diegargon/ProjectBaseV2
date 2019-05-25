<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function NS_build_result_page(& $query) {
    global $db, $cfg, $tpl, $frontend;
    $content = "";

    if ($query && ($num_rows = $db->numRows($query)) > 0) {
        $counter = 0;
        while ($result = $db->fetch($query)) {
            $counter == 0 ? $result['TPL_FIRST'] = 1 : false;
            $counter == ($num_rows - 1 ) ? $result['TPL_LAST'] = 1 : false;
            $counter++;
            if ($cfg['FRIENDLY_URL']) {
                $friendly_title = news_friendly_title($result['title']);
                $result['url'] = "/{$cfg['WEB_LANG']}/news/{$result['nid']}/{$result['page']}/{$result['lang_id']}/$friendly_title";
            } else {
                $result['url'] = "/{$cfg['CON_FILE']}?module=News&page=news&nid={$result['nid']}&lang={$cfg['WEB_LANG']}&npage={$result['page']}&news_lang_id={$result['news_lang']}";
            }
            $content .= $tpl->getTplFile("NewsSearch", "NewsSearch-results", $result);
        }
        $tpl->addtoTplVar("ADD_TO_BODY", $content);
    } else {
        $frontend->messageBox(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_NORESULT']);
    }
}
