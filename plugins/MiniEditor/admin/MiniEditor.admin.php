<?php

/**
 *  MiniEditor admin main
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

function MiniEditor_AdminInit() {
    global $plugins;
    $plugins->expressStart("MiniEditor") ? register_action("add_admin_menu", "MiniEditor_AdminMenu", "5") : null;
}

function MiniEditor_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID("MiniEditor");
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_aside_menu", "MiniEditor_AdminAside", $params);
        register_uniq_action("admin_get_section_content", "MiniEditor_admin_content", $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>MiniEditor</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>MiniEditor</a></li>";
    }
}

function MiniEditor_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=2">' . $LNG['L_EDITOR_KEYLINKS'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
}

function MiniEditor_admin_content($params) {
    global $LNG;
    $page_data = "";

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = "<h1>" . $LNG['L_GENERAL'] . ": " . $LNG['L_PL_STATE'] . "</h1>";
        $page_data .= Admin_GetPluginState("MiniEditor");
    } else if ($params['opt'] == 2) {
        $catch_status = '';
        if (($_SERVER['REQUEST_METHOD'] === 'POST' && ( isset($_POST['btnSubmitKeyLink']) || isset($_POST['btnDeleteKeyLink'])))) {
            $catch_status = MiniEditor_KeyLinks_Catch();
        }
        $page_data .= MiniEditor_KeyLinks($catch_status);
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig("MiniEditor");
    }
    return $page_data;
}

function MiniEditor_KeyLinks($catch_status) {
    global $db, $tpl, $LNG;


    $keylink_result = $db->selectAll('links', ['plugin' => 'MiniEditor'], 'ORDER BY extra');
    if (!empty($catch_status)) {
        $content = '<h3>' . $catch_status . '</h3>';
    } else {
        $content = '';
    }
    $counter = 1;
    $num_items = $db->numRows($keylink_result);

    $keylink_row['title'] = $LNG['L_EDITOR_ADD_KEYLINKS'];
    $keylink_row['link_id'] = '';
    $keylink_row['link'] = '';
    $keylink_row['extra'] = '';
    $keylink_row['TPL_CTRL'] = '1';
    $keylink_row['TPL_FOOT'] = '1';
    $keylink_row['btn_caption'] = $LNG['L_EDITOR_ADD_KEYLINKS'];
    $content .= $tpl->getTplFile('MiniEditor', 'MiniEditorKeyLinks', $keylink_row);

    while ($keylink_row = $db->fetch($keylink_result)) {
        $keylink_row['TPL_CTRL'] = $counter;
        $keylink_row['btn_caption'] = $LNG['L_EDITOR_MOD_KEYLINKS'];
        $counter == 1 ? $keylink_row['title'] = $LNG['L_EDITOR_MOD_KEYLINKS'] : null;
        $counter == $num_items ? $keylink_row['TPL_FOOT'] = 1 : $keylink_row['TPL_FOOT'] = 0;
        $content .= $tpl->getTplFile('MiniEditor', 'MiniEditorKeyLinks', $keylink_row);
        $counter++;
    }

    return $content;
}

function MiniEditor_KeyLinks_Catch() {
    global $filter, $LNG, $db;
    $link_id = $filter->postInt('link_id');
    //not use postUrl because we want internal url like '/article.php' no only complete links
    $link_url = $filter->postUtf8Txt('link');
    $keyword = $filter->postUtf8Txt('keyword');

    if (empty($link_url) || empty($keyword)) {
        return $LNG['L_EDITOR_KEYLINKS_FIELD_NOEMPTY'];
    }
    if (empty($link_id) && isset($_POST['btnSubmitKeyLink'])) {
        //check if already exist
        $query = $db->selectall('links', ['plugin' => 'MiniEditor', 'extra' => $keyword], 'LIMIT 1');
        if ($db->numRows($query) > 0) {
            return $LNG['L_EDITOR_KEYLINKS_KEYWORD_EXIST'];
        }
        $insert_ary = [
            'plugin' => 'MiniEditor',
            'source_id' => '0',
            'type' => 'link',
            'extra' => $keyword,
            'link' => $link_url
        ];
        $db->insert('links', $insert_ary);
    } if (!empty($link_id) && isset($_POST['btnSubmitKeyLink'])) {
        //Check if already exist the keyword that we want change
        $query = $db->selectall('links', ['plugin' => 'MiniEditor', 'extra' => $keyword], 'LIMIT 1');
        if ($db->numRows($query) > 0) {
            while ($link_row = $db->fetch($query)) {
                if ($link_row['extra'] == $keyword && $link_row['link_id'] != $link_id) {
                    return $LNG['L_EDITOR_KEYLINKS_KEYWORD_EXIST'];
                }
            }
        }
        //all ok submit
        $db->update('links', ['extra' => $keyword, 'link' => $link_url], ['link_id' => $link_id], 'LIMIT 1');
    } else if (!empty($link_id) && isset($_POST['btnDeleteKeyLink'])) {
        $db->delete('links', ['link_id' => $link_id], 'LIMIT 1');
    }
}
