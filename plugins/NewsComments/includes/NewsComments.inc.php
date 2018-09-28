<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
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
            $plugins->check_enabled_provider('RATINGS') &&
            $plugins->express_start_provider('RATINGS')
    ) {
        $tpl->addScriptFile('StdRatings', 'rate', 'BOTTOM');
        $tpl->getCssFile('NewsComments');
        register_action('std_format_comments', 'NewsComments_AddrateDisplay');

        if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
                ( ($filter->post_strict_chars('rate_section')) == 'news_comments_rate')
        ) {
            if (rating_rate_getPost('news_comments_rate')) {
                die('[{"status": "6", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
            } else {
                die('[{"status": "4", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
            }
        }
    }

    if (!$plugins->check_enabled_provider('COMMENTS') || !$plugins->express_start_provider('COMMENTS')) {
        return false;
    }

    $tpl->addScriptFile('standard', 'jquery', 'BOTTOM');

    $comm_conf['plugin'] = 'NewsComments';
    $comm_conf['resource_id'] = $news['nid'];
    $comm_conf['lang_id'] = $news['lang_id'];

    if (!empty($_POST['btnSendNewComment']) && $cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        if (!empty($user) || $cfg['nc_allow_anon_comments']) {
            empty($user) ? $comm_conf['author_id'] = 0 : $comm_conf['author_id'] = $user['uid'];
            $comm_conf['comment'] = $filter->post_UTF8_txt('news_comment');
            $comm_conf['comment'] ? stdAddComment($comm_conf) : null;
            unset($comm_conf['comment']);
            unset($comm_conf['author_id']);
        }
    }

    $comm_conf['limit'] = $cfg['nc_max_comments_perpage'];

    if (($comments = stdGetComments($comm_conf))) {
        $content .= stdFormatComments($comments, $comm_conf);
    }

    if ($cfg['nc_allow_new_comments'] && !$news['comments_disabled']) {
        (($user['uid'] > 1) || $cfg['nc_allow_anon_comments']) ? $content .= stdNewComment() : null;
    }

    $tpl->addtoTplVar('ADD_TO_NEWSSHOW_BOTTOM', $content);

    return true;
}

function NewsComments_AddrateDisplay(& $comments) {

    if (count($comments) < 1) {
        return false;
    }

    $rating_r_ids = '';

    foreach ($comments as $comment) {
        empty($rating_r_ids) ? $rating_r_ids = $comment['cid'] : $rating_r_ids .= ',' . $comment['cid'];
    }

    $ratings_data = ratings_get_ratings($rating_r_ids, 'news_comments_rate');
    foreach ($comments as $key => $comment) {
        $comments[$key]['COMMENT_EXTRA'] = ratings_get_content('news_comments_rate', $comment['cid'], $comment['rating'], $comment['author_id'], $comment['lang_id'], $ratings_data);
    }
}

/* OLD to CRON 
 
 function NewsVote_Calc_Rating($rid, $section) {
    global $db;
    $where_ary = [
        'section' => $section,
        'resource_id' => $rid,
    ];
    $query = $db->select_all('rating', $where_ary);
    $vote_sum = 0;
    if (($num_votes = $db->num_rows($query)) > 0) {
        while ($vote_row = $db->fetch($query)) {
            $vote_sum = $vote_sum + $vote_row['vote_value'];
        }
        $new_rate = $vote_sum / $num_votes;
        if ($section == 'news_comments_rate') {
            $db->update('comments', ['rating' => $new_rate], ['cid' => $rid]);
        } else if ($section == 'news_rate') {
            $db->update('news', ['rating' => $new_rate], ['nid' => $rid]);
        }
    }
}

 *  
function newsvote_news_user_rating($nid, $lang_id, $user_rating) {
    global $db, $cfg, $UXtra;

    $query = $db->select_all("news", ["nid" => "$nid", "lang_id" => $lang_id, "page" => 1], "LIMIT 1");
    $news_data = $db->fetch($query);
    $author_xtrData = $UXtra->getById($news_data['author_id']);
    if ($author_xtrData == false) {
        $author_xtrData['uid'] = $news_data['author_id'];
        $author_xtrData['rating_user'] = 0;
        $author_xtrData['rating_times'] = 0;
    }
    $new_rating = $author_xtrData['rating_user'] + $user_rating;
    $new_rating_times = ++$author_xtrData['rating_times'];

    $UXtra->upsert(["rating_user" => "$new_rating", "rating_times" => "$new_rating_times"], ["uid" => $author_xtrData['uid']]);

    if (!empty($news_data['translator_id']) && $cfg['NEWSVOTE_NEWS_USER_RATING_NT'] && $news_data['moderation'] == 0) {
        $translator_xtrData = $UXtra->getById($news_data['translator_id']);
        $t_new_rating = $translator_xtrData['rating_user'] + $user_rating;
        $t_new_rating_times = ++$translator_xtrData['rating_times'];
        $UXtra->upsert(["rating_user" => "$t_new_rating", "rating_times" => "$t_new_rating_times"], ["uid" => $translator_xtrData['uid']]);
    }
}

function newsvote_comment_user_rating($cid, $lang_id, $user_rating) {
    global $db, $cfg, $UXtra;

    $query = $db->select_all("comments", ["cid" => "$cid", "lang_id" => $lang_id], "LIMIT 1");
    $comment_data = $db->fetch($query);
    $author_xtrData = $UXtra->getById($comment_data['author_id']);
    if ($author_xtrData == false) {
        $author_xtrData['uid'] = $comment_data['author_id'];
        $author_xtrData['rating_user'] = 0;
        $author_xtrData['rating_times'] = 0;
    }
    if ($cfg['NEWSVOTE_COMMENT_USER_RATING_MODE'] == 1) {
        $new_rating = ++$author_xtrData['rating_user'];
    } else if ($cfg['NEWSVOTE_COMMENT_USER_RATING_MODE'] == "div2") {
        $new_rating = $author_xtrData['rating_user'] + round($user_rating / 2);
        $new_rating == 0 ? $new_rating = 1 : false;
    } else {
        $new_rating = $author_xtrData['rating_user'] + $user_rating;
    }
    $new_rating_times = ++$author_xtrData['rating_times'];

    $UXtra->upsert(["rating_user" => "$new_rating", "rating_times" => "$new_rating_times"], ["uid" => $author_xtrData['uid']]);
}

*/