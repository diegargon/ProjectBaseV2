<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 *  
 */
!defined('IN_WEB') ? exit : true;

$user = $sm->getSessionUser();

if (!$user || (defined('ACL') && !$acl_auth->acl_ask("admin_read"))) {
    $msgbox['msg'] = "L_E_NOACCESS";
    $msgbox['backlink'] = $sm->getPage("login");
    $msgbox['backlink_title'] = "L_LOGIN";
    do_action("message_page", $msgbox);
    return false;
}

if (!defined('ACL') && $user['isAdmin'] != 1) {
    $msgbox['msg'] = "L_E_NOACCESS";
    $msgbox['backlink'] = $sm->getPage("login");
    $msgbox['backlink_title'] = "L_LOGIN";
    do_action("message_page", $msgbox);
    return false;
}

admin_load_plugin_files();

$admin_id = $plugins->getPluginID("AdminBasic");

!($admtab = $filter->get_int("admtab")) ? $admtab = $admin_id : null;
!($opt = $filter->get_int("opt")) ? $opt = $admin_id : null;

$params['admtab'] = $admtab;
$params['opt'] = $opt;

if ($cfg['FRIENDLY_URL']) {
    $params['url'] = "/{$cfg['WEB_LANG']}/admin";
} else {
    $params['url'] = "/{$cfg['CON_FILE']}?lang={$cfg['WEB_LANG']}&module=AdminBasic&page=adm";
}

$tpl->addto_tplvar("ADMIN_TAB_ACTIVE", $params);
$tpl->getCSS_filePath("AdminBasic");
$tpl->addto_tplvar("ADD_ADMIN_MENU", do_action("add_admin_menu", $params));
$tpl->addto_tplvar("ADD_TOP_MENU", do_action("add_top_menu"));
$tpl->addto_tplvar("ADD_BOTTOM_MENU", do_action("add_bottom_menu"));


if ($params['admtab'] == $admin_id) {
    $tpl->addto_tplvar("ADM_ASIDE_MENU_OPT", admin_general_aside($params));
    $tpl->addto_tplvar("ADM_SECTION_CONTENT", admin_general_content($params));
} else {
    $tpl->addto_tplvar("ADM_ASIDE_MENU_OPT", do_action("admin_get_aside_menu", $params));
    $tpl->addto_tplvar("ADM_SECTION_CONTENT", do_action("admin_get_section_content", $params));
}

$tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("AdminBasic", "admin_main_body", $params));
do_action("common_web_structure");
