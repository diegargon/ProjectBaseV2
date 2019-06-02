<?php

/**
 *  AdminBasic
 * 
 *  Entry point panel
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Begin admin panel
 */
require_once("admin/AdminBasic.func.php");

admin_load_plugin_files();

$tpl->getCssFile("AdminBasic");

if (!admin_auth("r_adminmain_access")) {
    return false;
}

$admin_id = $plugins->getPluginID("AdminBasic");

!($admtab = $filter->getInt("admtab")) ? $admtab = $admin_id : null;
!($opt = $filter->getInt("opt")) ? $opt = $admin_id : null;

$params['admtab'] = $admtab;
$params['opt'] = $opt;

if ($cfg['FRIENDLY_URL']) {
    $params['url'] = "/{$cfg['WEB_LANG']}/admin";
} else {
    $params['url'] = "/{$cfg['CON_FILE']}?lang={$cfg['WEB_LANG']}&module=AdminBasic&page=adm";
}

$tpl->addtoTplVar("ADMIN_TAB_ACTIVE", $params);
$tpl->addtoTplVar("ADD_ADMIN_MENU", do_action("add_admin_menu", $params));
$tpl->addtoTplVar("ADD_TOP_MENU", do_action("add_top_menu"));
$tpl->addtoTplVar("ADD_BOTTOM_MENU", do_action("add_bottom_menu"));

if ($params['admtab'] == $admin_id) {
    $general_content = admin_general_content($params);
    if ($general_content !== false) {
        $tpl->addtoTplVar("ADM_ASIDE_MENU_OPT", admin_general_aside($params));
        $tpl->addtoTplVar("ADM_SECTION_CONTENT", $general_content);
    } else {
        return false;
    }
} else {
    $section_content = do_action("admin_get_section_content", $params);
    if ($section_content !== false) {
        $tpl->addtoTplVar("ADM_ASIDE_MENU_OPT", do_action("admin_get_aside_menu", $params));
        $tpl->addtoTplVar("ADM_SECTION_CONTENT", $section_content);
    } else {
        return false;
    }
}

$tpl->addtoTplVar("ADD_TO_BODY", $tpl->getTplFile("AdminBasic", "admin_main_body", $params));

