<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
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

    function getCatIdByNamePath($plugin, $cat_path) {
        global $cfg;
        // thanks to mickmackusa @ StackOverflow

        $breadcrumbs = explode($cfg['categories_separator'], trim($cat_path, $cfg['categories_separator']));

        $parent = 0;
        foreach ($breadcrumbs as $crumb) {
            $id = false;
            foreach ($this->categories as $row) {
                if ($row['name'] == $crumb && $parent == $row['father'] && $row['plugin'] == $plugin) {
                    $id = $parent = $row['cid'];
                    break;
                }
            }
            if (!$id) {
                return false;
            }
        }
        return $id;
    }

    function getCatNameByID($catid) {
        foreach ($this->categories as $category) {
            if ($category['cid'] == $catid) {
                return $category['name'];
            }
        }
        return false;
    }

    function getCatURLByID($catid) {
        foreach ($this->categories as $category) {
            if ($category['cid'] == $catid) {
                return $category['image'];
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

    function getRootCats($plugin) {
        global $ml;

        if (empty($plugin)) {
            return false;
        }
        $cat_data = [];

        defined('MULTILANG') ? $lang_id = $ml->getWebLangID() : $lang_id = 1;

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

        defined('MULTILANG') ? $lang_id = $ml->getWebLangID() : $lang_id = 1;

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

    function getCatsAllLangs($plugin = null) {
        global $db;

        ($plugin) ? $plugin_name['plugin'] = $plugin : $plugin_name = null;

        $query = $db->selectAll('categories', $plugin_name, 'ORDER BY father,weight');

        if ($db->numRows($query) > 0) {
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

        defined('MULTILANG') ? $lang_id = $ml->getWebLangID() : $lang_id = 1;

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

        $query = $db->selectAll('categories', null, 'ORDER BY father,cid, weight');

        $this->categories = $db->fetchAll($query);
        $db->free($query);
    }

}
