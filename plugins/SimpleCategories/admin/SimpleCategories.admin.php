<?php

/**
 *  SimpleCategories main admin file 
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleCategories
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Admin init entry point
 * @global Plugins $plugins
 */
function SimpleCats_AdminInit() {
    global $plugins;

    $plugins->expressStart('SimpleCategories') ? register_action('add_admin_menu', 'SimpleCats_AdminMenu', 5) : null;
}

/**
 * Admin menu std
 * @global Plugins $plugins
 * @param array $params
 * @return string
 */
function SimpleCats_AdminMenu($params) {
    global $plugins;

    $tab_num = $plugins->getPluginID('SimpleCategories');
    if ($params['admtab'] == $tab_num) {
        register_uniq_action('admin_get_aside_menu', 'SimpleCats_AdminAside', $params);
        register_uniq_action('admin_get_section_content', 'SimpleCats_AdminContent', $params);
        return '<li class="tab_active"><a href="' . $params['url'] . '&admtab=' . $tab_num . '">SimpleCats</a></li>';
    } else {
        return '<li><a href="' . $params['url'] . '&admtab=' . $tab_num . '">SimpleCats</a></li>';
    }
}

/**
 * admin aside std
 * @global array $LNG
 * @param array $params
 * @return string
 */
function SimpleCats_AdminAside($params) {
    global $LNG;

    return '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=1">' . $LNG['L_PL_STATE'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=2">' . $LNG['L_CATS_CATS'] . '</a></li>' .
            '<li><a href="admin&admtab=' . $params['admtab'] . '&opt=4">' . $LNG['L_PL_CONFIG'] . '</a></li>';
}

/**
 * Show plugin/section content
 * 
 * @global array $LNG
 * @param array $params
 * @return string
 */
function SimpleCats_AdminContent($params) {
    global $LNG;

    //$tpl->getCssFile("SimpleCats");

    $page_data = '';

    if ($params['opt'] == 1 || $params['opt'] == false) {
        $page_data = '<h1>' . $LNG['L_GENERAL'] . ': ' . $LNG['L_PL_STATE'] . '</h1>';
        $page_data .= Admin_GetPluginState('SimpleCategories');
    } else if ($params['opt'] == 2) {
        $page_data .= SimpleCats_AdminCats();
    } else if ($params['opt'] == 4) {
        $page_data .= AdminPluginConfig('SimpleCategories');
    }

    return $page_data;
}

function SimpleCats_AdminCats($plugin = null) {
    global $cfg, $ml, $ctgs, $tpl;

    $catdata = [];
    $catdata['msg'] = '';

    $tpl->getCssFile('SimpleCategories');

    if (defined('MULTILANG')) {
        $langs = $ml->getSiteLangs();
    } else {
        $langs['lang_id'] = 1;
        $langs['lang_name'] = $cfg['WEB_LANG'];
    }

    isset($_POST['ModCatSubmit']) ? $catdata['msg'] = SimpleCats_ModCategories() : null;
    isset($_POST['NewCatSubmit']) ? SimpleCats_NewCategory($plugin) : null;
    isset($_POST['DelCatSubmit']) ? SimpleCats_DelCategory() : null;

    /* NEW CAT */
    $catdata['catrow_new'] = '';
    $catdata['catlist'] = '';

    foreach ($langs as $lang) {
        $catdata['catrow_new'] .= '<label>' . $lang['lang_name'] . '</label> <input type="text" name="' . $lang['lang_id'] . '" value="" />';
    }
    $content = $tpl->getTplFile('SimpleCategories', 'adm_create_cat', $catdata);

    /* MODIFY */
    $cats = $ctgs->getCatsAllLangs($plugin);

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
                        $catdata['catlist'] .= '<label>' . $lang['lang_name'] . '</label>';
                        $catdata['catlist'] .= '<input type="text" name="' . $lang['lang_id'] . '" class="cat_name" value="' . $cat['name'] . '" />';
                        $foundit = 1;
                        $catdata['catFather'] = $cat['father'];
                        $catdata['catWeight'] = $cat['weight'];
                        $catdata['plugin'] = $cat['plugin'];
                        !empty($cat['image']) ? $catdata['image'] = $cat['image'] : $catdata['image'] = '';
                    }
                }

                if ($foundit == 0) { //Not traslated
                    $catdata['catlist'] .= '<label>' . $lang['lang_name'] . '</label> <input type="text" name="' . $lang['lang_id'] . '" value="" />';
                }
                $foundit = 0;
            }

            $content .= $tpl->getTplFile('SimpleCategories', 'adm_modify_cat', $catdata);
            $catdata['catlist'] = '';
            $counter++;
        }
    }

    return $content;
}

