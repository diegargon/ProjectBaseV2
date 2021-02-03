<?php

/**
 *  SMBasic terms file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_SM_TERMS'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ': ' . $LNG['L_SM_TERMS'];

$tpl->getCssFile('SMBasic');
$tpl->getCssFile('SMBasic', 'SMBasic-mobile');
$terms_data = [
    'WEB_NAME' => $cfg['WEB_NAME']
];

$tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SMBasic', 'terms', $terms_data));
