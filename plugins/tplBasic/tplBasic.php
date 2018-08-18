<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function tplBasic_init() {
    global $tpl, $cfg;

    define('TPL', TRUE);

    $custom_lang = "tpl/lang/" . $cfg['WEB_LANG'] . "/custom.lang.php";
    file_exists($custom_lang) ? require_once($custom_lang) : false;

    if (defined('SQL')) {
        global $db;
        $tpl = new TPL($cfg, $db);
    } else {
        $tpl = new TPL($cfg);
    }

    $tpl->getCSS_filePath("tplBasic", "basic");
    $tpl->getCSS_filePath("tplBasic", "basic-mobile");
    register_action("common_web_structure", "tplBasic_web_structure", 0);
    register_uniq_action("index_page", "tplBasic_index_page", "5");
    register_uniq_action("message_page", "tplBasic_message_page");
    register_uniq_action("message_box", "tplBasic_message_box");
}

function tplBasic_Install() {
    global $db;
    require_once "db/tplBasic.db.php";
    foreach ($tplbasic_database as $query) {
        $db->query($query);
    }
    return;
}

function tplBasic_web_structure() {
    register_uniq_action("get_head", "tpl_basic_head");
    register_uniq_action("get_body", "tpl_basic_body");
    register_uniq_action("get_footer", "tpl_basic_footer");
}

function tplBasic_index_page() {
    do_action("common_web_structure");
}

function tplBasic_message_page($box_data) {
    do_action("message_box", $box_data);
    do_action("common_web_structure");
}

function tplBasic_message_box($box_data) {
    global $tpl, $LNG;

    !empty($box_data['title']) ? $data['box_title'] = $LNG[$box_data['title']] : $data['box_title'] = $LNG['L_E_ERROR'];
    !empty($box_data['backlink']) ? $data['box_backlink'] = $box_data['backlink'] : $data['box_backlink'] = "/";
    !empty($box_data['backlink_title']) ? $data['box_backlink_title'] = $LNG[$box_data['backlink_title']] : $data['box_backlink_title'] = $LNG['L_BACK'];
    $data['box_msg'] = $LNG[$box_data['msg']];
    !empty($box_data['xtra_box_msg']) ? $data['box_msg'] .= $box_data['xtra_box_msg'] : false;
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("tplBasic", "msgbox", $data));
}

function tpl_basic_head() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "head");
}

function tpl_basic_body() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "body");
}

function tpl_basic_footer() {
    global $tpl;
    return $tpl->getTPL_file("tplBasic", "footer");
}
