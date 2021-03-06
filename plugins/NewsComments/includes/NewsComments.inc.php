<?php

/**
 *  Newscomments - main include file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage Newscomments
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function News_Comments($news) {
    global $cfg, $tpl, $sm, $filter, $plugins, $LNG;
    $content = '';
    $msg = '';

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

    stdCatchAdmCommActions();

    $tpl->addScriptFile('standard', 'jquery', 'BOTTOM');

    if (!empty($_POST['btnSendNewComment']) && $cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        if (!empty($user) || $cfg['nc_allow_anon_comments']) {
            $add_comm_conf['plugin'] = 'NewsComments';
            $add_comm_conf['resource_id'] = $news['nid'];
            $add_comm_conf['lang_id'] = $news['lang_id'];
            empty($user) ? $add_comm_conf['author_id'] = 0 : $add_comm_conf['author_id'] = $user['uid'];
            $add_comm_conf['comment'] = $filter->postUtf8Txt('news_comment');
            if ($cfg['nc_moderate_comm']) {
                if ($user['uid'] > 0 && ($user['uid'] == $news['author_id'])) {
                    $add_comm_conf['moderation'] = 0;
                } else {
                    $add_comm_conf['moderation'] = 1;
                }
            }
            $add_comm_conf['comment'] ? stdAddComment($add_comm_conf) : null;
            if ($cfg['nc_moderate_comm'] && ($user['uid'] != $news['author_id'])) {
                $msg .= $LNG['L_SC_COMM_WAITING_MOD'];
            }
        }
    }

    $comm_conf['plugin'] = 'NewsComments';
    $comm_conf['resource_id'] = $news['nid'];
    $comm_conf['lang_id'] = $news['lang_id'];
    $comm_conf['limit'] = $cfg['nc_max_comments_perpage'];

    if (($user['uid'] > 0 && $news['author_id'] == $user['uid']) || ( $user['isAdmin'] || $user['isFounder'] )
    ) {
        $perm_cfg['allow_comm_delete'] = $cfg['nc_allow_author_delete'];
        $perm_cfg['allow_comm_softdelete'] = $cfg['nc_allow_author_softdelete'];
        $perm_cfg['allow_comm_shadowban'] = $cfg['nc_allow_author_shadowban'];
        $perm_cfg['allow_comm_moderation'] = $cfg['nc_moderate_comm'];
    } else {
        $perm_cfg['allow_comm_delete'] = 0;
        $perm_cfg['allow_comm_softdelete'] = 0;
        $perm_cfg['allow_comm_shadowban'] = 0;
        $perm_cfg['allow_comm_moderation'] = 0;
    }
    $perm_cfg['allow_comm_report'] = $cfg['nc_allow_comm_report'];

    if (($comments = stdGetComments($comm_conf))) {
        $content .= stdFormatComments($comments, $comm_conf, $perm_cfg);
    }

    if ($cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        (($user['uid'] > 1) || $cfg['nc_allow_anon_comments']) ? $content .= stdNewComment($msg) : null;
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
