<?php

/**
 *  SimpleFrontend class file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * SimpleFrontend class
 */
class SimpleFrontend {

    /**
     *
     * @var db 
     */
    private $db;

    /**
     *
     * @var string
     */
    private $nav_menu;

    /**
     *
     * @var string
     */
    private $theme;

    /**
     *
     * @var array
     */
    private $pages = [];

    /**
     *
     * @var int
     */
    private $show_load_time;

    /**
     *
     * @var float
     */
    private $load_start_time;

    /**
     *
     * @var int
     */
    private $display_section_menu;

    /**
     *
     * @var int
     */
    private $show_stats_query;

    /**
     *
     * @var array
     */
    private $layouts = [];

    /**
     * Kep menu element $name_menu, $code, $weight
     * @var type 
     */
    private $menu_elements = [];

    /**
     * Class contructor, iniacilice blocks and set the config
     * 
     * @global plugins $plugins
     */
    public function __construct() {
        global $plugins;
        $plugins->expressStart('Blocks');
        $this->setConfig();
    }

    /**
     * Provide access to a virtual page calling a function
     * 
     * @param array $page
     * @return boolean
     */
    function vPage($page) {
        if (empty($this->pages)) {
            $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return false;
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

    /**
     * Provide access to file disk page 
     * 
     * @param string $request_module
     * @param string $request_page
     * @return boolean
     */
    function getPage($request_module, $request_page) {
        if (empty($this->pages)) {
            $this->messageBox(['msg' => 'L_E_PLUGPAGE_NOEXISTS']);
            return false;
        }
        foreach ($this->pages as $page) {
            if ($page['module'] == $request_module && $page['page'] == $request_page) {
                return $page;
            }
        }

        return false;
    }

    /**
     * Register for be accesible a page virtual+func or disk file (module ambit) 
     * 
     * @param array $page
     * @return boolean
     */
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

    /**
     * Register a page array
     * 
     * @param array $pages_array
     * @return boolean
     */
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

    /**
     * get layouts
     * 
     * @return array
     */
    function getLayouts() {
        return $this->layouts;
    }

    /**
     * register layout
     * 
     * @param array $layout
     */
    function registerLayout($layout) {
        $this->layouts[] = $layout;
    }

    /**
     * Build the index page
     * 
     * @global tpl $tpl
     * @global array $cfg
     * @global blocks $blocks
     */
    function indexPage() {
        global $tpl, $cfg;

        if (defined('BLOCKS')) {
            global $blocks;

            $page_data = $blocks->getBlocksContent('index', $cfg['index_sections']);
            $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile($cfg['index_plugin_layout'], $cfg['index_layout'] . '_layout', $page_data));
        }
    }

    /**
     * Send page to client, last stage
     * 
     * @global tpl $tpl
     */
    function sendPage() {
        $this->sendHeaders();
        $this->sendBody();
        $this->sendFooter();

        return true;
    }

    function sendHeaders() {
        global $tpl;
        $tpl->addScriptFile('standard', 'jquery', 'TOP', 0);
        $tpl->addPrefetchLinks();
        $tpl->cssCache();

        $web_head = $tpl->getTplFile('SimpleFrontend', 'head');

        echo $web_head;
    }

    function sendBody() {
        global $tpl, $cfg;

        $body_data = [];

        if ($cfg['tplbasic_nav_menu']) {
            $menu_data['header_menu_elements_left'] = $this->getMenuItems('top_menu_left');
            $menu_data['header_menu_elements_right'] = $this->getMenuItems('top_menu_right');
            $menu_data['header_drop_menu_elements'] = $this->getMenuItems('dropdown_menu');
            $menu_data['drop_menu_caption'] = $this->getMenuItems('dropdown_menu_caption');
            $body_data['header_menu'] = $tpl->getTplFile('SimpleFrontend', 'header_menu', $menu_data);
        }

        if ($this->display_section_menu) {
            $sections_menu = $this->getMenuItems('sections_menu');
            ($sections_menu !== false) ? $sections_data['sections_menu'] = $sections_menu : null;
            $sections_sub_menu = $this->getMenuItems('sections_sub_menu');
            ($sections_sub_menu !== false) ? $sections_data['sections_sub_menu'] = $sections_sub_menu : null;

            if ($sections_menu !== false || $sections_sub_menu !== false) {
                $body_data['sections_menu'] = $tpl->getTplFile('SimpleFrontend', 'sections_menu', $sections_data);
            }
        }
        $tpl->addtoTplVar('ADD_TO_BODY', do_action('add_to_body'));
        $web_body = $tpl->getTplFile('SimpleFrontend', 'body', $body_data);

        echo $web_body;
    }

    function sendFooter() {
        global $tpl;

        if (defined('SQL') && $this->db != null && $this->show_stats_query) {
            $tpl->addtoTplVar('ADD_TO_FOOTER', '<p class="db_querys">Querys(' . $this->db->numQuerys() . ')</p>');
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
    }

    /**
     * Format a message box
     * 
     * @global tpl $tpl
     * @global array $LNG
     * @param array $box_data
     */
    function messageBox($box_data) {
        global $tpl, $LNG, $filter;

        !empty($box_data['title']) ? $data['box_title'] = $LNG[$box_data['title']] : $data['box_title'] = $LNG['L_E_ERROR'];
        !empty($box_data['backlink']) ? $data['box_backlink'] = $box_data['backlink'] : $data['box_backlink'] = $filter->srvReferer();
        !empty($box_data['backlink_title']) ? $data['box_backlink_title'] = $LNG[$box_data['backlink_title']] : $data['box_backlink_title'] = $LNG['L_BACK'];
        $data['box_msg'] = $LNG[$box_data['msg']];
        !empty($box_data['xtra_box_msg']) ? $data['box_msg'] .= $box_data['xtra_box_msg'] : null;

        $tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('SimpleFrontend', 'msgbox', $data));
    }

    function getMenuItems($menu_name) {
        $menu = false;

        if (count($this->menu_elements) < 1) {
            return false;
        }
        foreach ($this->menu_elements as $menu_item) {
            if ($menu_item['menu_name'] == $menu_name) {
                $this_menu[] = $menu_item;
            }
        }
        if (!isset($this_menu) || count($this_menu) < 1) {
            return false;
        }
        usort($this_menu, function($a, $b) {
            return $a['weight'] - $b['weight'];
        });

        foreach ($this_menu as $element) {
            $menu .= $element['code'];
        }

        return $menu;
    }

    function addMenuItem($menu_name, $code, $weight = 5) {
        if (empty($menu_name) || empty($code)) {
            return false;
        }
        $this->menu_elements[] = [
            'menu_name' => $menu_name,
            'code' => $code,
            'weight' => $weight
        ];
    }

    /**
     * Set start time
     * 
     * @param float $start
     */
    function setStartTime($start) {
        $this->load_start_time = $start;
    }

    /**
     * set initial config
     * 
     * @global array $cfg
     * @global debug $debug
     * @global db $db
     * @global tpl $tpl
     */
    private function setConfig() {
        global $cfg, $debug, $db, $tpl;

        $this->db = & $db;
        $this->display_section_menu = $cfg['display_section_menu'];
        $this->show_stats_query = $cfg['simplefrontend_stats_query'];
        $this->show_load_time = $cfg['show_load_time'];
        $this->show_memory_usage = $cfg['show_memory_usage'];

        if (defined('DEBUG') && $cfg['simplefrontend_debug']) {
            $this->debug = & $debug;
        } else {
            $this->debug = false;
        }
        $this->nav_menu = $cfg['simplefrontend_nav_menu'];
        $this->theme = $cfg['simplefrontend_theme'];

        $custom_lang = 'tpl/lang/' . $cfg['WEB_LANG'] . '/custom.lang.php';
        file_exists($custom_lang) ? require_once($custom_lang) : null;

        $tpl->getCssFile('SimpleFrontend', 'basic');
        $tpl->getCssFile('SimpleFrontend', 'basic-mobile');
    }

}
