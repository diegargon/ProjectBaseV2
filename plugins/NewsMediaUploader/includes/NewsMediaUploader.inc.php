<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NMU_form_add($news) {
    global $tpl, $sm, $cfg;

    $user = $sm->getSessionUser();

    if ($cfg['upload_allow_anon'] == 0 && empty($user['uid'])) {
        $tpl->addtoTplVar('NEWS_FORM_TOP_OPTION', NMU_disable_warn());
        return false;
    }
    /*
      if (!empty($news['news_auth']) && $news['news_auth'] == "translator") { //translator can upload new files
      return false;
      }
     */
    $tpl->getCssFile('NewsMediaUploader');

    $extra_content['UPLOAD_EXTRA'] = '';
    ($user = $sm->getSessionUser()) ? $extra_content['UPLOAD_EXTRA'] = NMU_upload_list($user) : false;

    $tpl->addScriptFile('standard', 'jquery', 'TOP', null);
    $tpl->addScriptFile('NewsMediaUploader', 'plupload.full.min', 'TOP', null);
    if ($cfg['allow_remote_file_upload']) {
        $tpl->addtoTplVar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTplFile('NewsMediaUploader', 'remoteFileUpload', $extra_content));
    }
    $tpl->addtoTplVar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTplFile('NewsMediaUploader', 'formFileUpload', $extra_content));
}

function NMU_upload_list($user) {
    global $db, $cfg;

    $content = '<div id="photobanner">';
    $select_ary = [
        'plugin' => 'NewsMediaUploader',
        'source_id' => $user['uid'],
    ];

    $query = $db->select_all('links', $select_ary, 'ORDER BY `date` DESC LIMIT ' . $cfg['upload_max_list_files']);
    while ($link = $db->fetch($query)) {
        $link_thumb = str_replace('[S]', '/thumbs/', $link['link']);
        $textToadd = '[localimg]' . $link['link'] . '[/localimg]';
        $content .= "<a href=\"#news_text\" onclick=\"addtext('$textToadd'); return false\"><img src='$link_thumb' alt='' /></a>";
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
