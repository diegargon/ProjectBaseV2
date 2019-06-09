<?php

/**
 *  Blocks class file 
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Blocks
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */

/**
 * Blocks Class
 */
class Blocks {

    /**
     * List register blocks
     * 
     * @var array 
     */
    private $registered_blocks;

    /**
     * List user blocks
     * 
     * @var array 
     */
    private $user_blocks;

    /**
     * List pages
     * 
     * @var array
     */
    private $pages;

    /**
     * Debug reference
     * 
     * @var debug|false
     */
    public $debug;

    /**
     * _construct
     */
    public function __construct() {
        $this->setConfig();
    }

    /**
     * Set config
     * 
     * @global array $cfg
     * @global debug $debug
     * @global array $LNG
     */
    private function setConfig() {
        global $cfg, $debug, $LNG;

        defined('DEBUG') && $cfg['blocks_debug'] ? $this->debug = & $debug : $this->debug = false;

        //Default blocks
        $this->registerBlock('block_html_restricted', $LNG['L_BLK_HTMLRESTRIC_DESC'], [$this, 'blockHtml'], [$this, 'blockHtmlConfRestricted'], null, 0);
        $this->registerBlock('block_html', $LNG['L_BLK_HTML_DESC'], [$this, 'blockHtml'], [$this, 'blockHtmlConf'], null, 1);
        $this->registerBlock('block_html_file', $LNG['L_BLK_HTMLFILE_DESC'], [$this, 'blockHtmlFile'], [$this, 'blockHtmlFileConf'], null, 1);
        $this->registerBlock('block_php_file', $LNG['L_BLK_PHPFILE_DESC'], [$this, 'blockPhpFile'], [$this, 'blockPhpFileConf'], null, 1);
    }

    /**
     * Register block
     * 
     * @param string $block_name
     * @param string $block_desc
     * @param string $func_show
     * @param string $func_conf
     * @param string $def_conf
     * @param string $admin_block
     */
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

    /**
     * register blocks page
     * 
     * @param string $page_name
     * @param string $page_sections
     */
    function registerBlocksPage($page_name, $page_sections) {
        isset($this->debug) ? $this->debug->log('Registered blocks page, ' . $page_name . ' sections ' . $page_sections, 'Blocks', 'INFO') : null;
        $this->pages [] = [
            'page_name' => $page_name,
            'page_sections' => $page_sections
        ];
    }

    /**
     * get pages
     * 
     * @return array
     */
    function getPages() {
        return $this->pages;
    }

    /**
     * get registered blocks
     * 
     * @return array
     */
    function getRegisteredBlocks() {
        return $this->registered_blocks;
    }

    /**
     * get admin blocks
     * 
     * @global db $db
     * @return array|false
     */
    function getAdminBlocks() {
        global $db;

        $q = $db->selectAll('blocks');
        $admin_blocks = $db->fetchAll($q);

        return (count($admin_blocks) > 0) ? $admin_blocks : false;
    }

    /**
     * set a block in page
     * 
     * @global db $db
     * @global sm $sm
     * @global ml $ml
     * @param string $page
     */
    function setBlocks($page) {
        global $db;

        $users_id = '0';
        if (defined('SESSIONS')) {
            global $sm;
            $user = $sm->getSessionUSer();
            if (!empty($user) && $user['uid'] > 0) {
                $users_id .= ',' . $user['uid'];
            }
        }
        $where_ary['uid'] = ['value' => "({$users_id})", 'operator' => 'IN'];

        if (defined('MULTILANG')) {
            global $ml;
            $langs = '0,' . $ml->getWebLangID();
            $where_ary['lang'] = ['value' => "({$langs})", 'operator' => 'IN'];
        }

        $q = $db->selectAll('blocks', $where_ary, 'ORDER BY section,weight', 'AND');

        $cfg['user_can_disable_dflt_blocks'] = 1; //TODO: TO CFG
        $user_cfg['user_disable_dflt_blocks'] = 0; //TODO: TO USER CFG

        while ($result = $db->fetch($q)) {
            if ($cfg['user_can_disable_dflt_blocks'] && $user_cfg['user_disable_dflt_blocks'] &&
                    $result['canUserDisable'] && $result['uid'] == 0) {
                //NOTHING
            } else {
                $this->user_blocks[] = $result;
            }
        }
    }

    /**
     * get content by section/column
     * 
     * @param string $page
     * @param int $sections
     * @return boolean
     */
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

