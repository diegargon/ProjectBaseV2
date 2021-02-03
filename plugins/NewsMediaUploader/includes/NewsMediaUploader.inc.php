<?php

/**
 *  NewsmediaUploader - Main Include file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function NMU_form_add($news) {
    global $tpl, $sm, $cfg;

    $user = $sm->getSessionUser();

    if ($cfg['upload_allow_anon'] == 0 && (empty($user) || $user['uid'] == 0)) {
        $tpl->addtoTplVar('NEWS_FORM_TOP_OPTION', NMU_disable_warn());
        return false;
    }
    /*
      if (!empty($news['news_auth']) && $news['news_auth'] == "translator") { //translator can upload new files
      return false;
      }
     */
    $tpl->getCssFile('NewsMediaUploader');

    $extra_content['uploaded_content'] = '';
    (!empty($user) && $user['uid'] > 0) ? $extra_content['uploaded_content'] = NMU_upload_list() : null;

    $tpl->addScriptFile('standard', 'jquery', 'TOP', 0);
    $tpl->addScriptFile('NewsMediaUploader', 'plupload.full.min', 'TOP', 0);
    if ($cfg['allow_remote_file_upload']) {
        $tpl->addtoTplVar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTplFile('NewsMediaUploader', 'remoteFileUpload', $extra_content));
    }
    $tpl->addtoTplVar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTplFile('NewsMediaUploader', 'formFileUpload', $extra_content));
}

function NMU_upload_list() {
    global $db, $sm, $cfg;

    $user = $sm->getSessionUser();

    $content = '<div id="photobanner">';
    $select_ary = [
        'plugin' => 'NewsMediaUploader',
        'source_id' => $user['uid'],
    ];

    $query = $db->selectAll('links', $select_ary, 'ORDER BY `link_id` DESC LIMIT ' . $cfg['upload_max_list_files']);

    if (($num_rows = $db->numRows($query)) > 0) {
        while ($link = $db->fetch($query)) {
            $link_thumb = str_replace('[S]', '/thumbs/', $link['link']);
            $alt = str_replace('media[S]', '', $link['link']);
            $textToadd = '[localimg]' . $link['link'] . '[/localimg]';
            $content .= '<a href="#news_text" data-id="' . $link['link_id'] . '" onclick="addtext(\'' . $textToadd . '\'); return false"><img src="' . $link_thumb . '" alt="' . $alt . '" /></a>';
        }
    }
    $content .= '</div>';

    return $content;
}

function NMU_disable_warn() {
    global $LNG;
    $content = '<p class="warn_disable">' . $LNG['L_NMU_W_DISABLE'] . '</p>';
    return $content;
}

function NMU_convertToBytes($from) { // SO 11807115
    $number = substr($from, 0, -2);
    switch (strtoupper(substr($from, -2))) {
        case 'KB':
            return $number * 1024;
        case 'MB':
            return $number * pow(1024, 2);
        case 'GB':
            return $number * pow(1024, 3);
        case 'TB':
            return $number * pow(1024, 4);
        case 'PB':
            return $number * pow(1024, 5);
        default:
            return $from;
    }
}
