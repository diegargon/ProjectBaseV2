<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */

function stdGetComments($comm_conf) {
    global $db, $filter;

    if (empty($comm_conf['plugin']) || empty($comm_conf['resource_id'])) {
        return false;
    }

    if (!empty($comm_conf['limit']) && $filter->var_int($comm_conf['limit'])) {
        $LIMIT = 'LIMIT ' . $comm_conf['limit'];
    } else {
        $LIMIT = '';
    }
    if (isset($comm_conf['limit'])) {
        unset($comm_conf['limit']);
    }

    $query = $db->select_all('comments', $comm_conf, $LIMIT);

    return ($db->num_rows($query) < 1) ? false : $db->fetch_all($query);
}

function stdFormatComments($comments, $comm_conf) {
    global $sm, $tpl, $cfg, $LNG, $timeUtil;

    $counter = 0;
    $uid_list = $content = '';
    $num_comments = count($comments);

    do_action('std_format_comments', $comments);

    /* First, retrieve all authors in one query) */
    foreach ($comments as $comment_row) {
        if (!empty($comment_row['author_id'])) {
            !empty($uid_list) ? $uid_list = $comment_row['author_id'] : $uid_list .= $comment_row['author_id'];
        }
        if (!empty($comment_row['translator_id'])) {
            !empty($uid_list) ? $uid_list = $comment_row['translator_id'] : $uid_list .= $comment_row['translator_id'];
        }
    }
    !empty($uid_list) ? $sm->setUsersInCacheByIDs($uid_list) : null;

    foreach ($comments as $comment_row) {
        $counter == 0 ? $comment_row['TPL_FIRST'] = 1 : false;
        $counter == ($num_comments - 1 ) ? $comment_row['TPL_LAST'] = 1 : false;
        $counter++;

        if (!$author_data = $sm->getUserByID($comment_row['author_id'])) {
            $author_data['uid'] = 0;
            $author_data['username'] = $LNG['L_ANONYMOUS'];
        }

        $comment_row = array_merge($author_data, $comment_row);

        if ($cfg['FRIENDLY_URL']) {
            $comment_row['p_url'] = '/' . $cfg['WEB_LANG'] . '/profile&viewprofile=' . $author_data['uid'];
        } else {
            $comment_row['p_url'] = '/' . $cfg['CON_FILE'] . '?module=SMBasic&page=profile&viewprofile=' . $author_data['uid'] . '&lang=' . $cfg['WEB_LANG'];
        }

        $comment_row['date'] = $timeUtil->formatDbDate($comment_row['date']);
        $content .= $tpl->getTplFile('StdComments', 'comments', $comment_row);
    }

    return $content;
}

function stdNewComment() {
    global $tpl;

    return $tpl->getTplFile('StdComments', 'new_comment');
}

function stdAddComment($comment) {
    global $db;

    $comment['comment'] = $db->escape_strip($comment['comment']);
    $r = $db->insert('comments', $comment);

    return $r ? true : false;
}

function stdGetNumComm($conf) {
    global $db;

    if (empty($conf['plugin']) || empty($conf['resource_id'])) {
        return false;
    }

    $query = $db->select_all('comments', $conf);
    return $db->num_rows($query);
}