    /**
     * Block config
     * 
     * @param string $blockname
     * @param string $block_data
     * @return boolean
     */
    function blockConfig($blockname, $block_data = null) {
        foreach ($this->registered_blocks as $reg_block) {
            if ($reg_block['blockname'] == $blockname) {
                return call_user_func($reg_block['func_conf'], $block_data);
            }
        }
        return false;
    }

    /**
     * Blocks edit config
     * 
     * @global db $db
     * @param int $block_id
     * @return array|null
     */
    function blockEditConfig($block_id) {
        global $db;

        $query = $db->select('blocks', 'blocks_id, uid, blockname, blockconf', ['blocks_id' => $block_id], 'LIMIT 1');
        if ($db->numRows($query) > 0) {
            $block_data = $db->fetch($query);
            $block_conf = $this->blockConfig($block_data['blockname'], $block_data);
            return $block_conf;
        }
        return null;
    }

    /**
     * Delete block
     * 
     * @global db $db
     * @param int $block_id
     */
    function deleteBlock($block_id) {
        global $db;

        $db->delete('blocks', ['blocks_id' => $block_id], 'LIMIT 1');
    }

    /**
     * Block html
     * 
     * Basic block provide
     * 
     * @param arraye $conf
     * @return string
     */
    public function blockHtml($conf) {

        return nl2br($conf['html_code']);
    }

    /**
     * block html config
     * 
     * @global filter $filter
     * @param array $block_data
     * @return string
     */
    public function blockHtmlConf($block_data = null) {
        global $filter;

        $content['content'] = '<textarea name="block_conf[html_code]" maxlength="60000" rows="10" cols="100">';

        if (!empty($block_data)) { //Edit block             
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] .= $_blockconf['html_code'];
        } else { //New block
            $block_conf = $filter->postArray('block_conf', 60000, 1);
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

    /**
     * block htmlrestricted config
     * 
     * @global filter $filter
     * @param array $block_data
     * @return string
     */
    public function blockHtmlConfRestricted($block_data = null) {
        global $filter;

        $content['content'] = '<textarea name="block_conf[html_code]" maxlength="60000" rows="10" cols="100">';

        if (!empty($block_data)) { //Edit block 
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] .= $_blockconf['html_code'];
        } else {
            $block_conf = $filter->postArray('block_conf', 60000, 1);
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

    /**
     * Block html file
     * 
     * @global array $cfg
     * @param array $conf
     * @return boolean
     */
    public function blockHtmlFile($conf) {
        global $cfg;

        $file = $cfg['CORE_PATH'] . $conf['file'];

        if (file_exists($file)) {
            $content = html_entity_decode(file_get_contents($file));
            return $content;
        }
        return false;
    }

    /**
     * Block html file config
     * 
     * @global filter $filter
     * @param array $block_data
     * @return string
     */
    public function blockHtmlFileConf($block_data = null) {
        global $filter;
        if (!empty($block_data)) {
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[file]" value="' . $_blockconf['file'] . '"/>';
        } else {
            $block_conf = $filter->postArray('block_conf', 60000, 1);
            $block_conf['admin_block'] = 1;
            //TODO CHECK AND FILTER return false if something fail        

            $content['config'] = $block_conf;
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[file]"/>';
        }
        return $content;
    }

    /**
     * Block php file
     * 
     * @global tpl $tpl
     * @param array $conf
     * @return string
     */
    public function blockPhpFile($conf) {
        global $tpl;
        //file without ext. "test" and look at /tpl/default/test.tpl.php
        return $tpl->getTplFile('Blocks', $conf['php_file']);
    }

    /**
     * block php file conf
     * @global filter $filter
     * @param array $block_data
     * @return string
     */
    public function blockPhpFileConf($block_data = null) {
        global $filter;

        if (!empty($block_data)) {
            $block_conf = $block_data['blockconf'];
            $content['config'] = $block_conf;
            $_blockconf = unserialize($block_conf);
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[php_file]" value="' . $_blockconf['php_file'] . '"/>';
        } else {
            $block_conf = $filter->postArray('block_conf', 60000, 1);
            $block_conf['admin_block'] = 1;
            //TODO CHECK AND FILTER return false if something fail        

            $content['config'] = $block_conf;
            $content['content'] = '<br/><input type="text" maxlength="60000" size="100" name="block_conf[php_file]"/>';
        }
        return $content;
    }

    /* FIN BASIC BLOCKS */
}
