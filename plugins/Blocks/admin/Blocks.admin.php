<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

function Blocks_AdminInit() {
    global $plugins;

    $plugins->express_start('Blocks') ? register_action('add_admin_menu', 'Blocks_AdminMenu', '5') : null;
}

function Blocks_AdminMenu($params) {
    global $plugins, $LNG;

    $tab_num = $plugins->getPluginID('Blocks');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'Blocks_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'Blocks_admin_content', $params);

        return "<li class='tab_active'><a href='{$params['url']}&admtab=$tab_num'>{$LNG['L_BLK']}</a></li>";
    } else {
        return "<li><a href='{$params['url']}&admtab=$tab_num'>{$LNG['L_BLK']}</a></li>";
    }
}

function Blocks_AdminAside($params) {
    global $LNG;

    return "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LNG['L_PL_STATE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LNG['L_BLK_MANAGE'] . "</a></li>\n" .
            "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=4'>" . $LNG['L_PL_CONFIG'] . "</a></li>\n";
}

function Blocks_admin_content($params) {
    global $LNG;
    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState("Blocks");
    } else if ($params['opt'] == 2) {
        $page_data = '<h1>' . $LNG['L_BLK_MANAGE'] . '</h1>';
        $page_data .= Blocks_blk_mng();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('Blocks');
    }
    return $page_data;
}

function Blocks_blk_mng() {
    global $blocks, $tpl, $filter, $LNG;

    if (defined('MULTILANG')) {
        global $ml;
    }

    $block_config_data = [];
    $content = '';
    $selected_page = $filter->post_AlphaNum('block_page', 255, 1);
    $selected_blockname = $filter->post_strict_chars('blockname', 255, 1);
    $selected_section = $filter->post_int('block_section', 255, 1);
    $page_data['page_options'] = '';
    $page_data['sections'] = '';
    $page_data['reg_blocks'] = '';

    //Edit Block
    if (isset($_POST['btnEditBlock'])) {
        Blocks_showEditBlock($block_config_data);
    }
    //Submit Edit Block
    if (isset($_POST['btnSubmitEditBlock'])) { //Click Submit edit
        $block_config_data = $blocks->blockConfig($selected_blockname);
        Blocks_updateBlock();
    }

    //Delete New Block
    (isset($_POST['btnDelBlock'])) ? Blocks_delBlock() : null;

    //Display config new block
    if (!empty($selected_blockname) && !isset($_POST['btnEditBlock']) && !isset($_POST['btnDelBlock'])) {
        $block_config_data = $blocks->blockConfig($selected_blockname);
        $block_config_data['content'] .= '<br/><input type="submit" name="btnNewBlock" value="' . $LNG['L_CREATE'] . '"/>';
    }

    //Add New block
    if (isset($_POST['btnNewBlock']) && !empty($block_config_data['config'])) {
        $block_added = Blocks_addBlock($block_config_data['config']);
    } else {
        $block_added = false;
    }

    //Display Create New Block
    $pages = $blocks->getPages();

    (empty($selected_page) || $selected_page == false) ? $selected = 1 : $selected = 0;

    foreach ($pages as $page) {
        ($selected_page == $page['page_name']) ? $selected = 1 : null;

        if ($selected == 1) {
            $page_data['page_options'] .= "<option selected value='{$page['page_name']}'>{$page['page_name']}</option>";
            $page_selected_sections = $page['page_sections'];
            $selected = 0;
        } else {
            $page_data['page_options'] .= "<option value='{$page['page_name']}'>{$page['page_name']}</option>";
        }
    }

    if (defined('MULTILANG')) {
        $page_data['block_lang'] = $ml->get_sitelangs_select('block_lang', 1);
    }

    for ($i = 1; $i <= $page_selected_sections; $i++) {
        if (!empty($selected_section) && $selected_section == $i) {
            $page_data['sections'] .= "<option selected value='$i'>$i</option>";
        } else {
            $page_data['sections'] .= "<option value='$i'>$i</option>";
        }
    }

    $reg_blocks = $blocks->getRegisteredBlocks();

    $page_data['reg_blocks'] .= '<option value="">None</option>';
    $page_data['block_desc'] = '';

    foreach ($reg_blocks as $reg_block) {
        if (!empty($selected_blockname) && $selected_blockname == $reg_block['blockname'] && $block_added == false) {
            $page_data['reg_blocks'] .= "<option selected value='{$reg_block['blockname']}'>{$reg_block['blockname']}</option>";
            $page_data['block_desc'] = $reg_block['block_desc'];
        } else {
            $page_data['reg_blocks'] .= "<option value='{$reg_block['blockname']}'>{$reg_block['blockname']}</option>";
        }
    }
    !empty($block_config_data['content']) && !$block_added ? $page_data['block_config_request'] = $block_config_data['content'] : null;

    /* DELETE BLOCKS */

    $admin_blocks = $blocks->getAdminBlocks();

    $counter = 1;
    $num_items = count($admin_blocks);

    if (!$admin_blocks) { //NO ADMIN _BLOCKS
        $page_data['TPL_CTRL'] = $counter;
        $page_data['TPL_FOOT'] = 1;
        $page_data['blocks_notempty'] = 0;
        $content .= $tpl->getTplFile('Blocks', 'admin_blocks', $page_data);
        return $content;
    } else {
        $page_data['blocks_notempty'] = 1;
    }

    // DISPLAY CREATE BLOCK AND ACTUAL BLOCKS
    foreach ($admin_blocks as $admin_block) {
        $page_data['TPL_CTRL'] = $counter;
        $counter == $num_items ? $page_data['TPL_FOOT'] = 1 : $page_data['TPL_FOOT'] = 0;
        $page_data['page'] = $admin_block['page'];

        if (defined('MULTILANG') && $admin_block['lang'] != 0) {
            $page_data['lang'] = $ml->id_to_iso($admin_block['lang']);
        } else {
            $page_data['lang'] = $LNG['L_ML_ALL'];
        }

        $page_data['block'] = $admin_block['blockname'];
        $page_data['weight'] = $admin_block['weight'];
        $page_data['section'] = $admin_block['section'];
        $page_data['canUserDisable'] = $admin_block['canUserDisable'];
        $page_data['block_id'] = $admin_block['blocks_id'];

        $content .= $tpl->getTplFile('Blocks', 'admin_blocks', $page_data);
        $counter++;
    }

    return $content;
}

