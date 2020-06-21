<?php

/**
 *  Multilang admin main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Multilang
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Admin initialization
 * 
 * @global Plugins $plugins
 */
function Multilang_AdminInit() {
    global $plugins;
    /* Check if start for configure */
    $plugins->expressStart('Multilang') ? register_action('add_admin_menu', 'Multilang_AdminMenu') : null;
}

/**
 * Show admin menu
 * @global Plugins $plugins
 * @param array $params
 * @return string
 */
function Multilang_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('Multilang');

    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'Multilang_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'Multilang_AdminContent');
        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">Multilang</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">Multilang</a></li>';
    }
}

/**
 * Show admin aside menu
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function Multilang_AdminAside($params) {
    global $LNG;
    return "<li><a href='{$params['url']}&admtab={$params['admtab']}&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab={$params['admtab']}&opt=2'>" . $LNG['L_ML_LANGS'] . "</a></li>\n"
            . "<li><a href='{$params['url']}&admtab={$params['admtab']}&opt=3'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

/**
 * Show admin content
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function Multilang_AdminContent($params) {
    global $LNG;

    $page_data = '<h1>Multilang</h1>';

    if ($params['opt'] == 1) {
        $page_data .= $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'];
        $page_data .= Admin_GetPluginState('Multilang');
    } else if ($params['opt'] == 2) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            !empty($_POST['btnModifyLang']) ? $ml_error_message = Multilang_ModifyLang() : null;
            !empty($_POST['btnCreateLang']) ? $ml_error_message = Multilang_CreateLang() : null;
            !empty($_POST['btnDeleteLang']) ? $ml_error_message = Multilang_DeleteLang() : null;
            if ($ml_error_message == false) {
                header('Refresh:0');
            } else {
                $page_data .= '<h2>' . $ml_error_message . '</h2>';
            }
        }

        $page_data .= Multilang_AdminLangs();
    } else if ($params['opt'] == 3) {
        $page_data .= AdminPluginConfig('Multilang');
    } else {
        do_action('ADM_MULTILANG_OPT', $params);
    }

    return $page_data;
}

/**
 * Admin langs
 * 
 * @global TPL $tpl
 * @global Multilang $ml
 * @return string
 */
function Multilang_AdminLangs() {
    global $tpl, $ml;
    $content = '';

    $langs = $ml->getSiteLangs(0);
    if(empty($langs)) {
        return false;
    }
    $counter = 1;
    $count = count($langs);
    foreach ($langs as $lang) {
        $lang['TPL_CTRL'] = $counter;
        ($counter == $count) ? $lang['TPL_FOOT'] = 1 : $lang['TPL_FOOT'] = 0;
        $content .= $tpl->getTplFile('Multilang', 'ml_admin_lang_mng', $lang);
        $counter++;
    }

    return $content;
}

/**
 * Modify lang
 * 
 * @global Database $db
 * @global array $LNG
 * @global SecureFilter $filter
 * @return string|boolean
 */
function Multilang_ModifyLang() {
    global $db, $LNG, $filter;

    $error = false;

    $lang_id = $filter->postInt('lang_id');
    $lang_name = $filter->postUtf8Txt('lang_name', 255, 1);
    $iso_code = $filter->postAZChar('iso_code', 2, 2);
    $active = $filter->postInt('active', 1, 1);
    empty($active) ? $active = 0 : null;

    $modify_ary = [];
    $modify_ary['active'] = $active;

    if ($lang_name != false && $iso_code != false && $lang_id != false) {
        $query2 = $db->selectAll('lang', ['lang_id' => $lang_id], 'LIMIT 1');
        if ($db->numRows($query2) > 0) {
            $lang_data = $db->fetch($query2);
            if ($lang_data['lang_name'] != $lang_name) {
                $query3 = $db->selectAll('lang', ['lang_name' => $lang_name], 'LIMIT 1');
                if ($db->numRows($query3) > 0) {
                    return $LNG['L_ML_E_FIELDS_EXISTS'];
                } else {
                    $modify_ary['lang_name'] = $lang_name;
                }
            }
            if ($lang_data['iso_code'] != $iso_code) {
                $query3 = $db->selectAll('lang', ['iso_code' => $iso_code], 'LIMIT 1');
                if ($db->numRows($query3) > 0) {
                    return $LNG['L_ML_E_FIELDS_EXISTS'];
                } else {
                    $modify_ary['iso_code'] = $iso_code;
                }
            }
            $db->update('lang', $modify_ary, ['lang_id' => $lang_id]);
        } else {
            $error = $LNG['L_ML_E_INTERNAL_ID'];
        }
    } else {
        $error = $LNG['L_ML_E_FIELDS'];
    }
    return $error;
}

/**
 * Create lang
 * 
 * @global Database $db
 * @global array $LNG
 * @global SecureFilter $filter
 * @return string|boolean
 */
function Multilang_CreateLang() {
    global $db, $LNG, $filter;

    $error = false;
    $lang_name = $filter->postUtf8Txt('lang_name', 11, 2);
    $iso_code = $filter->postAZChar('iso_code', 2, 2);
    $active = $filter->postInt('active', 1, 1);
    empty($active) ? $active = 0 : null;

    if ($lang_name != false && $iso_code != false) {

        //Lang/ISo collation its utf8_general_ci (case insensitve), anyway we use LIKE operator instead '=' 
        $where_ary = [
            'lang_name' => ['value' => $lang_name, 'operator' => 'LIKE'],
            'iso_code' => ['value' => $iso_code, 'operator' => 'LIKE']
        ];
        $query = $db->selectAll('lang', $where_ary, 'LIMIT 1', 'OR');

        if ($db->numRows($query) == 0) {
            $db->insert('lang', ['lang_name' => $lang_name, 'active' => $active, 'iso_code' => $iso_code]);
        } else {
            $error = $LNG['L_ML_E_FIELDS_EXISTS'];
        }
    } else {
        $error = $LNG['L_ML_E_FIELDS'];
    }
    return $error;
}

/**
 * Delete lang
 * 
 * @global Database $db
 * @global array $LNG
 * @global SecureFilter $filter
 * @return string|boolean
 */
function Multilang_DeleteLang() {
    global $db, $LNG, $filter;

    $error = false;
    $lid = $filter->postInt('lang_id');
    if ($lid != false) {
        $db->delete('lang', ['lang_id' => $lid]);
    } else {
        $error = $LNG['L_ML_E_INTERNAL_ID'];
    }
    return $error;
}
