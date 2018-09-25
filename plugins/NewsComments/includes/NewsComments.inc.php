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

    if ($cfg['nc_vote_comments'] && $plugins->check_enabled_provider('RATINGS')) {
        if ($plugins->express_start_provider('RATINGS')) {
            $tpl->getCSS_filePath('NewsComments');
            register_action('NewsComments_format_comments', 'NewsComments_Addrate');
            if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
                    ($user_rate = $filter->post_int("comment_rate", 5, 1)) &&
                    ($cid = $filter->post_int('rate_rid'))
            ) {
                if (rating_rate($user['uid'], $cid, 'news_comments_rate', $user_rate)) {
                    die('[{"status": "6", "msg": "' . $LNG['L_VOTE_SUCCESS'] . '"}]');
                } else {
                    die('[{"status": "4", "msg": "' . $LNG['L_VOTE_INTERNAL_ERROR'] . '"}]');
                }
            }
        }
    }

    if (!$plugins->check_enabled_provider('COMMENTS') || !$plugins->express_start_provider('COMMENTS')) {
        return false;
    }

    $tpl->AddScriptFile('standard', 'jquery', 'BOTTOM');
    !$cfg['ITS_BOT'] ? $tpl->AddScriptFile('NewsComments', 'comment_rate', 'BOTTOM') : null;

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

    $tpl->addto_tplvar('ADD_TO_NEWSSHOW_BOTTOM', $content);

    return true;
}

function NewsComments_Addrate(& $comments) {
    global $db, $cfg, $tpl, $sm;

    if (count($comments) < 1) {
        return false;
    }

    $rating_cids = '';

    foreach ($comments as $comment) {
        empty($rating_cids) ? $rating_cids = $comment['cid'] : $rating_cids .= ',' . $comment['cid'];
    }

    $where_ary['section'] = 'news_comments_rate';
    $where_ary['resource_id'] = ['value' => '(' . $rating_cids . ')', 'operator' => 'IN'];

    $query = $db->select_all('rating', $where_ary);
    $ratings = $db->fetch_all($query);

    $user = $sm->getSessionUser();

    //TODO MANAGE ANONYMOUS
    foreach ($comments as $key => $comment) {
        $rate_data['btnExtra'] = " style=\"background: url({$cfg['dflt_vote_visuals_url']}) no-repeat;\" ";
        if ($comment['author_id'] == $user['uid']) {
            $rate_data['show_pointer'] = 0;
            $rate_data['btnExtra'] .= "disabled";
        } else {
            $rate_data['show_pointer'] = 1;
            foreach ($ratings as $rating) { //buscamos si ya hay algun rating, por usuario al comentario  si es asi deshabilitamos
                if (($rating['uid'] == $user['uid']) && ($comment['cid'] == $rating['resource_id'] )) {
                    $rate_data['btnExtra'] .= "disabled";
                    $rate_data['show_pointer'] = 0;
                    break;
                }
            }
        }

        $rate_stars = rating_css_display($comment['rating']);
        $rate_data = array_merge($rate_data, $comment, $rate_stars);
        $rate_content = $tpl->getTpl_file('NewsComments', 'comment_rate', $rate_data);
        $comments[$key]['COMMENT_EXTRA'] = $rate_content;
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