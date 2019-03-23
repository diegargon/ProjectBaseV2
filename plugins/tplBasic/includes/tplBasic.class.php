<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 * 
 * 
 */
!defined('IN_WEB') ? exit : true;

class TPL {

    private $debug;
    private $theme;
    private $static_url;
    private $css_optimize;
    private $gzip;
    private $css_inline;
    private $db;
    private $lang;
    private $tpldata;
    private $css_added = [];
    private $css_cache_filepaths;
    public $css_cache_onefile;
    private $scripts = [];
    private $std_remote_scripts = [//TODO LOAD LIST
        'jquery' => 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js',
        'font-awesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        'bootstrap' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
        'angularjs' => 'https://code.angularjs.org/1.7.3/angular.min.js',
        'dojo' => 'https://ajax.googleapis.com/ajax/libs/dojo/1.13.0/dojo/dojo.js',
        'ext-core' => 'https://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js',
        'hammer' => 'https://ajax.googleapis.com/ajax/libs/hammerjs/2.0.8/hammer.min.js',
        'mootools' => 'https://ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js',
        'prototype' => 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.3.0/prototype.js',
        'scriptaculous' => 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js',
        'spf' => 'https://ajax.googleapis.com/ajax/libs/spf/2.4.0/spf.js',
        'swfobject' => 'https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js',
        'three' => 'https://ajax.googleapis.com/ajax/libs/threejs/r84/three.min.js',
        'webfont' => 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js',
    ];

    function __construct($db = null) {
        $this->setConfig($db);
    }

    function setConfig($db) {
        global $debug, $cfg;

        $this->db = & $db;
        (defined('DEBUG') && $cfg['tplbasic_debug']) ? $this->debug = & $debug : $this->debug = false;

        $this->theme = $cfg['tplbasic_theme'];
        $this->static_url = $cfg['STATIC_SRV_URL'];
        $this->css_optimize = $cfg['tplbasic_css_optimize'];
        $this->gzip = $cfg['tplbasic_gzip'];
        $this->css_inline = $cfg['tplbasic_css_inline'];
        $this->lang = $cfg['WEB_LANG'];
    }

