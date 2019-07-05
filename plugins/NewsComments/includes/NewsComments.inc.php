<?php

/**
 *  Newscomments - main include file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Newscomments
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function News_Comments($news) {
    global $cfg, $tpl, $sm, $filter, $plugins, $LNG;
    $content = '';

    if (empty($news['nid']) || empty($news['lang_id'])) {
        return false;
    }

    $user = $sm->getSessionUser();

    if (!$cfg['ITS_BOT'] && $cfg['nc_vote_comments'] &&
            $plugins->checkEnabledProvider('RATINGS') &&
            $plugins->expressStartProvider('RATINGS')
    ) {
        $tpl->addScriptFile('StdRatings', 'rate', 'BOTTOM');
        /* $tpl->getCssFile('NewsComments'); */
        register_action('std_format_comments', 'NewsComments_AddrateDisplay');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
                ( ($filter->postStrictChars('rate_section')) == 'news_comments_rate')
        ) {
            if (rating_rate_getPost('news_comments_rate')) {
                die('[{"status": "6", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
            } else {
                die('[{"status": "4", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
            }
        }
    }

    if (!$plugins->checkEnabledProvider('COMMENTS') || !$plugins->expressStartProvider('COMMENTS')) {
        return false;
    }

    $tpl->addScriptFile('standard', 'jquery', 'BOTTOM');

    $comm_conf['plugin'] = 'NewsComments';
    $comm_conf['resource_id'] = $news['nid'];
    $comm_conf['lang_id'] = $news['lang_id'];

    if (!empty($_POST['btnSendNewComment']) && $cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        if (!empty($user) || $cfg['nc_allow_anon_comments']) {
            empty($user) ? $comm_conf['author_id'] = 0 : $comm_conf['author_id'] = $user['uid'];
            $comm_conf['comment'] = $filter->postUtf8Txt('news_comment');
            $comm_conf['comment'] ? stdAddComment($comm_conf) : null;
            unset($comm_conf['comment']);
            unset($comm_conf['author_id']);
        }
    }

    $comm_conf['limit'] = $cfg['nc_max_comments_perpage'];

    if ($news['author_id'] == $user['uid']) {
        $perm_cfg['allow_comm_delete'] = $cfg['nc_allow_author_delete'];
        $perm_cfg['allow_comm_softdelete'] = $cfg['nc_allow_author_softdelete'];
        $perm_cfg['allow_comm_shadowban'] = $cfg['nc_allow_author_shadowban'];
    } else {
        $perm_cfg['allow_comm_delete'] = 0;
        $perm_cfg['allow_comm_softdelete'] = 0;
        $perm_cfg['allow_comm_shadowban'] = 0;
    }
    $perm_cfg['allow_comm_report'] = $cfg['nc_allow_comm_report'];

    if (($comments = stdGetComments($comm_conf, $perm_cfg))) {
        $content .= stdFormatComments($comments, $comm_conf, $perm_cfg);
    }

    if ($cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        (($user['uid'] > 1) || $cfg['nc_allow_anon_comments']) ? $content .= stdNewComment() : null;
    }

    $tpl->addtoTplVar('ADD_TO_NEWSSHOW_BOTTOM', $content);

    return true;
}

function NewsComments_AddrateDisplay(& $comments) {

    $rating_r_ids = '';
    
    if (empty($comments) || count($comments) < 1) {
        return false;
    }

    foreach ($comments as $comment) {
        empty($rating_r_ids) ? $rating_r_ids = $comment['cid'] : $rating_r_ids .= ',' . $comment['cid'];
    }

    $ratings_data = ratings_get_ratings($rating_r_ids, 'news_comments_rate');
    foreach ($comments as $key => $comment) {
        $comments[$key]['COMMENT_EXTRA'] = ratings_get_content('news_comments_rate', $comment['cid'], $comment['author_id'], $comment['lang_id'], $ratings_data);
    }
}
