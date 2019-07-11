<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
/**
 *  tplBasic - TPL class
 *  Template manager
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage tplBasic
 */
!defined('IN_WEB') ? exit : true;

/**
 * Class TPL - tplBasic
 */
class TPL {

    /**
     * Reference to $debug class
     * @var object 
     */
    private $debug;

    /**
     * Theme 
     * @var string 
     */
    private $theme;

    /**
     * Static content url
     * @var string 
     */
    private $static_url;

    /**
     *
     * @var int
     */
    private $css_optimize;

    /**
     *
     * @var int
     */
    private $gzip;

    /**
     *
     * @var int
     */
    private $css_inline;

    /**
     * UI lang
     * @var string
     */
    private $lang;

    /**
     * hold template data
     * @var string
     */
    private $tpldata;

    /**
     * CSS file array
     * @var array
     */
    private $css_added = [];

    /**
     * Hold css cache filepaths
     * @var array
     */
    private $css_cache_filepaths;

    /**
     * Holds one css file name
     * @var string
     */
    private $css_cache_onefile;

    /**
     * Hold list of script already called for use
     * @var array
     */
    private $scripts = [];

    /**
     * Hold list of css already called for use
     * @var array
     */
    private $css_std = [];

    /**
     * Hold list of domain list for create a html link to dns prefetch
     * @var type 
     */
    private $dns_prefetch = [];

    /**
     * Standard remote script
     * @var array
     */
    private $std_remote_scripts = [//TODO LOAD LIST
        'jquery' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js',
        'angularjs' => 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js',
        'dojo' => 'https://ajax.googleapis.com/ajax/libs/dojo/1.13.0/dojo/dojo.js',
        'ext-core' => 'https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js',
        'hammer' => 'https://ajax.googleapis.com/ajax/libs/hammerjs/2.0.8/hammer.min.js',
        'mootools' => 'https://ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js',
        'prototype' => 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.3.0/prototype.js',
        'scriptaculous' => 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js',
        'spf' => 'https://ajax.googleapis.com/ajax/libs/spf/2.4.0/spf.js',
        'swfobject' => 'https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js',
        'three' => 'https://cdnjs.cloudflare.com/ajax/libs/three.js/106/three.min.js',
        'webfont' => 'https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js',
    ];

    /**
     * Standard css files
     * @var array
     */
    private $std_remote_css = [
        'font-awesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/fontawesome.min.css',
        'bootstrap' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js',
        'octicons' => 'https://cdnjs.cloudflare.com/ajax/libs/octicons/4.4.0/font/octicons.min.css',
    ];

    /**
     * Constructor call set config
     */
    function __construct() {
        $this->setConfig();
    }

    /**
     * set initial config
     * 
     * @global debug $debug
     * @global array $cfg
     */
    function setConfig() {
        global $debug, $cfg;


        (defined('DEBUG') && $cfg['tplbasic_debug']) ? $this->debug = & $debug : $this->debug = false;

        $this->theme = $cfg['tplbasic_theme'];
        $this->static_url = $cfg['STATIC_SRV_URL'];
        $this->css_optimize = $cfg['tplbasic_css_optimize'];
        $this->gzip = $cfg['tplbasic_gzip'];
        $this->css_inline = $cfg['tplbasic_css_inline'];
        $this->lang = $cfg['WEB_LANG'];
    }

