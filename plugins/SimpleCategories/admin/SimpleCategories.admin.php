<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleCats_AdminInit() {
    global $plugins;

    $plugins->express_start("SimpleCategories") ? register_action("add_admin_menu", "SimpleCats_AdminMenu", 5) : null;
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
    global $LNG;

    //$tpl->getCSS_filePath("SimpleCats");

    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("SimpleCategories");
    } else if ($params['opt'] == 2) {
        $page_data .= SimpleCats_AdminCats();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("SimpleCategories");
    }

    return $page_data;
}

function SimpleCats_AdminCats($plugin = null) {
    global $cfg, $ml, $ctgs, $tpl;

    $tpl->getCSS_filePath("SimpleCategories");

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = 1;
        $langs['lang_name'] = $cfg['WEB_LANG'];
    }

    isset($_POST['ModCatSubmit']) ? SimpleCats_ModCategories() : null;
    isset($_POST['NewCatSubmit']) ? SimpleCats_NewCategory($plugin) : null;
    isset($_POST['DelCatSubmit']) ? SimpleCats_DelCategory() : null;

    /* NEW CAT */
    $catdata['catrow_new'] = "";
    $catdata['catlist'] = "";

    foreach ($langs as $lang) {
        $catdata['catrow_new'] .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";
    }
    $content = $tpl->getTPL_file("SimpleCategories", "adm_create_cat", $catdata);

    /* MODIFY */
    $cats = $ctgs->getCategories_all_lang($plugin);

    if ($cats !== false) {
        $catsids = [];
        foreach ($cats as $cat) {
            $catsids[] = $cat['cid'];
        }

        $catsids = array_unique($catsids);
        $foundit = 0;
        $counter = 1;
        $num_items = count($catsids);

        foreach ($catsids as $catid) {
            $catdata['catid'] = $catid;
            $catdata['TPL_CTRL'] = $counter;
            ($counter == $num_items) ? $catdata['TPL_FOOT'] = 1 : $catdata['TPL_FOOT'] = 0;
            foreach ($langs as $lang) {

                foreach ($cats as $cat) {
                    if (($catid == $cat['cid']) && ($cat['lang_id'] == $lang['lang_id'])) {
                        $catdata['catlist'] .= "<label>{$lang['lang_name']}</label>";
                        $catdata['catlist'] .= "<input type='text' name='{$lang['lang_id']}' class='cat_name' value='{$cat['name']}' />";
                        $foundit = 1;
                        $catdata['catFather'] = $cat['father'];
                        $catdata['catWeight'] = $cat['weight'];
                        $catdata['plugin'] = $cat['plugin'];
                    }
                }

                if ($foundit == 0) { //Not traslated
                    $catdata['catlist'] .= "<label>{$lang['lang_name']}</label> <input type='text' name='{$lang['lang_id']}' value='' />";
                }
                $foundit = 0;
            }

            $content .= $tpl->getTPL_file("SimpleCategories", "adm_modify_cat", $catdata);
            $catdata['catlist'] = "";
            $counter++;
        }
    }

    return $content;
}

function SimpleCats_ModCategories() {
    global $ml, $db, $filter;

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = 1;
    }

    foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = $filter->post_alphanum_middle_underscore_unicode("$lang_id"); // field name value its 1 or 2 depend of lang_id, we get GET['1']

        if (!empty($posted_name)) {
            $posted_cid = $filter->post_int("cid", 11, 1);
            $posted_father = $filter->post_int("father", 3, 1);
            $posted_weight = $filter->post_int("weight", 3, 1);
            if ($posted_cid != false) {
                empty($posted_father) ? $posted_father = 0 : null;
                empty($posted_weight) ? $posted_weight = 0 : null;
                $query = $db->select_all("categories", ["cid" => "$posted_cid", "lang_id" => "$lang_id"]);
                if ($db->num_rows($query) > 0) {
                    $db->update("categories", ["name" => "$posted_name", "father" => "$posted_father", "weight" => "$posted_weight"], ["cid" => "$posted_cid", "lang_id" => "$lang_id"]);
                }
            }
        }
    }
}

function SimpleCats_NewCategory($plugin) {
    global $filter, $ml, $db;

    $new_cid = $db->get_next_num("categories", "cid");

    if (defined('MULTILANG')) {
        $langs = $ml->get_site_langs();
    } else {
        $langs['lang_id'] = 1;
    }
    !$plugin ? $plugin = "General" : null;

    foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = $filter->post_alphanum_middle_underscore_unicode("$lang_id"); //POST['1'] 2... id return text value
        $posted_father = $filter->post_int("father", 3, 1);
        $posted_weight = $filter->post_int("weight", 3, 1);
        if (!empty($posted_name)) {
            $new_cat_ary = [
                "cid" => "$new_cid",
                "lang_id" => "{$lang['lang_id']}",
                "plugin" => $plugin,
                "name" => "$posted_name",
                "father" => "$posted_father",
                "weight" => "$posted_weight"
            ];
            $db->insert("categories", $new_cat_ary);
        }
    }
}

function SimpleCats_DelCategory() {
    global $filter, $db;

    if (( $posted_cid = $filter->post_int("cid", 11, 1))) {
        $db->delete("categories", ['cid' => $posted_cid]);
    }
}
