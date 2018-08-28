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

    function getCatIDbyName_path($plugin, $cat_path, $separator = ".") {
        if (empty($plugin) || empty($cat_path)) {
            return false;
        }
        $cat_path_ary = explode($separator, $cat_path);
        if (count($cat_path_ary) > 1) {
            $catname = array_pop($cat_path_ary);
            $catparent = array_pop($cat_path_ary);
            $cat_id = $this->getCatIDbyName($plugin, $catname, $catparent);
        } else {
            $catname = array_pop($cat_path_ary);
            $cat_id = $this->getCatIDbyName($plugin, $catname);
        }

        return $cat_id;
    }

    function getCatNameByID($catid) {
        foreach ($this->categories as $category) {
            if ($category['cid'] == $catid) {
                return $category['name'];
            }
        }
        return false;
    }

    function getCatChildsId($plugin, $cats, $separator = ",") {
        $cat_ids = "";

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
        !empty($cat_ids) ? $cat_ids .= $this->getCatChildsId($plugin, $cat_ids) : false;

        return $cat_ids;
    }

    function root_cats($plugin) { // get_fathers_cat_list
        if (empty($plugin)) {
            return false;
        }
        $cat_data = [];

        foreach ($this->categories as $category) {
            if ($category['plugin'] == $plugin && $category['father'] == 0) {
                $cat_data[$category['cid']] = $category;
            }
        }

        return $cat_data;
    }

    function childcats($plugin, $cat_path, $separator = ".") {
        $cats = [];

        if (empty($plugin) || empty($cat_path) || empty($this->categories)) {
            return false;
        }
        $cat_id = $this->getCatIDbyName_path($plugin, $cat_path);
        foreach ($this->categories as $category) {
            if ($category['plugin'] == $plugin && $category['father'] == $cat_id) {
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

    function getCategories($plugin = null, $order = null) {
        global $db;
        !$order ? $order = "cid" : null;

        ($plugin) ? $plugin["plugin"] = $plugin : null;

        $query = $db->select_all("categories", $plugin, "ORDER BY $order");

        while ($row = $db->fetch($query)) {
            $cats[] = $row;
        }

        return $cats;
    }

    private function loadCategories() {
        global $ml, $db;
        //Carga todas las categorias de todos los modulos de un solo lenguage

        (defined('MULTILANG') && !empty($ml)) ? $lang_id = $ml->getSessionLangID() : $lang_id = 1;
        $where_ary = [];
        !empty($lang_id) && is_numeric($lang_id) ? $where_ary['lang_id'] = $lang_id : null;

        $query = $db->select_all("categories", $where_ary, "ORDER BY weight");
        while ($c_row = $db->fetch($query)) {
            $this->categories[$c_row['cid']] = $c_row;
        }
    }

}
