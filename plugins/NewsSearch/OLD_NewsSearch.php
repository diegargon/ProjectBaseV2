<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function NewsSearch_init() { 
    global $tpl, $cfg;
    print_debug("NewsSearch initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsSearch");

    if ($cfg['NS_ALLOW_ANON'] == 0) {
        return false;
    }
    $tpl->getCSS_filePath("NewsSearch");
    register_action("header_menu_element", "NS_basicSearchbox", 5);

    /* TAGS */
    if ($cfg['NS_TAGS_SUPPORT']) {
        register_action("news_new_form_add", "NS_tag_add_form");
        register_action("news_mod_submit_insert", "NS_news_mod_insert");
        register_action("news_show_page", "NS_news_tag_show_page");
        register_action("news_edit_form_add", "NS_tags_edit_form_add");
        register_action("news_fulledit_mod_set", "NS_news_edit_set_tag");
        register_action("news_limitededit_mod_set", "NS_news_edit_set_tag");
    }
}