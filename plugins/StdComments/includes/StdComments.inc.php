<?php

/**
 *  StdComments - Main include
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage StdComments
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */

/**
 * Standard funtion for get comments
 * 
 * wait for plugin name and resource id in the $comm_conf array
 * for retrieve the comments relative to the plugin and id
 * 
 * @global Database $db
 * @global SecureFilter $filter
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
 * retrieve list of messages with html format using the template "comments"
 * 
 * @global SessionManager $sm
 * @global TPL $tpl
 * @global array $cfg
 * @global array $LNG
 * @global timeUtil $timeUtil
 * @param array $comments
 * @param array $comm_conf
 * @param array $perm_cfg
 * @return string
 */
function stdFormatComments($comments, $comm_conf, $perm_cfg) {
    global $sm, $tpl, $cfg, $LNG, $timeUtil;

    $counter = 0;
    $uid_list = $content = '';
    if (empty($comments)) {
        return false;
    }
    $num_comments = count($comments);

    do_action('std_format_comments', $comments);

    /* First, retrieve all authors in one query) */
    $uid_ary = [];
    foreach ($comments as $comment_row) {
        if (!empty($comment_row['author_id'])) {
            $uid_ary[] = $comment_row['author_id'];
        }
        if (!empty($comment_row['translator_id'])) {
            $uid_ary[] = $comment_row['translator_id'];
        }
    }
    $uid_ary = array_unique($uid_ary);
    $uid_list = implode(", ", $uid_ary);

    !empty($uid_list) ? $sm->setUsersInCacheByIDs($uid_list) : null;

    foreach ($comments as $comment_row) {

        /* Fix buscar forma en template de cerrar TPL_LAST si es el ultimo y no se va a mostrar */
        if ($comment_row['moderation'] == 1 && $perm_cfg['allow_comm_moderation'] == 0) {
            $num_comments--;
            $counter == $num_comments ? $content .= "</section></div>" : null; //if it the last close
            continue;
        }
        if (!$author_data = $sm->getUserByID($comment_row['author_id'])) {
            $author_data['uid'] = 0;
            $author_data['username'] = $LNG['L_ANONYMOUS'];
        }
        $user = $sm->getSessionUser();
        if ($comment_row['shadow_ban'] && !$user['isAdmin'] && $comment_row['author_id'] != $user['uid']) {
            $num_comments--;
            $counter == $num_comments ? $content .= "</section></div>" : null;
            continue;
        }
        if (!$user['isAdmin'] && $comment_row['soft_delete']) {
            $num_comments--;
            $counter == $num_comments ? $content .= "</section></div>" : null;
            continue;
        }


        $counter == 0 ? $comment_row['TPL_FIRST'] = 1 : null;
        $counter == ($num_comments - 1 ) ? $comment_row['TPL_LAST'] = 1 : null;
        $counter++;

        empty($author_data['avatar']) ? $author_data['avatar'] = $cfg['STATIC_SRV_URL'] . '/' . $cfg['smbasic_default_img_avatar'] : null;
        $comment_row = array_merge($author_data, $comment_row);

        if ($cfg['FRIENDLY_URL']) {
            $comment_row['p_url'] = $cfg['REL_PATH'] . $cfg['WEB_LANG'] . '/profile&viewprofile=' . $author_data['uid'];
        } else {
            $comment_row['p_url'] = $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?module=SMBasic&page=profile&viewprofile=' . $author_data['uid'] . '&lang=' . $cfg['WEB_LANG'];
        }

        $perm_comm = stdGetCommPerms($author_data['uid'], $perm_cfg);
        $comment_row = array_merge($perm_comm, $comment_row);

        $comment_row['admbar'] = stdAdmBar($comment_row, $perm_comm);

        $comment_row['date'] = $timeUtil->formatDbDate($comment_row['date']);
        $content .= $tpl->getTplFile('StdComments', 'comments', $comment_row);
    }

    return $content;
}

/**
 * Get the comment admin options bar
 * 
 * @global array $LNG
 * @param array $comm
 * @param array $perm
 * @return string
 */
function stdAdmBar($comm, $perm) {
    global $LNG;

    $bar = '';
    if ($perm['report_comm']) {
        ($comm['reported'] > 0) ? $class = 'class="adm_btn_on_comm"' : $class = '';
        $bar .= '<input ' . $class . ' type="submit" value="' . $LNG['L_SC_REPORT'] . '" name="report" />';
    }

    if ($perm['delete_comm']) {
        $bar .= '<input type="submit" value="' . $LNG['L_SC_DELETE'] . '" name="delete" />';
    }
    if ($perm['soft_delete_comm']) {
        ($comm['soft_delete'] > 0) ? $class = 'class="adm_btn_on_comm"' : $class = '';
        $bar .= '<input ' . $class . ' type="submit" value="' . $LNG['L_SC_SOFTDELETE'] . '" name="softdelete" />';
    }

    if ($perm['shadow_ban_comm']) {
        ($comm['shadow_ban'] > 0) ? $class = 'class="adm_btn_on_comm"' : $class = '';
        $bar .= '<input ' . $class . ' type="submit" value="' . $LNG['L_SC_SHADOWBAN'] . '" name="shadowban" />';
    }
    if ($perm['comm_moderation']) {
        ($comm['moderation'] == 1) ? $class = 'class="adm_btn_on_comm"' : $class = '';
        $bar .= '<input ' . $class . ' type="submit" value="' . $LNG['L_SC_MODERATION'] . '" name="moderation" />';
    }
    return $bar;
}

