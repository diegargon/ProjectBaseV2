<?php

/**
 *  News - Submit news
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
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
    $tpl->addScriptFile("standard", "jquery", "TOP", 0);
    $tpl->addScriptFile("MiniEditor", "editor");
    $tpl->addScriptFile("News", "newsform");
    if ($cfg['news_side_scroll']) {
        $tpl->addScriptFile('News', 'news_scroll', 'BOTTOM');
    }

    news_new_form();
}