/**
 * Called when modify button
 * 
 * TODO: This func works but sux. 
 * 
 * @global Multilang $ml
 * @global Database $db
 * @global SecureFilter $filter
 */
function SimpleCats_ModCategories() {
    global $ml, $db, $filter, $LNG;

    (defined('MULTILANG')) ? $langs = $ml->getSiteLangs() : $langs['lang_id'] = 1;

    foreach ($langs as $lang) {
        $msg = '';
        $lang_id = $lang['lang_id'];
        // post field name to get its numeric 1 or 2... depend of lang_id, we get GET['1'] for id lang 1
        $post_name = $filter->postAlphaUnderscoreUnicode($lang_id);

        if (!empty($post_name)) {
            $post_cid = $filter->postInt('cid');
            $post_father = $filter->postInt('father', 32767);
            $post_weight = $filter->postInt('weight', 128, 1);
            $post_image = $filter->postUrl('cat_image', 255, 1);

            !empty($_POST['cat_image']) && empty($post_image) ? $msg .= $LNG['L_CATS_IMAGE_NOVALID'] . '<br/>' : null;
            if ($post_cid != false) {
                $mod_cat_ary = ['name' => $post_name];
                !empty($post_father) ? $mod_cat_ary['father'] = $post_father : $mod_cat_ary['father'] = 0;
                !empty($post_weight) ? $mod_cat_ary['weight'] = $post_weight : $mod_cat_ary['weight'] = 0;
                !empty($post_image) ? $mod_cat_ary['image'] = $post_image : $mod_cat_ary['image'] = '';

                $db->update('categories', $mod_cat_ary, ['cid' => $post_cid, 'lang_id' => $lang_id], 'LIMIT 1');
                $msg .= $LNG['L_CATS_MOD_SUCCESS'] . '<br/>';
            } else {
                $msg .= $LNG['L_CATS_MODINT_ERROR'] . '<br/>';
            }
        }
    }
    return $msg;
}

function SimpleCats_NewCategory($plugin) {
    global $filter, $ml, $db;

    $new_cid = $db->getNextNum('categories', 'cid');

    (defined('MULTILANG')) ? $langs = $ml->getSiteLangs() : $langs['lang_id'] = 1;

    !$plugin ? $plugin = "General" : null;

    foreach ($langs as $lang) {
        $lang_id = $lang['lang_id'];
        $posted_name = $filter->postAlphaUnderscoreUnicode($lang_id); //POST['1'] 2... id return text value
        $posted_father = $filter->postInt('father', 32767);
        $posted_weight = $filter->postInt('weight', 127, 1);
        $posted_image = $filter->postUrl('cat_image', 255, 1);

        if (!empty($posted_name)) {
            $new_cat_ary = [
                'cid' => $new_cid,
                'lang_id' => $lang['lang_id'],
                'plugin' => $plugin,
                'name' => $posted_name,
                'father' => $posted_father
            ];
            !empty($posted_image) ? $new_cat_ary['image'] = $posted_image : null;
            empty($posted_weight) ? $new_cat_ary['weight'] = 0 : $new_cat_ary['weight'] = $posted_weight;

            $db->insert('categories', $new_cat_ary);
        }
    }
}

function SimpleCats_DelCategory() {
    global $filter, $db;

    if (( $posted_cid = $filter->postInt('cid'))) {
        $db->delete('categories', ['cid' => $posted_cid]);
    }
}