/**
 * Catch administrative actions
 * 
 * @return boolean
 */
function stdCatchAdmCommActions() {

    if (!isset($_POST['cid']) || !is_numeric($_POST['cid'])) {
        return false;
    }
    if (isset($_POST['report'])) {
        stdCommReport($_POST['cid']);
    }
    if (isset($_POST['delete'])) {
        stdCommDelete($_POST['cid']);
    }
    if (isset($_POST['softdelete'])) {
        stdCommSofDelete($_POST['cid']);
    }
    if (isset($_POST['shadowban'])) {
        stdCommShadowBan($_POST['cid']);
    }
    if (isset($_POST['moderation'])) {
        stdCommApprove($_POST['cid']);
    }
}

/**
 * Show new comment box
 * 
 * @global tpl $tpl
 * @return string
 */
function stdNewComment($msg = null) {
    global $tpl;
    $comm_data = [];

    isset($msg) ? $comm_data['msg'] = $msg : null;
    return $tpl->getTplFile('StdComments', 'new_comment', $comm_data);
}

/**
 * Add specific comment
 * 
 * $comm_conf array must have 'plugin' name and 'resource_id' and the 'comment' text
 * 
 * @global Database $db
 * @param array $comm_conf
 * @return string
 */
function stdAddComment($comm_conf) {
    global $db;

    $comm_conf['comment'] = $db->escapeStrip($comm_conf['comment']);
    $r = $db->insert('comments', $comm_conf);

    return $r ? true : null;
}

/**
 * Get the number of commentarys for a give 'plugin' & 'resource_id' on $conf 
 * 
 * @global Database $db
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

/**
 * Determine admistrative bar permission
 * 
 * @global SessionManager $sm
 * @param array $comm_author_id
 * @param array $perm_cfg
 * @return array
 */
function stdGetCommPerms($comm_author_id, $perm_cfg) {
    global $sm;

    $user = $sm->getSessionUser();

    $perm_comm['report_comm'] = $perm_cfg['allow_comm_report'];

    if ($user['isAdmin']) {
        $perm_comm['delete_comm'] = 1;
        $perm_comm['soft_delete_comm'] = 1;
        $perm_comm['shadow_ban_comm'] = 1;
        $perm_comm['comm_moderation'] = 1;
        return $perm_comm;
    }

    $perm_comm['delete_comm'] = $perm_cfg['allow_comm_delete'];
    $perm_comm['soft_delete_comm'] = $perm_cfg['allow_comm_softdelete'];
    $perm_comm['shadow_ban_comm'] = $perm_cfg['allow_comm_shadowban'];
    $perm_comm['comm_moderation'] = $perm_cfg['allow_comm_moderation'];

    $comm_author = $sm->getUserByID($comm_author_id);
    if ($comm_author['isAdmin'] && !$user['isAdmin']) {
        $perm_comm['delete_comm'] = 0;
        $perm_comm['soft_delete_comm'] = 0;
        $perm_comm['shadow_ban_comm'] = 0;
        $perm_comm['comm_moderation'] = 0;
    }

    if ($user['uid'] == $comm_author_id) {
        $perm_comm['delete_comm'] = 1;
        $perm_comm['shadow_ban_comm'] = 0;
        $perm_comm['soft_delete_comm'] = 0;
        $perm_comm['report_comm'] = 0;
        $perm_comm['comm_moderation'] = 0;
    }


    return $perm_comm;
}

/**
 * Report commentary
 * 
 * @global Database $db
 * @param int $comm_id
 */
function stdCommReport($comm_id) {
    global $db;

    $db->plusOne('comments', 'reported', ['cid' => $comm_id], 'LIMIT 1');
}

/**
 * Delete commentary
 * 
 * @global Database $db
 * @param int $comm_id
 */
function stdCommDelete($comm_id) {
    global $db;

    $db->delete('comments', ['cid' => $comm_id]);
}

/**
 * Hide but not delete
 * 
 * @global Database $db
 * @param int $comm_id
 */
function stdCommSofDelete($comm_id) {
    global $db;

    $db->toggleField('comments', 'soft_delete', ['cid' => $comm_id]);
}

/**
 * Hide to all except comm author
 * 
 * @global Database $db
 * @param int $comm_id
 */
function stdCommShadowBan($comm_id) {
    global $db;

    $db->toggleField('comments', 'shadow_ban', ['cid' => $comm_id]);
}

/**
 * Approve message
 * @global Database $db
 * @param int $comm_id
 */
function stdCommApprove($comm_id) {
    global $db;

    $db->toggleField('comments', 'moderation', ['cid' => $comm_id]);
}
