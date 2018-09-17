<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 */

class SimpleFrontend {

    private $db;
    private $cfg;
    private $nav_menu;
    private $theme;
    private $pages;

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
                return;
            } else {
                $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            }
        } else {
            $this->message_box(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
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

    function index_page() {
        global $tpl, $cfg, $blocks;
        $page_data = [];

        for ($i = 1; $i <= $cfg['index_sections']; $i++) {
            //$page_data["section_" . $i] = "<p>Hola $i</p>";
            $page_data["section_" . $i] = $blocks->get_blocks_content("index", $i);
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
        if ($this->cfg['simplefrontend_nav_menu']) { //we use do_action for select order
            $tpl->addto_tplvar("HEADER_MENU_ELEMENT", do_action("header_menu_element"));
        }

        $tpl->addto_tplvar("ADD_TO_BODY", do_action("add_to_body"));
        $web_body = $tpl->getTPL_file("SimpleFrontend", "body");

        echo $web_body;
        //END BODY
        //BEGIN FOOTER
        if (defined('SQL') && $this->db != null && $this->cfg['simplefrontend_stats_query']) {
            $tpl->addto_tplvar("ADD_TO_FOOTER", "<p class='center zero'>Querys(" . $this->db->num_querys() . ")</p>");
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

    private function setConfig() {
        global $cfg, $debug, $db, $tpl, $blocks;

        $this->cfg = & $cfg;
        $this->db = & $db;

        $blocks->register_page("index", $this->cfg['index_sections']);
        $blocks->register_page("index2", 2);

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
