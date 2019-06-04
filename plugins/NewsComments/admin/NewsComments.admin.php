<?php

/**
 *  Newscomments main admin file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Newscomments
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
function NewsComments_AdminInit() {
    global $plugins;
    $plugins->expressStart("NewsComments") ? register_action("add_admin_menu", "NewsComments_AdminMenu", "5") : null;
}

function NewsComments_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("NewsComments");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "NewsComments_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "NewsComments_admin_content", $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>NewsComments</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>NewsComments</a></li>";
    }
}

function NewsComments_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function NewsComments_admin_content($params) {
    global $LNG;
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("NewsComments");
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("NewsComments");
    }
    return $page_data;
}
