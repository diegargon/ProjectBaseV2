<?php

/**
 *  News - News main include
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
 */
!defined('IN_WEB') ? exit : true;

require_once ('News_perms.inc.php');

/**
 * Put send news on top menu
 * @global array $LNG
 * @global array $cfg
 * @return string
 */
function add_menu_submit_news() {
    global $cfg, $frontend, $tpl;

    $menu_data['submit_opt'] = 1;
    if ($cfg['FRIENDLY_URL']) {
        $menu_data['submit_url'] = $cfg['REL_PATH'] . $cfg['WEB_LANG'] . '/submit_news';
    } else {
        $menu_data['submit_url'] .= "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=News&page=submit_news&lang={$cfg['WEB_LANG']}";
    }
    $frontend->addMenuItem('top_menu_left', $tpl->getTPLFile('News', 'news_menu_opt', $menu_data), 2);
    return true;
}

/**
 * Clean news title for friendly links
 * @param string $title
 * @return string
 */
function news_friendly_title($title) {
    //FIX: better way for clean all those character?
    $friendly_filter = ['"', '\'', '?', '$', ',', '.', '‘', '’', ':', ';', '[', ']', '{', '}', '*', '!', '¡', '¿', '+', '<', '>', '#', '@', '|', '~', '%', '&', '(', ')', '=', '`', '´', '/', 'º', 'ª', '\\'];
    $friendly = str_replace(' ', '-', $title);
    $friendly = str_replace($friendly_filter, '', $friendly);

    return $friendly;
}

/**
 * Put section menu element (news categories)
 * @global Categories $ctgs
 * @global array $cfg
 * @return string
 */
function news_section_menu_elements() {
    global $ctgs, $cfg;
    $ctgs->sortCatsByWeight();
    $menu_cats = $ctgs->getRootCats('News');
    $menu_data = '';

    if ($menu_cats != false) {
        foreach ($menu_cats as $menucat) {
            $cat_display_name = preg_replace('/\_/', ' ', $menucat['name']);
            $menu_data .= "<li><a href=\"{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/section/{$menucat['name']}\">{$cat_display_name}</a></li>";
        }
    }

    return $menu_data;
}

/**
 * Put section sub menu categories elements
 *
 * @global array $cfg
 * @global Categories $ctgs
 * @global SecureFilter $filter
 * @return boolean
 */
function news_section_menu_subelements() {
    global $cfg, $ctgs, $filter;

    if (empty($cat_path = $filter->getUtf8Txt('section'))) {
        return false;
    }

    $submenu_data = '';

    $cats_explode = explode($cfg['categories_separator'], $cat_path);
    if (empty($cats_explode)) {
        return false;
    }
    if (count($cats_explode) > 1) { //Back button to the previus cat;
        array_pop($cats_explode);
        $f_cats = implode($cfg['categories_separator'], $cats_explode);
        $submenu_data .= "<li><a href='{$cfg['WEB_LANG']}/section/$f_cats'><<</a></li>";
        //TODO NO FRIENDLY URL
    }

    $cat_id = $ctgs->getCatIdByNamePath("News", $cat_path);
    $childcats = $ctgs->getChilds('News', $cat_id);
    if (!empty($childcats)) {
        foreach ($childcats as $childcat) {
            if ($childcat['father'] == $cat_id) {
                $cat_display_name = preg_replace('/\_0/', ' ', $childcat['name']);
                $submenu_data .= "<li><a href='{$cfg['WEB_LANG']}/section/$cat_path{$cfg['categories_separator']}{$childcat['name']}'>$cat_display_name</a></li>";
            }
        }
    }
    return $submenu_data;
}

/**
 * Edit the dropdown field
 * @global array $LNG
 * @global MultiLang $ml
 * @global SessionMananger $sm
 * @param string $form_data
 */
function news_dropdown_profile_edit(& $form_data) {
    global $LNG, $ml, $sm;

    $user = $sm->getSessionUser();
    $user_langs = @unserialize($user['news_lang']);
    if ($user_langs !== false) {
        $form_data['dropdown_fields'] = '<dl><dt><label>' . $LNG['L_NEWS_SHOW_LANG'] . '</label></dt><dd>';
        $langs = $ml->getSiteLangs();

        $checked = '';
        foreach ($langs as $lang) {
            if (!empty($user_langs) && in_array($lang['lang_id'], $user_langs)) {
                $checked = 'checked';
            }
            $form_data['dropdown_fields'] .= '<label>' . $lang['lang_name'] . '</label>'
                    . '<input type="checkbox"  ' . $checked . ' name="langs[]" value="' . $lang['lang_id'] . '" />';
            $checked = '';
        }
        $form_data['dropdown_fields'] .= '</dd></dl>';
    }
}

/**
 * Dropdown change
 * @param array $q_data
 */
function news_dropdown_profile_change(& $q_data) {
    $sanity_fail = 0;
    $langs = $_POST['langs'];
    foreach ($langs as $lang) {
        !is_numeric($lang) ? $sanity_fail = 1 : null;
    }
    !$sanity_fail ? $q_data['news_lang'] = serialize($langs) : null;
}

function news_dropdown_items() {
    global $cfg, $frontend, $tpl;

    $menu_data['drafts_opt'] = 1;
    if ($cfg['FRIENDLY_URL']) {
        $menu_data['drafts_url'] = $cfg['REL_PATH'] . $cfg['WEB_LANG'] . '/drafts';
    } else {
        $menu_data['drafts_url'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=News&page=drafts&lang={$cfg['WEB_LANG']}";
    }
    $frontend->addMenuItem('dropdown_menu', $tpl->getTPLFile('News', 'news_menu_opt', $menu_data), 5);
}

function drafts_page() {
    global $tpl, $db, $cfg, $frontend, $sm;

    if (!$cfg['news_allow_user_drafts']) {
        $frontend->messageBox(['msg' => 'L_NEWS_E_CANT_ACCESS']);
        return false;
    }
    $content_data = [];
    $user_id = $sm->getSessionUserId();

    $query = $db->selectAll('news', ['author_id' => $user_id, 'as_draft' => '1']);

    if ($db->numRows($query) <= 0) {
        $frontend->messageBox(['msg' => 'L_NEWS_E_NODRAFTS']);
        return false;
    }
    $drafts = $db->fetchAll($query);
    $counter = 1;
    $num_items = (count($drafts));

    foreach ($drafts as $draft) {
        $content_data['TPL_CTRL'] = $counter;
        ($counter == $num_items) ? $content_data['TPL_FOOT'] = 1 : $content_data['TPL_FOOT'] = 0;
        if ($cfg['FRIENDLY_URL']) {
            $content_data['draft_url'] = "{$cfg['REL_PATH']}{$cfg['WEB_LANG']}/news/{$draft['nid']}/{$draft['page']}/{$draft['lang_id']}/";
        } else {
            $content_data['draft_url'] = "{$cfg['REL_PATH']}{$cfg['CON_FILE']}?module=News&page=view_news&nid={$draft['nid']}&news_lang_id={$draft['lang_id']}&npage={$draft['page']}/";
        }
        $content_data = array_merge($content_data, $draft);
        $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('News', 'news_drafts', $content_data));
        $counter++;
    }
}
