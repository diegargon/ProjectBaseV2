<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NMU_form_add($news) {
    global $tpl, $sm, $cfg;
    /*
      if (!empty($news['news_auth']) && $news['news_auth'] == "translator") { //translator can upload new files
      return false;
      }
     */
    ($user = $sm->getSessionUser()) ? $extra_content['UPLOAD_EXTRA'] = NMU_upload_list($user) : false;

    $tpl->AddScriptFile('standard', 'jquery', 'TOP', null);
    $tpl->AddScriptFile('NewsMediaUploader', 'plupload.full.min', 'TOP', null);
    if ($cfg['allow_remote_file_upload']) {
        $tpl->addto_tplvar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTPL_file('NewsMediaUploader', 'remoteFileUpload', $extra_content));
    }
    $tpl->addto_tplvar('NEWS_FORM_MIDDLE_OPTION', $tpl->getTPL_file('NewsMediaUploader', 'formFileUpload', $extra_content));
}

function NMU_upload_list($user) {
    global $db, $cfg;

    $content = '<div id=\"photobanner\">';
    $select_ary = [
        'plugin' => 'news_img_upload',
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
