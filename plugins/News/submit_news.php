<?php

/**
 *  News - Submit news
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

require_once 'includes/news_form_common.php';
require_once 'includes/news_submit.inc.php';


if (!news_perm_ask('w_news_create||w_news_adm_all')) {
    $frontend->messageBox(["msg" => "L_E_NOACCESS"]);
    return false;
}

if ($plugins->checkEnabled('Multilang')) {
    $plugins->expressStart('Multilang');
}
if (!empty($_POST['submitForm'])) {
    news_submit_new_process();
} else {
    $tpl->getCssFile("News");
    $tpl->getCssFile("News", "News-mobile");
    $tpl->getCssFile("MiniEditor");
    $tpl->addScriptFile("standard", "jquery", "TOP", null);
    $tpl->addScriptFile("MiniEditor", "editor");
    $tpl->addScriptFile("News", "newsform");
    news_new_form();
}