    /**
     * Append content to template var
     * 
     * @param string $tplvar
     * @param string $content
     * @param int $priority
     */
    function addtoTplVar($tplvar, $content, $priority = 5) { // change name to appendTo_tplvar? TODO priority support?
        !isset($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $content : $this->tpldata[$tplvar] .= $content;
    }

    /**
     * Replace content on template var
     * 
     * @param string $tplvar
     * @param string $content
     */
    function addtoTplVarUniq($tplvar, $content) {
        $this->tpldata[$tplvar] = $content;
    }

    /**
     * Add content if template var is empty
     * 
     * @param string $tplvar
     * @param string $content
     */
    function addIfEmpty($tplvar, $content) {
        empty($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $content : null;
    }

    /**
     * Return tpldata[name] content
     * 
     * @param string $value
     * @return string
     */
    function getTplValue($value) {
        return $this->tpldata[$value];
    }

    /**
     * Get tpldata
     * 
     * @return string
     */
    function getTplData() {
        return $this->tpldata;
    }

    /**
     * Get content and parse a template filename in plugin path
     * Search order:
     * User path lang: /tpl/theme/file.es.tpl.php
     * User path: /tpl/theme/file.tpl.php
     * Default: /plugins/plugin/tpl/filename.tpl.php
     * 
     * @param string $plugin
     * @param string $filename
     * @param mixed $data
     * @return boolean
     */
    function getTplFile($plugin, $filename = null, $data = null) {

        empty($filename) ? $filename = $plugin : null;

        $USER_PATH_LANG = "tpl/{$this->theme}/$filename.{$this->lang}.tpl.php";
        $USER_PATH = "tpl/{$this->theme}/$filename.tpl.php";
        $DEFAULT_PATH = "plugins/$plugin/tpl/$filename.tpl.php";
        if (file_exists($USER_PATH_LANG)) {
            $tpl_file_content = $this->parseFile($USER_PATH_LANG, $data);
        } else if (file_exists($USER_PATH)) {
            $tpl_file_content = $this->parseFile($USER_PATH, $data);
        } else if (file_exists($DEFAULT_PATH)) {
            $tpl_file_content = $this->parseFile($DEFAULT_PATH, $data);
        } else {
            $this->debug ? $this->debug->log('getTPL_file called but i can\'t find ' . $filename, 'tplBasic', 'WARNING') : null;
            return false;
        }

        return $tpl_file_content;
    }

    /**
     * Check if tpl file exists
     * 
     * @param string $plugin
     * @param string $filename
     * @return boolean
     */
    function checkTplFileExists($plugin, $filename) {
        if (empty($filename)) {
            return false;
        }

        $USER_PATH_LANG = "tpl/{$this->theme}/$filename.{$this->lang}.tpl.php";
        $USER_PATH = "tpl/{$this->theme}/$filename.tpl.php";
        $DEFAULT_PATH = "plugins/$plugin/tpl/$filename.tpl.php";
        if (file_exists($USER_PATH_LANG)) {
            return true;
        } else if (file_exists($USER_PATH)) {
            return true;
        } else if (file_exists($DEFAULT_PATH)) {
            return true;
        }
        return false;
    }

    /**
     * Add standard script to page
     * 
     * @param string $key
     * @param string $url
     * @return boolean
     */
    function addStdScript($key, $url) {
        if (array_key_exists($key, $this->std_remote_scripts)) {
            return false;
        }
        $this->std_remote_scripts[$key] = $url;
        return true;
    }

    /**
     * Check if script exists
     * 
     * @param string $script
     * @return boolean
     */
    private function checkScript($script) {
        foreach ($this->scripts as $value) {
            if ($value == $script) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add a script file
     * 
     * @param string $plugin
     * @param string $filename
     * @param string $place place on html TOP or BOTTOM
     * @param string $async default: async 
     * @return boolean
     */
    function addScriptFile($plugin, $filename = null, $place = 'TOP', $async = 'async') {

        $this->debug ? $this->debug->log('AddScriptFile request ->' . $plugin . 'for get a ' . $filename, 'tplBasic', 'DEBUG') : null;
        if ($this->checkScript($filename)) {
            $backtrace = debug_backtrace();
            $this->debug ? $this->debug->log("AddcriptFile:CheckScript found coincidence * $filename * called by " . $backtrace[1]['function'], 'tplBasic', 'DEBUG') : null;
            return false;
        } else {
            $this->scripts[] = $filename;
        }
        if (!empty($plugin) && ($plugin == 'standard')) {

            if (array_key_exists($filename, $this->std_remote_scripts)) {
                $script_url = $this->std_remote_scripts[$filename];
                $script = '<script src="' . $script_url . '" ' . $async . '></script>';
                $this->addtoTplVar('SCRIPTS_' . $place, $script);

                $backtrace = debug_backtrace();
                $url = parse_url($script_url);
                $this->setPrefetchURL($url['scheme'] . '://' . $url['host']);
                $this->debug ? $this->debug->log("AddcriptFile:CheckScript setting first time * $filename * by " . $backtrace[1]['function'], 'tplBasic', 'DEBUG') : null;
            } else {
                $backtrace = debug_backtrace();
                $this->debug ? $this->debug->log("AddcriptFile:CheckScript standard script * $filename * not found called by " . $backtrace[1]['function'], 'tplBasic', 'WARNING') : null;
            }

            return true;
        }

        empty($filename) ? $filename = $plugin : null;

        $USER_LANG_PATH = "tpl/{$this->theme}/js/$filename.{$this->lang}.js";
        $DEFAULT_LANG_PATH = "plugins/$plugin/js/$filename.{$this->lang}.js";
        $USER_PATH = "tpl/{$this->theme}/js/$filename.js";
        $DEFAULT_PATH = "plugins/$plugin/js/$filename.js";

        if (file_exists($USER_LANG_PATH)) { //TODO Recheck priority later
            $SCRIPT_PATH = $USER_LANG_PATH;
        } else if (file_exists($USER_PATH)) {
            $SCRIPT_PATH = $USER_PATH;
        } else if (file_exists($DEFAULT_LANG_PATH)) {
            $SCRIPT_PATH = $DEFAULT_LANG_PATH;
        } else if (file_exists($DEFAULT_PATH)) {
            $SCRIPT_PATH = $DEFAULT_PATH;
        }
        if (!empty($SCRIPT_PATH)) {
            $script = '<script src="' . $this->static_url . $SCRIPT_PATH . '" ' . $async . '></script>';
        } else {
            $this->debug ? $this->debug->log("AddScriptFile called by-> $plugin for get a $filename but NOT FOUND IT", 'tplBasic', 'ERROR') : null;
            return false;
        }
        $this->addtoTplVar('SCRIPTS_' . $place, $script);
    }

    function addStdCSS($css_name) {

        if (array_key_exists($css_name, $this->css_std)) {
            $this->debug ? $this->debug->log("Get CSS STD called for get $css_name but not exists", 'tplBasic', 'WARNING') : null;
            return false;
        }
        if (array_key_exists($css_name, $this->std_remote_css)) {
            $this->css_std[] = $css_name;
            $css = '<link rel="stylesheet" href="' . $this->std_remote_css['' . $css_name . ''] . ' ">';
            $this->addtoTplVar('LINK', $css);
            $parse_url = parse_url($this->std_remote_css[$css_name]);
            $this->setPrefetchURL($parse_url['scheme'] . '://' . $parse_url['host']);
            $this->debug ? $this->debug->log("Get CSS STD called for get $css_name sucessfull", 'tplBasic', 'DEBUG') : null;
            return true;
        } else {
            $this->debug ? $this->debug->log("Get CSS STD called for get $css_name but not exists", 'tplBasic', 'WARNING') : null;
            return false;
        }
    }

    /**
     * Add a css file
     * by conf, can be added the path, inline, or onefile cached mode 
     * added to LINK tpldata
     * 
     * @param string $plugin
     * @param string $filename
     * @return boolean
     */
    function getCssFile($plugin, $filename = null) {

        empty($filename) ? $filename = $plugin : null;
        if (in_array($filename, $this->css_added)) {
            return true;
        }
        $this->css_added[] = $filename;
        $this->debug ? $this->debug->log("Get CSS called by-> $plugin for get a $filename", 'tplBasic', 'DEBUG') : null;

        $USER_PATH = "tpl/{$this->theme}/css/$filename.css";
        $DEFAULT_PATH = "plugins/$plugin/tpl/css/$filename.css";
        if ($this->cssCacheCheck() == true) {
            if (file_exists($USER_PATH)) {
                $this->css_cache_filepaths[] = $USER_PATH;
            } else {
                $this->css_cache_filepaths[] = $DEFAULT_PATH;
            }
            if (empty($this->css_cache_onefile)) {
                $this->css_cache_onefile = $filename;
            } else {
                $this->css_cache_onefile .= '-' . $filename;
            }
        } else {
            /*
             * Inline mode: get content of the file to var later we dump the css inline
             * No Inline mode: build the path and use html tag link
             */
            if ($this->css_inline == 1) {
                if (file_exists($USER_PATH)) {
                    $css_code = $this->parseFile($USER_PATH);
                } else if (file_exists($DEFAULT_PATH)) {
                    $css_code = $this->parseFile($DEFAULT_PATH);
                }
                isset($css_code) ? $css = '<style>' . $this->cssStrip($css_code) . '</style>' : null;
            } else {
                if (file_exists($USER_PATH)) {
                    $css = '<link rel="stylesheet" href="/' . $USER_PATH . ' ">' . "\n";
                } else if (file_exists($DEFAULT_PATH)) {
                    $css = '<link rel="stylesheet" href="/' . $DEFAULT_PATH . ' ">' . "\n";
                }
            }
            if (isset($css)) {
                $this->addtoTplVar('LINK', $css);
            } else {
                $this->debug ? $this->debug->log("Get CSS called by-> $plugin for get a $filename NOT FOUND IT", 'tplBasic', 'DEBUG') : null;
            }
        }
    }

    /**
     * Check we can use css cache, by conf and by checking cache dir is writable
     * 
     * @return boolean
     */
    function cssCacheCheck() {
        if ($this->css_optimize == 0 || !is_writable('cache')) {
            return false;
        }

        if (!file_exists('cache/css')) {
            mkdir('cache/css', 0744, true);
        } else if (!is_writable('cache/css')) {
            return false;
        }
        return true;
    }

    /**
     * Build/Use css cache
     * 
     * @return boolean
     */
    function cssCache() {

        if (!$this->cssCacheCheck() || empty($this->css_cache_onefile)) {
            return false;
        }

        $css_code = '';

        $cssfile = $this->css_cache_onefile . '.css';
        $this->debug ? $this->debug->log('CSS One file Unify ' . $cssfile, 'tplBasic', 'DEBUG') : null;
        /*
         * Check if one file css exist in cache if not build it
         */
        if (!file_exists('cache/css/' . $cssfile)) {
            foreach ($this->css_cache_filepaths as $cssfile_path) {
                $this->debug ? $this->debug->log('CSS Unify ' . $cssfile_path, 'tplBasic', 'DEBUG') : null;
                $css_code .= $this->parseFile($cssfile_path);
            }
            $css_code = $this->cssStrip($css_code);
            file_put_contents('cache/css/' . $cssfile, $css_code);
        }
        /* CSS Inline or not */
        if ($this->css_inline == 0) {
            $this->addtoTplVar('LINK', '<link rel="stylesheet" href="/cache/css/' . $cssfile . '">');
        } else {
            $css_code = $this->parseFile('cache/css/' . $cssfile);
            $this->addtoTplVar('LINK', '<style>' . $css_code . '</style>');
        }

        return true;
    }

    /**
     * Strip/Compress css 
     * 
     * @param string $css
     * @return string
     */
    private function cssStrip($css) { #by nyctimus
        $preg_replace = [
            "#/\*.*?\*/#s" => '', // Strip C style comments.
            //"#\s\s+#" => '', // Strip excess whitespace.
            "/\s+/" => ' ' // Strip excess whitespace.
        ];
        $css = preg_replace(array_keys($preg_replace), $preg_replace, $css);
        $str_replace = [
            ': ' => ':',
            '; ' => ';',
            ' {' => '{',
            ' }' => '}',
            ', ' => ',',
            '{ ' => '{',
            ';}' => '}', // Strip optional semicolons.
            ',\n' => ',', // Don't wrap multiple selectors.
            '\n}' => '}', // Don't wrap closing braces.
        ];
        $css = str_replace(array_keys($str_replace), $str_replace, $css);

        return trim($css);
    }

    /**
     * Parse file
     * Load file template from disk and parse
     * 
     * @global array $LNG
     * @global array $cfg
     * @param string $path
     * @param mixed $data
     * @return string
     */
    private function parseFile($path, $data = null) {
        global $LNG, $cfg;

        $this->debug ? $this->debug->log("TPL parse $path, gzip its {$cfg['tplbasic_gzip']}", 'tplBasic', 'DEBUG') : null;

        $tpldata = $this->getTplData(); //used in include

        isset($this->gzip) && $this->gzip == 1 ? ob_start('ob_gzhandler') : ob_start();

        include ($path); // need $tpldata and $LNG
        $content = ob_get_contents();
        ob_end_clean();

        if ($cfg['tplbasic_html_optimize']) { //FIXME this give problems don't use... :)
            //TODO a regex that not give problems to remove html spaces
            $content = preg_replace('/(\>)\s+(\<)/S', '$1$2', $content); //spaces between > <            
        }
        return $content;
    }

    /**
     * Add domain to dns_prefetch
     * 
     * @param string $value
     */
    function setPrefetchURL($value) {
        $this->dns_prefetch[] = $value;
    }

    /**
     * Build dns_prefech <link> and add to tpldata LINK
     */
    function addPrefetchLinks() {
        foreach ($this->dns_prefetch as $dns_prefetch) {
            $this->addtoTplVar('LINK', '<link rel="dns-prefetch" href="' . $dns_prefetch . '">' . "\n");
        }
    }

    function deleteCache() {
        $files_cache_css = glob('cache/css/*');
        $files_cache = glob('cache/*');
        $files = array_merge($files_cache, $files_cache_css);
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file) && $file != 'cache/css/index.html' && $file != 'cache/index.html') {
                    unlink($file);
                }
            }
        }
    }

}
