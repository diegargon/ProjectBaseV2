<?php

/**
 *  NewsmediaUploader - Get Links Pages
 * 
 *  Return content on scroll the div
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

global $cfg, $db, $filter;

$user = $sm->getSessionUser();

if (empty($user)) {
    return false;
}

$last_id = $filter->getInt('last_id');
$reload = $filter->getInt('reload', 1, 1);

if (!empty($last_id)) {
    $select_ary = [
        'plugin' => 'NewsMediaUploader',
        'source_id' => $user['uid'],
        'link_id' => ['value' => $last_id, 'operator' => '<']
    ];

    $query = $db->selectAll('links', $select_ary, 'ORDER BY `link_id` DESC LIMIT ' . $cfg['upload_max_list_files']);
    $content = '';
    if (($num_rows = $db->numRows($query)) > 0) {
        while ($link = $db->fetch($query)) {
            $link_thumb = str_replace('[S]', '/thumbs/', $link['link']);
            $alt = str_replace('/media[S]', '', $link['link']);
            $textToadd = '[localimg]' . $link['link'] . '[/localimg]';
            $content .= '<a href="#news_text" data-id="' . $link['link_id'] . '" onclick="addtext(\'' . $textToadd . '\'); return false"><img src="' . $link_thumb . '" alt="' . $alt . '" /></a>';
        }
    }
    print $content;
    exit();
} else if (!empty($reload)) {
    $select_ary = [
        'plugin' => 'NewsMediaUploader',
        'source_id' => $user['uid'],
    ];

    $query = $db->selectAll('links', $select_ary, 'ORDER BY `link_id` DESC LIMIT ' . $cfg['upload_max_list_files']);
    $content = '';
    if (($num_rows = $db->numRows($query)) > 0) {
        while ($link = $db->fetch($query)) {
            $link_thumb = str_replace('[S]', '/thumbs/', $link['link']);
            $alt = str_replace('/media[S]', '', $link['link']);
            $textToadd = '[localimg]' . $link['link'] . '[/localimg]';
            $content .= '<a href="#news_text" data-id="' . $link['link_id'] . '" onclick="addtext(\'' . $textToadd . '\'); return false"><img src="' . $link_thumb . '" alt="' . $alt . '" /></a>';
        }
    }
    print $content;
    exit();
}