    function addtoTplVar($tplvar, $data, $priority = 5) { // change name to appendTo_tplvar? TODO priority support?
        !isset($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $data : $this->tpldata[$tplvar] .= $data;
    }

    function addtoTplVarUniq($tplvar, $data) {
        $this->tpldata[$tplvar] = $data;
    }

    function addIfEmpty($tplvar, $data) {
        empty($this->tpldata[$tplvar]) ? $this->tpldata[$tplvar] = $data : null;
    }

    function addtoTplIfEmpty($tpl_ary) {
        if (empty($tpl_ary)) {
            return false;
        }
        foreach ($tpl_ary as $key => $value) {
            $this->addtoTplVar($key, $value);
        }
    }

    function getTplValue($value) {
        return $this->tpldata[$value];
    }

    function getTplData() {
        return $this->tpldata;
    }

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

    function addStdScript($key, $url) {
        if (array_key_exists($key, $this->std_remote_scripts)) {
            return 0;
        }
        $this->std_remote_scripts[$key] = $url;
    }

    private function checkScript($script) {
        foreach ($this->scripts as $value) {
            if ($value == $script) {
                return true;
            }
        }
        return false;
    }

    function addScriptFile($plugin, $filename = null, $place = 'TOP', $async = 'async') {

        $this->debug ? $this->debug->log('AddScriptFile request ->' . $plugin . 'for get a ' . $filename, 'tplBasic', 'DEBUG') : null;

        if (!empty($plugin) && ($plugin == 'standard')) {
            if (!$this->checkScript($filename)) {
                if (array_key_exists($filename, $this->std_remote_scripts)) {
                    $script_url = $this->std_remote_scripts[$filename];
                    $script = '<script type="text/javascript" src="' . $script_url . '" charset="UTF-8" ' . $async . '></script>';
                    $this->addtoTplVar("SCRIPTS_" . $place . "", $script);
                    $this->scripts[] = $filename;
                    $backtrace = debug_backtrace();
                    $this->debug ? $this->debug->log("AddcriptFile:CheckScript setting first time * $filename * by " . $backtrace[1]['function'], 'tplBasic', 'DEBUG') : null;
                } else {
                    $backtrace = debug_backtrace();
                    $this->debug ? $this->debug->log("AddcriptFile:CheckScript standard script * $filename * not found called by " . $backtrace[1]['function'], 'tplBasic', 'WARNING') : null;
                }
            } else {
                $backtrace = debug_backtrace();
                $this->debug ? $this->debug->log("AddcriptFile:CheckScript found coincidence * $filename * called by " . $backtrace[1]['function'], 'tplBasic', 'DEBUG') : null;
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
            $script = '<script type="text/javascript" src="' . $this->static_url . $SCRIPT_PATH . '" charset="UTF-8" ' . $async . '></script>';
        } else {
            $this->debug ? $this->debug->log("AddScriptFile called by-> $plugin for get a $filename but NOT FOUND IT", 'tplBasic', 'ERROR') : null;
            return false;
        }
        $this->addtoTplVar('SCRIPTS_' . $place, $script);
    }

    function getCssFile($plugin, $filename = null) {

        empty($filename) ? $filename = $plugin : null;
        if (in_array($filename, $this->css_added)) {
            return;
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
            if ($this->css_inline == 0) {
                if (file_exists($USER_PATH)) {
                    $css = "<link rel='stylesheet' href='/$USER_PATH'>\n";
                } else if (file_exists($DEFAULT_PATH)) {
                    $css = "<link rel='stylesheet' href='/$DEFAULT_PATH'>\n";
                }
            } else {
                if (file_exists($USER_PATH)) {
                    $css_code = $this->parseFile($USER_PATH);
                } else if (file_exists($DEFAULT_PATH)) {
                    $css_code = $this->parseFile($DEFAULT_PATH);
                }
                isset($css_code) ? $css = '<style>' . $this->cssStrip($css_code) . '</style>' : null;
            }
            if (isset($css)) {
                $this->addtoTplVar('LINK', $css);
            } else {
                $this->debug ? $this->debug->log("Get CSS called by-> $plugin for get a $filename NOT FOUND IT", 'tplBasic', 'DEBUG') : null;
            }
        }
    }

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

    function cssCache() {

        if (!$this->cssCacheCheck() || empty($this->css_cache_onefile)) {
            return false;
        }

        $css_code = "";

        $cssfile = $this->css_cache_onefile . '.css';
        $this->debug ? $this->debug->log('CSS One file Unify ' . $cssfile, 'tplBasic', 'DEBUG') : null;
        if (!file_exists('cache/css/' . $cssfile)) {
            foreach ($this->css_cache_filepaths as $cssfile_path) {
                $this->debug ? $this->debug->log('CSS Unify ' . $cssfile_path, 'tplBasic', 'DEBUG') : null;
                $css_code .= $this->parseFile($cssfile_path);
            }
            $css_code = $this->cssStrip($css_code);
            file_put_contents('cache/css/' . $cssfile, $css_code);
        }
        if ($this->css_inline == 0) {
            $this->addtoTplVar('LINK', '<link rel="stylesheet" href="/cache/css/' . $cssfile . '">');
        } else {
            $css_code = $this->parseFile('cache/css/' . $cssfile);
            $this->addtoTplVar("LINK", '<style>' . $css_code . '</style>');
        }

        return true;
    }

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

    private function parseFile($path, $data = null) {
        global $LNG, $cfg;

        $this->debug ? $this->debug->log("TPL parse $path, gzip its {$cfg['tplbasic_gzip']}", 'tplBasic', 'DEBUG') : null;

        $tpldata = $this->getTplData();

        isset($this->gzip) && $this->gzip == 1 ? ob_start('ob_gzhandler') : ob_start();

        include ($path);
        $content = ob_get_contents();
        ob_end_clean();

        if ($cfg['tplbasic_html_optimize']) { // that going to give problems... :)
            //TODO...
            $content = preg_replace('/(\>)\s+(\<)/S', '$1$2', $content); //spaces between > <            
        }
        return $content;
    }

}
