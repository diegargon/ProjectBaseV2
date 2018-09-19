<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NS_build_result_page(& $query) {
    global $db, $cfg, $tpl;
    $content = "";

    if ($query && ($num_rows = $db->num_rows($query)) > 0) {
        $counter = 0;
        do_action("common_web_structure");
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
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $content);
    } else {
        $msg['MSG'] = "L_NS_NORESULT";
        NS_msgbox($msg);
    }
}

function NS_msgbox($msg) {
    do_action("common_web_structure");
    $msg['title'] = "L_NS_SEARCH";
    $msg['MSG'] = $msg['MSG'];
    do_action("message_box", $msg);
}
