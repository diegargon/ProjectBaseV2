<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
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
        $this->registerBlock('block_html_restricted', $LNG['L_BLK_HTMLRESTRIC_DESC'], [$this, 'blockHtml'], [$this, 'blockHtmlConfRestricted'], null, 0);
        $this->registerBlock('block_html', $LNG['L_BLK_HTML_DESC'], [$this, 'blockHtml'], [$this, 'blockHtmlConf'], null, 1);
        $this->registerBlock('block_html_file', $LNG['L_BLK_HTMLFILE_DESC'], [$this, 'blockHtmlFile'], [$this, 'blockHtmlFileConf'], null, 1);
        $this->registerBlock('block_php_file', $LNG['L_BLK_PHPFILE_DESC'], [$this, 'blockPhpFile'], [$this, 'blockPhpFileConf'], null, 1);
    }

    function registerBlock($block_name, $block_desc, $func_show, $func_conf, $def_conf, $admin_block) {
        $this->registered_blocks[] = [
            'blockname' => $block_name,
            'block_desc' => $block_desc,
            'func_show' => $func_show,
            'func_conf' => $func_conf,
            'def_conf' => $def_conf,
            'admin_block' => $admin_block,
        ];
    }

    function registerBlocksPage($page_name, $page_sections) {
        isset($this->debug) ? $this->debug->log('Registered blocks page, ' . $page_name . ' sections ' . $page_sections, 'Blocks', 'INFO') : null;
        $this->pages [] = [
            'page_name' => $page_name,
            'page_sections' => $page_sections
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

        $q = $db->select_all('blocks');
        $admin_blocks = $db->fetch_all($q);

        return (count($admin_blocks) > 0) ? $admin_blocks : false;
    }

    function setBlocks($page) {
        global $db;

        $users_id = '0';
        if (defined('SESSIONS')) {
            global $sm;
            $user = $sm->getSessionUSer();
            if (!empty($user)) {
                $users_id .= ',' . $user['uid'];
            }
        }
        $where_ary['uid'] = ['value' => "({$users_id})", 'operator' => 'IN'];

        if (defined('MULTILANG')) {
            global $ml;
            $langs = '0,' . $ml->getWebLangID();
            $where_ary['lang'] = ['value' => "({$langs})", 'operator' => 'IN'];
        }

        $q = $db->select_all('blocks', $where_ary, 'ORDER BY section,weight', 'AND');

        $cfg['user_can_disable_dflt_blocks'] = 1; //TO CFG
        $user_cfg['user_disable_dflt_blocks'] = 0; //TO USER CFG

        while ($result = $db->fetch($q)) {
            if ($cfg['user_can_disable_dflt_blocks'] && $user_cfg['user_disable_dflt_blocks'] &&
                    $result['canUserDisable'] && $result['uid'] == 0) {
                //NOTHING
            } else {
                $this->user_blocks[] = $result;
            }
        }
    }

    /* Gets content by column */

    function getBlocksContent($page, $sections) {

        !isset($this->user_blocks) ? $this->setBlocks($page) : null;
        if (empty($this->user_blocks)) {
            return false;
        }
        $page_data = [];

        for ($i = 1; $i <= $sections; $i++) {
            $content = '';
            $page_data['section_' . $i] = '';
            foreach ($this->user_blocks as $user_block) {
                foreach ($this->registered_blocks as $reg_block) {
                    if (($user_block['blockname'] == $reg_block['blockname']) && ($user_block['section'] == $i) && $user_block['page'] == $page) {
                        if (is_array($reg_block['func_show']) && method_exists($reg_block['func_show'][0], $reg_block['func_show'][1]) ||
                                (!is_array($reg_block['func_show']) && function_exists($reg_block['func_show']))) {
                            $block_conf = unserialize($user_block['blockconf']);
                            $content .= call_user_func($reg_block['func_show'], $block_conf);
                        }
                    }
                }
            }
            $page_data['section_' . $i] = $content;
        }
        return $page_data;
    }

    function blockConfig($blockname, $block_data = null) {
        foreach ($this->registered_blocks as $reg_block) {
            if ($reg_block['blockname'] == $blockname) {
                return call_user_func($reg_block['func_conf'], $block_data);
            }
        }
        return false;
    }

    function blockEditConfig($block_id) {
        global $db;

        $query = $db->select('blocks', 'blocks_id, uid, blockname, blockconf', ['blocks_id' => $block_id], 'LIMIT 1');
        if ($db->num_rows($query) > 0) {
            $block_data = $db->fetch($query);
            $block_conf = $this->blockConfig($block_data['blockname'], $block_data);
            return $block_conf;
        }
        return null;
    }

    function deleteBlock($block_id) {
        global $db;

        $db->delete('blocks', ['blocks_id' => $block_id], 'LIMIT 1');
    }

    /* BASIC BLOCKS PROVIDED */

    public function blockHtml($conf) {

        return nl2br($conf['html_code']);
    }

    public function blockHtmlConf($block_data = null) {
        global $filter;

        $content['content'] = '<textarea name="block_conf[html_code]" maxlength="60000" rows="10" cols="100">';

        if (!empty($block_data)) { //Edit block             
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] .= $_blockconf['html_code'];
        } else { //New block
            $block_conf = $filter->post_array('block_conf', 60000, 1);
            $block_conf['admin_block'] = 0;
            //TODO CHECK AND FILTER return false if something fail.
            $content['config'] = $block_conf;

            //field config its 65535, reserved 5545 for array serialice payload 

            if (isset($content['config']['html_code'])) {
                $content['content'] .= $content['config']['html_code'];
            }
        }
        $content['content'] .= '</textarea>';
        return $content;
    }

    public function blockHtmlConfRestricted($block_data = null) {
        global $filter;

        $content['content'] = '<textarea name="block_conf[html_code]" maxlength="60000" rows="10" cols="100">';

        if (!empty($block_data)) { //Edit block 
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] .= $_blockconf['html_code'];
        } else {
            $block_conf = $filter->post_array('block_conf', 60000, 1);
            $block_conf['admin_block'] = 0;
            //TODO CHECK AND FILTER return false if something fail.
            $content['config'] = $block_conf;
            //field config its 65535, reserved 5545 for array serialice payload 
            if (isset($content['config']['html_code'])) {
                $content['content'] .= $content['config']['html_code'];
            }
        }
        $content['content'] .= '</textarea>';

        return $content;
    }

    public function blockHtmlFile($conf) {
        global $cfg;

        $file = $cfg['CORE_PATH'] . $conf['file'];

        if (file_exists($file)) {
            $content = html_entity_decode(file_get_contents($file));
            return $content;
        }
        return false;
    }

    public function blockHtmlFileConf($block_data = null) {
        global $filter;
        if (!empty($block_data)) {
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[file]" value="' . $_blockconf['file'] . '"/>';
        } else {
            $block_conf = $filter->post_array('block_conf', 60000, 1);
            $block_conf['admin_block'] = 1;
            //TODO CHECK AND FILTER return false if something fail        

            $content['config'] = $block_conf;
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[file]"/>';
        }
        return $content;
    }

    public function blockPhpFile($conf) {
        global $tpl;
        //file without ext. "test" and look at /tpl/default/test.tpl.php
        return $tpl->getTplFile('Blocks', $conf['php_file']);
    }

    public function blockPhpFileConf($block_data = null) {
        global $filter;

        if (!empty($block_data)) {
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[php_file]" value="' . $_blockconf['php_file'] . '"/>';
        } else {
            $block_conf = $filter->post_array('block_conf', 60000, 1);
            $block_conf['admin_block'] = 1;
            //TODO CHECK AND FILTER return false if something fail        

            $content['config'] = $block_conf;
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[php_file]"/>';
        }
        return $content;
    }

    /* FIN BASIC BLOCKS */
}
