<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function News_Comments($news) {
    global $cfg, $tpl, $sm, $filter, $plugins;

    if (empty($news['nid']) || empty($news['lang_id'])) {
        return false;
    }

    $plugins->express_start_provider("COMMENTS");

    $comm_conf['plugin'] = "NewsComments";
    $comm_conf['resource_id'] = $news['nid'];
    $comm_conf['lang_id'] = $news['lang_id'];

    $user = $sm->getSessionUser();

    if (empty($user)) {
        $comm_conf['author_id'] = 0;
    } else {
        $comm_conf['author_id'] = $user['uid'];
    }

    if (!empty($_POST['btnSendNewComment']) && $cfg['nc_allow_new_comments']) {
        if (!empty($user) || $cfg['nc_allow_anon_comments']) {
            $comm_conf['comment'] = $filter->post_UTF8_txt("news_comment");
            $comm_conf['comment'] ? scAddComment($comm_conf) : false;
        }
    }

    $comm_conf['limit'] = $cfg['nc_max_comments_perpage'];

    if (isset($comm_conf['comment'])) {
        unset($comm_conf['comment']);
    }
    $content = scGetComments($comm_conf);

    if ($cfg['nc_allow_new_comments']) {
        if ($user || $cfg['nc_allow_anon_comments']) {
            $content .= scNewComment();
        }
    }

    $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $content);

    return true;
}
