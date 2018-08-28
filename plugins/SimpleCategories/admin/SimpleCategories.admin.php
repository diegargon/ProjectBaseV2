<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleCats_AdminInit() {
    global $ctgs, $plugins;

    !isset($ctgs) ? ( $plugins->express_start("SimpleCategories")) : null;

    if (defined('CATS')) {
        register_action("add_admin_menu", "SimpleCats_AdminMenu", 5);
    }
}

function SimpleCats_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("SimpleCategories");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "SimpleCats_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "SimpleCats_AdminContent", $params);
        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>SimpleCats</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>SimpleCats</a></li>";
    }
}

function SimpleCats_AdminAside($params) {
    global $LNG;

    return "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_CATS_CATS'] . "</a></li>\n" .
            "<li><a href='{$params['url']}&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function SimpleCats_AdminContent($params) {
    global $LNG, $tpl;

    //$tpl->getCSS_filePath("SimpleCats");
    $msg = "";
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SimpleCategories");
    } else if ($params['opt'] == 2) {
//        isset($_POST['btnDelPerm']) ? $msg = SimpleACL_DelPerm() : false;
        //       isset($_POST['btnNewPerm']) ? $msg = SimpleACL_AddPerm() : false;
//        $page_data = $LNG['L_GENERAL'] . ": " . $LNG['L_ACL_PERM_GROUPS'];
//        $page_data .= SimpleACL_ShowPermGroups($msg);
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SimpleCategories");
    }

    return $page_data;
}
