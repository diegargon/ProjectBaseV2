<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 *  
 */
!defined('IN_WEB') ? exit : true;

require_once("admin/AdminBasic.func.php");

admin_load_plugin_files();

$tpl->getCSS_filePath("AdminBasic");

if (!admin_auth("r_adminmain_access")) {
    return false;
}

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
$tpl->addto_tplvar("ADD_ADMIN_MENU", do_action("add_admin_menu", $params));
$tpl->addto_tplvar("ADD_TOP_MENU", do_action("add_top_menu"));
$tpl->addto_tplvar("ADD_BOTTOM_MENU", do_action("add_bottom_menu"));


if ($params['admtab'] == $admin_id) {
    $general_content = admin_general_content($params);
    if ($general_content !== false) {
        $tpl->addto_tplvar("ADM_ASIDE_MENU_OPT", admin_general_aside($params));
        $tpl->addto_tplvar("ADM_SECTION_CONTENT", $general_content);
    } else {
        return false;
    }
} else {
    $section_content = do_action("admin_get_section_content", $params);
    if ($section_content !== false) {
        $tpl->addto_tplvar("ADM_ASIDE_MENU_OPT", do_action("admin_get_aside_menu", $params));
        $tpl->addto_tplvar("ADM_SECTION_CONTENT", $section_content);
    } else {
        return false;
    }
}

$tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("AdminBasic", "admin_main_body", $params));
do_action("common_web_structure");
