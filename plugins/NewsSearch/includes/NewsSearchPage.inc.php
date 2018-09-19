<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NS_build_result_page(& $query) {
    global $db, $cfg, $tpl, $frontend;
    $content = "";
    
    if ($query && ($num_rows = $db->num_rows($query)) > 0) {
        $counter = 0;
        while ($result = $db->fetch($query)) {
            $counter == 0 ? $result['TPL_FIRST'] = 1 : false;
            $counter == ($num_rows - 1 ) ? $result['TPL_LAST'] = 1 : false;
            $counter++;
            if ($cfg['FRIENDLY_URL']) {
                $friendly_title = news_friendly_title($result['title']);
                $result['url'] = "/{$result['lang']}/news/{$result['nid']}/{$result['page']}/$friendly_title";
            } else {
                $result['url'] = "/{$cfg['CON_FILE']}?module=Newspage&page=news&nid={$result['nid']}&lang={$result['lang']}&npage={$result['page']}";
            }
            $content .= $tpl->getTPL_file("NewsSearch", "NewsSearch-results", $result);
        }
        $tpl->addto_tplvar("ADD_TO_BODY", $content);
    } else {
        $frontend->message_box(['title' => 'L_NS_SEARCH', 'msg' => 'L_NS_NORESULT']);
    }
}

