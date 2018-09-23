<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

function stdGetComments($comm_conf) {

    global $tpl, $db, $filter, $sm, $cfg;

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
    if ($db->num_rows($query) < 1) {
        return false;
    } else {
        return $db->fetch_all($query);
    }
}

function stdFormatComments($comments, $comm_conf) {
    global $sm, $tpl, $cfg;
    $counter = 0;
    $content = '';
    $num_comments = count($comments);

    foreach ($comments as $comment_row) {
        $counter == 0 ? $comment_row['TPL_FIRST'] = 1 : false;
        $counter == ($num_comments - 1 ) ? $comment_row['TPL_LAST'] = 1 : false;
        $counter++;

        $author_data = $sm->getUserByID($comment_row['author_id']);
        $comment_row = array_merge($author_data, $comment_row);
        do_action($comm_conf['plugin'] . '_format_comments', $comment_row);

        if ($cfg['FRIENDLY_URL']) {
            $comment_row['p_url'] = '/' . $cfg['WEB_LANG'] . '/profile&viewprofile=' . $author_data['uid'];
        } else {
            $comment_row['p_url'] = '/' . $cfg['CON_FILE'] . '?module=SMBasic&page=profile&viewprofile=' . $author_data['uid'] . '&lang=' . $cfg['WEB_LANG'];
        }

        $content .= $tpl->getTPL_file('StdComments', 'comments', $comment_row);
    }

    return $content;
}

function stdNewComment() {
    global $tpl;

    return $tpl->getTPL_file('StdComments', 'new_comment');
}

function stdAddComment($comment) {
    global $db;
    $comment['comment'] = $db->escape_strip($comment['comment']);
    $db->insert('comments', $comment);
}

function stdGetNumComm($conf) {
    global $db;

    if (empty($conf['plugin']) || empty($conf['resource_id'])) {
        return false;
    }

    $query = $db->select_all('comments', $conf);
    return $db->num_rows($query);
}
