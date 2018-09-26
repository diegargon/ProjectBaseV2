<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 */

class SimpleFrontend {

    private $db;
    private $nav_menu;
    private $theme;
    private $pages;
    private $show_load_time;
    private $load_start_time;
    private $index_sections;
    private $display_section_menu;
    private $show_stats_query;

    public function __construct() {
        $this->setConfig();
    }

    function vpage($page) {
        if (empty($this->pages)) {
            $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return;
        }

        if ($page['type'] == 'virtual' && !empty($page['func'])) {
            if (is_array($page['func']) && method_exists($page['func'][0], $page['func'][1]) ||
                    (!is_array($page['func']) && function_exists($page['func']))) {
                call_user_func($page['func']);
                return true;
            } else {
                $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
                return false;
            }
        } else {
            $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return false;
        }
    }

    function getPage($request_module, $request_page) {
        //var_dump($this->pages);
        if (empty($this->pages)) {

            $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return;
        }

        foreach ($this->pages as $page) {
            if ($page['module'] == $request_module && $page['page'] == $request_page) {
                return $page;
            }
        }

        return false;
    }

    function register_page($page) {
        // TODO avoid duplicates
        if (!empty($page['module']) && !empty($page['page']) &&
                ( ($page['type'] == "virtual" && !empty($page['func'])) || ( $page['type'] == "disk" && empty($page['func'])) )
        ) {
            $this->pages[] = $page;
            return true;
        }
        return false;
    }

    function register_page_array($pages_array) {
        // TODO avoid duplicates
        foreach ($pages_array as $page) {
            if (!empty($page['module']) && !empty($page['page']) &&
                    ( ($page['type'] == "virtual" && !empty($page['func'])) || ( $page['type'] == "disk" && empty($page['func'])) )
            ) {
                $this->pages[] = $page;
            } else {
                return false;
            }
        }

        return true;
    }

    function index_page() {
        global $tpl, $cfg, $blocks;
        $page_data = [];

        for ($i = 1; $i <= $cfg['index_sections']; $i++) {
            $page_data["section_" . $i] = "";
            $page_data["section_" . $i] .= $blocks->get_blocks_content("index", $i);
        }

        $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SimpleFrontend", $cfg['index_layout'] . "_layout", $page_data));
    }

    function send_page() {
        global $tpl;

        // BEGIN HEAD        
        $tpl->css_cache();

        $web_head = $tpl->getTPL_file("SimpleFrontend", "head");

        echo $web_head;
        //END HEAD
        //BEGIN BODY
        if ($this->nav_menu) { //we use do_action for select order
            $tpl->addto_tplvar("HEADER_MENU_ELEMENT", do_action("header_menu_element"));
        }
        if ($this->display_section_menu) {
            $tpl->addto_tplvar("SECTIONS_NAV", do_action("section_nav_element"));
            $tpl->addto_tplvar("SECTIONS_NAV_SUBMENU", do_action("section_nav_subelement"));
        }

        $tpl->addto_tplvar("ADD_TO_BODY", do_action("add_to_body"));
        $web_body = $tpl->getTPL_file("SimpleFrontend", "body");

        echo $web_body;
        //END BODY
        //BEGIN FOOTER
        if (defined('SQL') && $this->db != null && $this->show_stats_query) {
            $tpl->addto_tplvar("ADD_TO_FOOTER", "<p class='center zero'>Querys(" . $this->db->num_querys() . ")</p>");
        }
        if ($this->show_load_time) {
            $tpl->addto_tplvar("ADD_TO_FOOTER", "<p class='center zero'>Page render in (" . $this->load_start_time . ")</p>");
        }
        $tpl->addto_tplvar("ADD_TO_FOOTER", do_action("add_to_footer"));

        $web_footer = $tpl->getTPL_file("SimpleFrontend", "footer");
        //END FOOTER
        echo $web_footer;

        //print $web_head . $web_body . $web_footer;
    }

    function message_box($box_data) {
        global $tpl, $LNG;

        !empty($box_data['title']) ? $data['box_title'] = $LNG[$box_data['title']] : $data['box_title'] = $LNG['L_E_ERROR'];
        !empty($box_data['backlink']) ? $data['box_backlink'] = $box_data['backlink'] : $data['box_backlink'] = "/";
        !empty($box_data['backlink_title']) ? $data['box_backlink_title'] = $LNG[$box_data['backlink_title']] : $data['box_backlink_title'] = $LNG['L_BACK'];
        $data['box_msg'] = $LNG[$box_data['msg']];
        !empty($box_data['xtra_box_msg']) ? $data['box_msg'] .= $box_data['xtra_box_msg'] : false;

        $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SimpleFrontend", "msgbox", $data));
    }

    function setStartTime($start) {
        $this->load_start_time = $start;
    }

    private function setConfig() {
        global $cfg, $debug, $db, $tpl, $blocks;

        $this->db = & $db;
        $this->index_sections = $cfg['index_sections'];
        $this->display_section_menu = $cfg['display_section_menu'];
        $this->show_stats_query = $cfg['simplefrontend_stats_query'];
        $this->show_load_time = $cfg['show_load_time'];

        $blocks->register_page("index", $this->index_sections);
        $blocks->register_page("index2", 2); //test remove later

        (defined('DEBUG') && $cfg['simplefrontend_debug']) ? $this->debug = & $debug : $this->debug = false;
        global $debug;

        $this->nav_menu = $cfg['simplefrontend_nav_menu'];
        $this->theme = $cfg['simplefrontend_theme'];

        $custom_lang = "tpl/lang/" . $cfg['WEB_LANG'] . "/custom.lang.php";
        file_exists($custom_lang) ? require_once($custom_lang) : false;

        $tpl->getCSS_filePath("SimpleFrontend", "basic");
        $tpl->getCSS_filePath("SimpleFrontend", "basic-mobile");
    }

}
