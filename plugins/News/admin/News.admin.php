<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function News_AdminInit() {
    global $plugins;

    $plugins->express_start("News");
    defined('News') ? register_action("add_admin_menu", "News_AdminMenu", "5") : null;
}

function News_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("News");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "News_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "News_admin_content", $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>News</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>News</a></li>";
    }
}

function News_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_NEWS_MODERATION'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LNG['L_NEWS_CATEGORIES'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function News_admin_content($params) {
    global $LNG;
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("News");
    } else if ($params['opt'] == 2) {
        $page_data = "<h1>" . $LNG['L_NEWS_MODERATION'] . "</h1>";
        $page_data .= News_AdminModeration();
    } else if ($params['opt'] == 3) {
        $page_data .= "<h1>" . $LNG['L_NEWS_CATEGORIES'] . "</h1>";
        $page_data .= SimpleCats_AdminCats("News"); //News_AdminCategories();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("News");
    } else {
        $page_data .= do_action("ADD_ADM_NEWSPAGE_OPT");
    }

    return $page_data;
}

/* FUNCTIONS */

function News_AdminModeration() {
    global $cfg, $LNG, $db;

    $content = "<div>";
    $query = $db->select_all("news", array("moderation" => "1"), "LIMIT {$cfg['news_list_moderation_limits']}");

    if ($db->num_rows($query) <= 0) {

        return "<p>{$LNG['L_NEWS_NONEWS_MOD']}</p>";
    }
    while ($news_row = $db->fetch($query)) {
        $content .= "<p>"
                . "[<a href='/{$cfg['CON_FILE']}?module=News&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&news_delete=1&npage={$news_row['page']}&admin=1'>{$LNG['L_NEWS_DELETE']}</a>]"
                . "[<a href='/{$cfg['CON_FILE']}?module=News&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&news_approved={$news_row['nid']}&lang_id={$news_row['lang_id']}&npage={$news_row['page']}&admin=1'>{$LNG['L_NEWS_APPROVED']}</a>]"
                . "<a href='/{$cfg['CON_FILE']}?module=News&page=news&nid={$news_row['nid']}&lang={$news_row['lang']}&npage={$news_row['page']}&admin=1' target='_blank'>{$news_row['title']}</a>"
                . "</p>";
    }
    $content .= "</div>";

    return $content;
}