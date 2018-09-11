<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

class Blocks {

    private $registered_blocks;
    private $user_blocks;
    private $pages;
    public $debug;

    public function __construct() {
        $this->setConfig();
    }

    private function setConfig() {
        global $cfg, $debug, $LNG;

        defined('DEBUG') && $cfg['blocks_debug'] ? $this->debug = & $debug : $this->debug = false;

        //Default blocks
        $this->register_block("block_html_restricted", $LNG['L_BLK_HTMLRESTRIC_DESC'], [$this, "block_html"], [$this, "block_html_conf_restricted"], null, 0);
        $this->register_block("block_html", $LNG['L_BLK_HTML_DESC'], [$this, "block_html"], [$this, "block_html_conf"], null, 1);
        $this->register_block("block_html_file", $LNG['L_BLK_HTMLFILE_DESC'], [$this, "block_html_file"], [$this, "block_html_file_conf"], null, 1);
        $this->register_block("block_php_file", $LNG['L_BLK_PHPFILE_DESC'], [$this, "block_php_file"], [$this, "block_php_file_conf"], null, 1);
    }

    function register_block($block_name, $block_desc, $func_show, $func_conf, $def_conf, $admin_block) {
        $this->registered_blocks[] = [
            "blockname" => $block_name,
            "block_desc" => $block_desc,
            "func_show" => $func_show,
            "func_conf" => $func_conf,
            "def_conf" => $def_conf,
            "admin_block" => $admin_block,
        ];
    }

    function register_page($page_name, $page_sections) {
        $this->pages [] = [
            "page_name" => $page_name,
            "page_sections" => $page_sections
        ];
    }

    function getPages() {
        return $this->pages;
    }

    function getRegisteredBlocks() {
        return $this->registered_blocks;
    }

    function getAdminBlocks() {
        global $db;

        $q = $db->select_all("blocks");
        $admin_blocks = $db->fetch_all($q);

        return (count($admin_blocks) > 0) ? $admin_blocks : false;
    }

    function set_user_blocks($page) {
        global $db, $sm;
        $user = $sm->getSessionUser();

        empty($user) ? $user['uid'] = -1 : null; //anon

        $cfg['user_can_disable_dflt_blocks'] = 0; //TO CFG
        $user_cfg['user_disable_dflt_blocks'] = 1; //TO USER CFG

        /*  uid = 0 mean default  */
        $q = $db->select_all("blocks", ["uid" => $user['uid'], "uid" => 0], "ORDER BY section,weight", "OR");
        while ($result = $db->fetch($q)) {
            if ($cfg['user_can_disable_dflt_blocks'] == 1 && $user_cfg['user_disable_dflt_blocks'] &&
                    $result['canUserDisable'] && $result['uid'] == 0) {
                
            } else {
                $this->user_blocks[] = $result;
            }
        }
    }

    function get_blocks_content($page, $section) {

        !isset($this->user_blocks) ? $this->set_user_blocks($page) : null;
        if (empty($this->user_blocks)) {
            return false;
        }

        $content = "";
        foreach ($this->user_blocks as $user_block) {
            foreach ($this->registered_blocks as $reg_block) {
                if (($user_block['blockname'] == $reg_block['blockname']) && ($user_block['section'] == $section) && $user_block['page'] == $page) {
                    if (is_array($reg_block['func_show']) && method_exists($reg_block['func_show'][0], $reg_block['func_show'][1]) ||
                            (!is_array($reg_block['func_show']) && function_exists($reg_block['func_show']))) {
                        $block_conf = unserialize($user_block['blockconf']);
                        $content .= call_user_func($reg_block['func_show'], $block_conf);
                    }
                }
            }
        }

        return $content;
    }

    function block_config($blockname) {
        foreach ($this->registered_blocks as $reg_block) {
            if ($reg_block['blockname'] == $blockname) {
                return call_user_func($reg_block['func_conf']);
            }
        }
        return false;
    }

    function deleteBlock($block_id) {
        global $db;

        $db->delete("blocks", ['blocks_id' => $block_id], "LIMIT 1");
    }

    public function block_html($conf) {

        return $conf['html_code'];
    }

    public function block_html_conf_restricted() {
        global $filter;

        $block_conf = $filter->post_array("block_conf");
        $block_conf['admin_block'] = 0;
        //TODO CHECK AND FILTER return false if something fail.
        $content['config'] = $block_conf;

        //field config its 256, reserved 56 for array serialice payload 
        $content['content'] = "<textarea name=\"block_conf[html_code]\" maxlength=\"200\" rows=\"3\" cols=\"75\">";
        if (isset($content['config']['html_code'])) {
            $content['content'] .= $content['config']['html_code'];
        }
        $content['content'] .= "</textarea>";

        return $content;
    }

    public function block_html_conf() {
        global $filter;

        $block_conf = $filter->post_array("block_conf");
        $block_conf['admin_block'] = 0;
        //TODO CHECK AND FILTER return false if something fail.
        $content['config'] = $block_conf;

        //field config its 256, reserved 56 for array serialice payload 
        $content['content'] = "<textarea name=\"block_conf[html_code]\" maxlength=\"200\" rows=\"3\" cols=\"75\">";
        if (isset($content['config']['html_code'])) {
            $content['content'] .= $content['config']['html_code'];
        }
        $content['content'] .= "</textarea>";

        return $content;
    }

    public function block_html_file($conf) {
        global $cfg;

        $file = $cfg['CORE_PATH'] . $conf['file'];

        if (file_exists($file)) {
            $content = html_entity_decode(file_get_contents($file));
            return $content;
        }
        return false;
    }

    public function block_html_file_conf() {
        global $filter;
        $block_conf = $filter->post_array("block_conf");
        $block_conf['admin_block'] = 1;
        //TODO CHECK AND FILTER return false if something fail        

        $content['config'] = $block_conf;
        $content['content'] = "<br/><input type='text' maxlength='256' size='100' name='block_conf[file]'/>";

        return $content;
    }

    public function block_php_file($conf) {
        global $tpl;

        return $tpl->getTPL_file("Blocks", $conf['php_file']);
    }

    public function block_php_file_conf() {
        global $filter;
        $block_conf = $filter->post_array("block_conf");
        $block_conf['admin_block'] = 1;
        //TODO CHECK AND FILTER return false if something fail        

        $content['config'] = $block_conf;
        $content['content'] = "<br/><input type='text' maxlength='256' size='100' name='block_conf[php_file]'/>";

        return $content;
    }

}
