<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_SM_TERMS'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_SM_TERMS'];

$tpl->getCSS_filePath("SMBasic");
$tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
$terms_data = [
    "WEB_NAME" => $cfg['WEB_NAME']
];

$tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "terms", $terms_data));
