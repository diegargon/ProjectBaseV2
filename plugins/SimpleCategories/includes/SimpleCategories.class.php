<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 *  Actually, on start load all cats all plug
 * 
 *  not fully tested and going to have problems if we have 
 *  something like...
 *  /News/Sports/Football and /Other/Sports/Football  ( That going to fail, first match return)
 *  /News/Sports/Football and /News/Videos/Football (its ok)
 *  
 */
!defined('IN_WEB') ? exit : true;

class Categories {

    private $categories = [];

    public function __construct() {
        $this->loadCategories();
    }

    function debugCats() {
        print_r($this->categories);
    }

    function getCatbyName($plugin, $catname, $father = 0) {
        if (empty($plugin) || empty($catname)) {
            return false;
        }

        foreach ($this->categories as $key => $category) {
            if (array_search($plugin, $category)) {
                if (array_search($catname, $category)) {
                    if (!empty($father)) {
                        $f_id = $category['father'];
                        if ($this->categories[$f_id]['name'] == $father) {
                            return $this->categories[$key];
                        }
                    } else {
                        if (array_search($father, $category)) {
                            return $this->categories[$key];
                        }
                    }
                }
            }
        }
    }

    function getCatIDbyName($plugin, $catname, $father = 0) {
        if (empty($plugin) || empty($catname)) {
            return false;
        }
        $category = $this->getCatbyName($plugin, $catname, $father);

        return $category['cid'];
    }

    function getCatIDbyName_path($plugin, $cat_path) {
        global $cfg;

        if (empty($plugin) || empty($cat_path)) {
            return false;
        }

        $cat_path_ary = explode($cfg['categories_separator'], $cat_path);
        if (count($cat_path_ary) > 1) {
            $located_cat_name = end($cat_path_ary);
            $root_cat = array_shift($cat_path_ary);

            foreach ($this->root_cats($plugin) as $category) {
                if ($category['name'] == $root_cat) {
                    $root_cat = $category;
                    break;
                }
            }

            $next_cat = array_shift($cat_path_ary);
            $parent_id = $root_cat['cid'];
            while (true) {
                $cat_return = $this->searchCatnameParent($next_cat, $parent_id);
                if ($cat_return == false) {
                    return false;
                } else if ($located_cat_name == $cat_return['name']) {
                    return $cat_return['cid'];
                } else {
                    $next_cat = array_shift($cat_path_ary);
                    $parent_id = $cat_return['cid'];
                }
            }
        } else { //Its parent
            $catname = $cat_path_ary[0];
            foreach ($this->root_cats($plugin) as $category) {
                if ($category['name'] == $catname) {
                    return $category['cid'];
                }
            }
            return false;
        }

        return false;
    }

    private function searchCatnameParent($name, $parent_id) {
        foreach ($this->categories as $category) {
            if (($category['name'] == $name) && $category['father'] == $parent_id) {
                return $category;
            }
        }
        return false;
    }

    function getCatNameByID($catid) {
        foreach ($this->categories as $category) {
            if ($category['cid'] == $catid) {
                return $category['name'];
            }
        }
        return false;
    }

    function getCatChildsId($plugin, $cats, $separator = ',') {
        $cat_ids = '';

        if (empty($plugin) || empty($cats)) {
            return false;
        }
        $cats = ltrim($cats, $separator); //remove first ',' if we have(in loop)

        $cats_ids_ary = explode($separator, $cats);

        foreach ($cats_ids_ary as $cat_id) {
            foreach ($this->categories as $category) {
                if ($category['plugin'] == $plugin && $category['father'] == $cat_id) {
                    $cat_ids .= $separator . $category['cid'];
                }
            }
        }
        //loop
        !empty($cat_ids) ? $cat_ids .= $this->getCatChildsId($plugin, $cat_ids, $separator) : false;

        return $cat_ids;
    }

    function root_cats($plugin) { // get_fathers_cat_list
        global $ml;

        if (empty($plugin)) {
            return false;
        }
        $cat_data = [];

        defined('MULTILANG') ? $lang_id = $ml->get_web_lang_id() : $lang_id = 1;

        foreach ($this->categories as $category) {
            if (($category['plugin'] == $plugin && $category['father'] == 0) &&
                    ($category['lang_id'] == $lang_id)
            ) {
                $cat_data[$category['cid']] = $category;
            }
        }

        return $cat_data;
    }

    function getChilds($plugin, $cat_id) {
        global $ml;

        $cats = [];

        if (empty($plugin) || empty($cat_id) || empty($this->categories)) {
            return false;
        }

        defined('MULTILANG') ? $lang_id = $ml->get_web_lang_id() : $lang_id = 1;

        foreach ($this->categories as $category) {
            if ($category['plugin'] == $plugin && $category['father'] == $cat_id &&
                    $category['lang_id'] == $lang_id
            ) {
                $cats[] = $category;
            }
        }

        return !empty($cats) ? $cats : false;
    }

    function sortCatsByWeight() {
        usort($this->categories, function($a, $b) {
            return $a['weight'] - $b['weight'];
        });
    }

    function sortCatsByViews() {
        usort($this->categories, function($a, $b) {
            return $a['views'] - $b['views'];
        });
    }

    function getRootCatID($catid) {

        foreach ($this->categories as $category) {
            if ($category['cid'] == $catid) {
                $cat = $category;
                break;
            }
        }

        if (!isset($cat)) {
            return false;
        }

        if ($cat['father'] == 0) {
            return $cat['cid'];
        }
        $father = $cat['father'];

        while ($father != 0) {
            foreach ($this->categories as $category) {
                if ($category['cid'] == $father) {
                    $father = $category['father'];
                    ($father == 0) ? $father_id = $category['cid'] : null;
                }
            }
        }
        return isset($father_id) ? $father_id : false;
    }

    function getCategories_all_lang($plugin = null) {
        global $db;

        ($plugin) ? $plugin_name['plugin'] = $plugin : $plugin_name = null;

        $query = $db->select_all('categories', $plugin_name, 'ORDER BY father,weight');

        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $cats[] = $row;
            }
            return $cats;
        }
        return false;
    }

    function getCategories($plugin_name = null) {
        global $ml;

        if ($plugin_name == null) {
            return $this->categories;
        }

        defined('MULTILANG') ? $lang_id = $ml->get_web_lang_id() : $lang_id = 1;

        foreach ($this->categories as $category) {
            if (($category['plugin'] == $plugin_name) &&
                    ($category['lang_id'] == $lang_id)
            ) {
                $plugin_categories[$category['cid']] = $category;
            }
        }
        return !empty($plugin_categories) ? $plugin_categories : false;
    }

    private function loadCategories() {
        global $db;

        $query = $db->select_all('categories', null, 'ORDER BY father,cid, weight');

        $this->categories = $db->fetch_all($query);
        $db->free($query);
    }

}
