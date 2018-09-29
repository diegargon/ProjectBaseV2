<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

require_once 'News_perms.inc.php';

function submit_news_menu() {
    global $LNG, $cfg;

    $data = '<li class="nav_left">';
    $data .= '<a rel="nofollow" href="/';
    if ($cfg['FRIENDLY_URL']) {
        $data .= $cfg['WEB_LANG'] . '/submit_news';
    } else {
        $data .= "{$cfg['CON_FILE']}?module=News&page=submit_news&lang={$cfg['WEB_LANG']}";
    }
    $data .= '">' . $LNG['L_SUBMIT_NEWS'] . '</a>';
    $data .= '</li>';

    return $data;
}

function news_friendly_title($title) {
    //FIX: better way for clean all those character?
    $friendly_filter = ['"', '\'', '?', '$', ',', '.', '‘', '’', ':', ';', '[', ']', '{', '}', '*', '!', '¡', '¿', '+', '<', '>', '#', '@', '|', '~', '%', '&', '(', ')', '=', '`', '´', '/', 'º', 'ª', '\\'];
    $friendly = str_replace(' ', '-', $title);
    $friendly = str_replace($friendly_filter, '', $friendly);

    return $friendly;
}

function news_section_nav_elements() {
    global $ctgs, $cfg;

    $menu_cats = $ctgs->getRootCats('News');
    $menu_data = '';

    if ($menu_cats != false) {
        foreach ($menu_cats as $menucat) {
            $cat_display_name = preg_replace('/\_/', ' ', $menucat['name']);
            $menu_data .= "<li><a href='/{$cfg['WEB_LANG']}/section/{$menucat['name']}'>$cat_display_name</a></li>";
        }
    }

    return $menu_data;
}

function news_section_nav_subelements() {
    global $cfg, $ctgs, $filter;

    if (empty($cat_path = $filter->get_UTF8_txt('section'))) {
        return false;
    }

    $submenu_data = '';

    $cats_explode = explode($cfg['categories_separator'], $cat_path);
    if (count($cats_explode) > 1) { //Back button to the previus cat;
        array_pop($cats_explode);
        $f_cats = implode($cfg['categories_separator'], $cats_explode);
        $submenu_data .= "<li><a href='/{$cfg['WEB_LANG']}/section/$f_cats'><<</a></li>";
        //TODO NO FRIENDLY URL
    }

    $cat_id = $ctgs->getCatIdByNamePath("News", $cat_path);
    $childcats = $ctgs->getChilds('News', $cat_id);
    if (!empty($childcats)) {
        foreach ($childcats as $childcat) {
            if ($childcat['father'] == $cat_id) {
                $cat_display_name = preg_replace('/\_0/', ' ', $childcat['name']);
                $submenu_data .= "<li><a href='/{$cfg['WEB_LANG']}/section/$cat_path{$cfg['categories_separator']}{$childcat['name']}'>$cat_display_name</a></li>";
            }
        }
    }
    return $submenu_data;
}
