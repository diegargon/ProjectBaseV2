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
    private $display_section_menu;
    private $show_stats_query;
    private $layouts;

    public function __construct() {
        global $plugins;
        $plugins->express_start('Blocks');
        $this->setConfig();
    }

    function vPage($page) {
        if (empty($this->pages)) {
            $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return;
        }

        if ($page['type'] == 'virtual' && !empty($page['func'])) {
            if (is_array($page['func']) && method_exists($page['func'][0], $page['func'][1]) ||
                    (!is_array($page['func']) && function_exists($page['func']))) {
                call_user_func($page['func']);
                return true;
            } else {
                $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
                return false;
            }
        } else {
            $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return false;
        }
    }

    function getPage($request_module, $request_page) {
        //var_dump($this->pages);
        if (empty($this->pages)) {

            $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return;
        }

        foreach ($this->pages as $page) {
            if ($page['module'] == $request_module && $page['page'] == $request_page) {
                return $page;
            }
        }

        return false;
    }

    function registerPage($page) {
        // TODO avoid duplicates
        if (!empty($page['module']) && !empty($page['page']) &&
                ( ($page['type'] == 'virtual' && !empty($page['func'])) || ( $page['type'] == 'disk' && empty($page['func'])) )
        ) {
            $this->pages[] = $page;
            return true;
        }
        return false;
    }

    function registerPageArray($pages_array) {
        // TODO avoid duplicates
        foreach ($pages_array as $page) {
            if (!empty($page['module']) && !empty($page['page']) &&
                    ( ($page['type'] == 'virtual' && !empty($page['func'])) || ( $page['type'] == 'disk' && empty($page['func'])) )
            ) {
                $this->pages[] = $page;
            } else {
                return false;
            }
        }

        return true;
    }

    function getLayouts() {
        return $this->layouts;
    }

    function registerLayout($layout) {
        $this->layouts[] = $layout;
    }

    function indexPage() {
        global $tpl, $cfg;

        if (defined('BLOCKS')) {
            global $blocks;

            $page_data = $blocks->getBlocksContent('index', $cfg['index_sections']);
        }
        $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SimpleFrontend', $cfg['index_layout'] . '_layout', $page_data));
    }

    function sendPage() {
        global $tpl;

        // BEGIN HEAD        
        $tpl->cssCache();

        $web_head = $tpl->getTplFile('SimpleFrontend', 'head');

        echo $web_head;
        //END HEAD
        //BEGIN BODY
        if ($this->nav_menu) { //we use do_action for select order
            $tpl->addtoTplVar('HEADER_MENU_ELEMENT', do_action('header_menu_element'));
        }
        if ($this->display_section_menu) {
            $tpl->addtoTplVar('SECTIONS_NAV', do_action('section_nav_element'));
            $tpl->addtoTplVar('SECTIONS_NAV_SUBMENU', do_action('section_nav_subelement'));
        }

        $tpl->addtoTplVar('ADD_TO_BODY', do_action('add_to_body'));
        $web_body = $tpl->getTplFile('SimpleFrontend', 'body');

        echo $web_body;
        //END BODY
        //BEGIN FOOTER
        if (defined('SQL') && $this->db != null && $this->show_stats_query) {
            $tpl->addtoTplVar('ADD_TO_FOOTER', '<p class="db_querys">Querys(' . $this->db->num_querys() . ')</p>');
        }
        if ($this->show_load_time) {
            $tpl->addtoTplVar('ADD_TO_FOOTER', '<p class="page_render">Page render in (' . get_load_time($this->load_start_time) . ')</p>');
        }
        if ($this->show_memory_usage) {
            $memory_usage = '<p class="memory_usage">Memory usage: ' . formatBytes(memory_get_usage()) . ' / Memory peak: ';
            $memory_usage .= formatBytes(memory_get_peak_usage()) . '</p>';
            $memory_usage .= '<p class="memory_usage">Memory  real usage: ' . formatBytes(memory_get_usage(true)) . ' / Memory real peak: ';
            $memory_usage .= formatBytes(memory_get_peak_usage(true)) . '</p>';
            $tpl->addtoTplVar('ADD_TO_FOOTER', $memory_usage);
        }
        $tpl->addtoTplVar('ADD_TO_FOOTER', do_action('add_to_footer'));

        $web_footer = $tpl->getTplFile('SimpleFrontend', 'footer');
        //END FOOTER
        echo $web_footer;

        //print $web_head . $web_body . $web_footer;
    }

    function messageBox($box_data) {
        global $tpl, $LNG;

        !empty($box_data['title']) ? $data['box_title'] = $LNG[$box_data['title']] : $data['box_title'] = $LNG['L_E_ERROR'];
        !empty($box_data['backlink']) ? $data['box_backlink'] = $box_data['backlink'] : $data['box_backlink'] = "/";
        !empty($box_data['backlink_title']) ? $data['box_backlink_title'] = $LNG[$box_data['backlink_title']] : $data['box_backlink_title'] = $LNG['L_BACK'];
        $data['box_msg'] = $LNG[$box_data['msg']];
        !empty($box_data['xtra_box_msg']) ? $data['box_msg'] .= $box_data['xtra_box_msg'] : false;

        $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SimpleFrontend', 'msgbox', $data));
    }

    function setStartTime($start) {
        $this->load_start_time = $start;
    }

    private function setConfig() {
        global $cfg, $debug, $db, $tpl;

        $this->db = & $db;
        $this->display_section_menu = $cfg['display_section_menu'];
        $this->show_stats_query = $cfg['simplefrontend_stats_query'];
        $this->show_load_time = $cfg['show_load_time'];
        $this->show_memory_usage = $cfg['show_memory_usage'];


        if (defined('BLOCKS')) {
            global $blocks;

            $blocks->registerBlocksPage('index', $cfg['index_sections']);
            $blocks->registerBlocksPage('index2', 2); //test remove later
        }

        /* Default SimpleFrontend layouts */
        $this->registerLayout(['name' => 'Index 3 Colums', 'file' => 'index_3', 'sections' => 3]);
        $this->registerLayout(['name' => 'Index 2 Colums', 'file' => 'index_2', 'sections' => 2]);
        $this->registerLayout(['name' => 'Index 1 Colums', 'file' => 'index_1', 'sections' => 2]);

        if (defined('DEBUG') && $cfg['simplefrontend_debug']) {
            global $debug;
            $this->debug = & $debug;
        } else {
            $this->debug = false;
        }
        $this->nav_menu = $cfg['simplefrontend_nav_menu'];
        $this->theme = $cfg['simplefrontend_theme'];

        $custom_lang = 'tpl/lang/' . $cfg['WEB_LANG'] . '/custom.lang.php';
        file_exists($custom_lang) ? require_once($custom_lang) : false;

        $tpl->getCssFile('SimpleFrontend', 'basic');
        $tpl->getCssFile('SimpleFrontend', 'basic-mobile');
    }

}