function Blocks_showEditBlock(& $blk_cfg_data) {
    global $filter, $blocks, $LNG;

    $editblock_id = $filter->post_int('block_id');

    if (empty($editblock_id)) {
        return;
    }
    $blk_cfg_data = $blocks->blockEditConfig($editblock_id);

    $blk_cfg_data['content'] .= '<input type="hidden" name="block_id" value="' . $editblock_id . '"/>';
    $blk_cfg_data['content'] .= '<br/><input type="submit" name="btnSubmitEditBlock" value="' . $LNG['L_SEND'] . '"/>';
}

function Blocks_delBlock() {
    global $filter, $blocks;

    $block_id = $filter->post_int('block_id');
    if ($block_id != false) {
        $blocks->deleteBlock($block_id);
    }
}

function Blocks_addBlock($config_array) {
    global $filter, $blocks, $db;

    /* WARNING: BLOCK PROVIDED MUST FILTER HIS CONFIG ARRAY */

    $block_page = $filter->post_AlphaNum('block_page', 255, 1);
    $block_section = $filter->post_AlphaNum('block_section', 255, 1);
    $blockname = $filter->post_strict_chars('blockname', 255, 1);
    $block_lang = $filter->post_int('block_lang', 127, 1);
    $block_weight = $filter->post_int('block_weight', 127, 1);
    $canUserDisable = $filter->post_AlphaNum('disable_by_user', 1, 1);

    !empty($canUserDisable) ? $canUserDisable = 0 : $canUserDisable = 1;

    if (!$block_page || !$block_section || !$blockname || !$block_weight || ( count($config_array) <= 0)) {
        !empty($blocks->debug) ? $blocks->debug->log('Add block failed', 'Blocks', 'WARNING') : null;
        return false;
    }

    $admin_block = $config_array['admin_block'];
    unset($config_array['admin_block']);

    $insert_ary = [
        'uid' => 0,
        'page' => $block_page,
        'lang' => $block_lang,
        'section' => $block_section,
        'admin_block' => $admin_block,
        'blockname' => $blockname,
        'plugin' => 'Blocks',
        'blockconf' => $db->escape(serialize($config_array)),
        'weight' => $block_weight,
        'canUserDisable' => $canUserDisable
    ];

    $ret = $db->insert('blocks', $insert_ary);

    return $ret ? true : false;
}

function Blocks_updateBlock() {
    global $filter, $db;
    $editblock_id = $filter->post_int('block_id');
    $block_conf = $filter->post_array('block_conf', 60000, 1);
    $upd_ary = [
        'blockconf' => $db->escape(serialize($block_conf))
    ];

    $ret = $db->update('blocks', $upd_ary, ['blocks_id' => $editblock_id]);
    return $ret ? true : false;
}
