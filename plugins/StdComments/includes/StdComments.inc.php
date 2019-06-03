<?php

/**
 *  StdComments - Main include
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage StdComments
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */

/**
 * Standard funtion for get comments
 * 
 * wait for plugin name and resource id in the $comm_conf array
 * for retrieve the comments relative to the plugin and id
 * 
 * @global db $db
 * @global filter $filter
 * @param array $comm_conf
 * @return boolean
 */
function stdGetComments($comm_conf) {
    global $db, $filter;

    if (empty($comm_conf['plugin']) || empty($comm_conf['resource_id'])) {
        return false;
    }

    if (!empty($comm_conf['limit']) && $filter->varInt($comm_conf['limit'])) {
        $LIMIT = 'LIMIT ' . $comm_conf['limit'];
    } else {
        $LIMIT = '';
    }
    if (isset($comm_conf['limit'])) {
        unset($comm_conf['limit']);
    }

    $query = $db->selectAll('comments', $comm_conf, $LIMIT);

    return ($db->numRows($query) < 1) ? false : $db->fetchAll($query);
}

/**
 * Standard funtion for format comments
 * 
 * retrieve list of message with html format using the template "comments"
 * 
 * @global sm $sm
 * @global tpl $tpl
 * @global array $cfg
 * @global array $LNG
 * @global timeUtil $timeUtil
 * @param array $comments
 * @param array $comm_conf
 * @return string
 */
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
        empty($author_data['avatar']) ? $author_data['avatar'] = $cfg['smbasic_default_img_avatar'] : null;
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

/**
 * Show new comment box
 * @global tpl $tpl
 * @return string
 */
function stdNewComment() {
    global $tpl;

    return $tpl->getTplFile('StdComments', 'new_comment');
}

/**
 * Add specific comment
 * 
 * $comm_conf array must have 'plugin' name and 'resource_id' and the 'comment' text
 * 
 * @global db $db
 * @param array $comment
 * @return string
 */
function stdAddComment($comm_conf) {
    global $db;

    $comm_conf['comment'] = $db->escapeStrip($comm_conf['comment']);
    $r = $db->insert('comments', $comm_conf);

    return $r ? true : false;
}

/**
 * get the number of commentarys for a give 'plugin' & 'resource_id' on $conf 
 * 
 * @global db $db
 * @param array $conf
 * @return boolean
 */
function stdGetNumComm($conf) {
    global $db;

    if (empty($conf['plugin']) || empty($conf['resource_id'])) {
        return false;
    }

    $query = $db->selectAll('comments', $conf);
    return $db->numRows($query);
}
