<?php

/* 
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;


function News_Comments($news) {
    global $cfg, $tpl, $sm;

    includePluginFiles("NewsComments");

    $nid = $news['nid'];
    $lang_id = $news['lang_id'];

    $user = $sm->getSessionUser();

    if (empty($nid) || empty($lang_id)) {
        return false;
    }

    if (!empty($_POST['btnSendNewComment']) && $cfg['NC_ALLOW_NEW_COMMENTS']) {
        if (!empty($user) || $cfg['NC_ALLOW_ANON_COMMENTS']) {
            $comment = S_POST_TEXT_UTF8("news_comment");
            $comment ? SC_AddComment("Newspage", $comment, $nid, $lang_id) : false;
        }
    }

    $content = SC_GetComments("Newspage", $nid, $lang_id, $cfg['NC_MAX_COMMENTS_PERPAGE']);

    if ($cfg['NC_ALLOW_NEW_COMMENTS']) {
        if ($user || $cfg['NC_ALLOW_ANON_COMMENTS']) {
            $content .= SC_NewComment("Newspage", $nid, $lang_id);
        }
    }
    $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $content);

    return true;
}

function News_Comment_Details(& $comment) {
    global $sm, $cfg, $LNG;

    $author_data = $sm->getUserByID($comment['author_id']);
    if(!$author_data) {
        $author_data['uid'] = 0;
        $author_data['username'] = $LNG['L_SM_DELETED']; 
        $author_data['avatar'] = $cfg['SMB_IMG_DFLT_AVATAR'];
    } 
    if ($cfg['FRIENDLY_URL']) {
            $comment['p_url'] = "/{$cfg['WEB_LANG']}/profile&viewprofile={$author_data['uid']}";
    } else {
            $comment['p_url'] = "/{$cfg['CON_FILE']}?module=SMBasic&page=profile&viewprofile={$author_data['uid']}&lang={$cfg['WEB_LANG']}";
    }
    $comment = array_merge($comment, $author_data);
}

