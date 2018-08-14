<?php

/*
 *  Copyright @ 2018 Diego Garcia
 * 
 *  Validation filters
 * 
 */

class SecureFilter {

//$_GET
    //S_GET_INT
    function get_int($var, $max_size = null, $min_size = null) {

        if ((!isset($_GET[$var])) || (!empty($max_size) && (strlen($_GET[$var]) > $max_size) ) || (!empty($min_size) && (strlen($_GET[$var]) < $min_size)) || !is_numeric($_GET[$var])
        ) {
            return false;
        }

        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_INT);
    }

    //S_GET_CHAR_AZ
    function get_AZChar($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->var_AZChar($_GET[$var], $max_size, $min_size);
    }

    //S_GET_TEXT_UTF8
    function get_UTF8_txt($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->var_UTF8_txt($_GET[$var], $max_size, $min_size);
    }

    //S_GET_EMAIL
    function get_email($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_EMAIL);
    }

    //S_GET_URI
    function get_URL($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_URL);
    }

    //S_GET_STRICT_CHARS
    function get_strict_chars($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }

        return $this->var_strict_chars($_GET[$var], $max_size, $min_size);
    }

//$_POST
    //S_POST_PASSWORD
    function post_password($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->var_password($_POST[$var], $max_size = null, $min_size = null);
    }

    //S_POST_EMAIL
    function post_email($var) {
        if (empty($_POST[$var])) {
            return false;
        }
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_EMAIL);
    }

    //S_POST_CHAR_AZNUM
    function post_AlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->var_AZNum($_POST[$var], $max_size, $min_size);
    }

    //S_POST_CHAR_AZ
    function post_AZChar($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->var_AZChar($_POST[$var], $max_size, $min_size);
    }

    //S_POST_TEXT_UTF8
    function post_UTF8_txt($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->var_UTF8_txt($_POST[$var], $max_size, $min_size);
    }

    //S_POST_STRICT_CHARS
    function post_strict_chars($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->var_strict_chars($_POST[$var], $max_size, $min_size);
    }

    //S_POST_INT
    function post_int($var, $max_size = null, $min_size = null) {
        if (!isset($_POST[$var]) || !is_numeric($_POST[$var])) {
            return false;
        }

        return $this->var_int($_POST[$var], $max_size, $min_size);
    }

    //S_POST_URL
    function post_URL($var, $max_size = null, $min_size = null) {

        if (empty($_POST[$var])) {
            return false;
        }
        if (is_array($_POST[$var])) {
            $var_ary = $_POST[$var];
            foreach ($var_ary as $key => $value) {
                $ret = var_URL($value, $max_size, $min_size);
                if (!$ret) {
                    $var_ary[$key] = false;
                } else {
                    $var_ary[$key] = $ret;
                }
            }
            return $var_ary;
        } else {
            return $this->var_URL($_POST[$var], $max_size, $min_size);
        }
    }

    //S_POST_CHARNUM_MIDDLE_UNDERSCORE_UNICODE
    function post_alphanum_middle_underscore_unicode($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return var_alphanum_unicode($_POST[$var], $max_size, $min_size);
    }

    //$_SERVER
    // S_SERVER_REQUEST_URI
    function srv_request_uri() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
    }

    //S_SERVER_USER_AGENT
    function srv_user_agent() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW);
    }

    //S_SERVER_REMOTE_ADDR
    function srv_remote_addr() {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    }

    //S_SERVER_URL
    function srv_url($var) {
        if (empty($_SERVER[$var])) {
            return false;
        }
        return $this->var_URL($_SERVER[$var]);
    }

    function srv_accept_language() {
        return filter_input(INPUT_SERVER, "HTTP_ACCEPT_LANGUAGE", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);        
    }
    //VAR
    //S_VAR_PASSWORD
    function var_password($var, $max_size = null, $min_size = null) {
        global $cfg;
        if (defined('SM') && empty($max_size) && empty($min_size)) {
            $max_size = $cfg['sm_max_password'];
            $min_size = $cfg['sm_min_password'];
        }

        if ((!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        /*
          No spaces only... allow all characteres since we hash we not need restrict characters
          No keywords requirements, since its more secure and easy remember
          something like this_is_my_long_password than $12#45ab
         */
        if (!preg_match("/^(\S+)+$/", $var)) {
            return false;
        }
        return $var;
    }

    //S_VAR_INTEGER
    function var_int($var, $max_size = null, $min_size = null) {

        if ((!isset($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size)) || !is_numeric($var)
        ) {
            return false;
        }

        return filter_var($var, FILTER_VALIDATE_INT);
    }

    //S_VAR_CHAR_AZ
    function var_AZChar($var, $max_size = null, $min_size = null) {

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        if (preg_match("/[^A-Za-z]/", $var)) {
            return false;
        }

        return $var;
    }

    //S_VAR_FILENAME
    function var_filename($file, $max_size = null, $min_size = null) {

        if ((empty($file) ) || (!empty($max_size) && (strlen($file) > $max_size) ) || (!empty($min_size) && (strlen($file) < $min_size))
        ) {
            return false;
        }
        // clean filename @ StackOverflow 2021624/Sean Viera
        $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
        $file = mb_ereg_replace("([\.]{2,})", '', $file);
        return trim($file);
    }

    //S_VAR_URL
    function var_URL($var, $max_size = null, $min_size = null, $force_no_remote_check = null) {
        global $cfg;

        if (empty($var)) {
            return false;
        }
        if ((strpos($var, 'http://') !== 0) && (strpos($var, 'https://') !== 0)) {
            $var = "http://" . $var;
        }
        if ((!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        $url = filter_var($var, FILTER_SANITIZE_URL);
        $url = filter_var($url, FILTER_VALIDATE_URL);

        if (empty($url)) {
            return false;
        }
        if ($cfg['REMOTE_CHECKS'] && (!remote_check($url))) {
            return false;
        }
        return $url;
    }

    //S_VAR_STRICT_CHARS
    function var_strict_chars($var, $max_size = null, $min_size = null) {
        /*
         * This filter  allow: characters Az 1-9 , "_" (in middle) ... Can't begin with number
         * For username, ACL roles    
         * TODO add support for áÁ 
         */
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        if (!preg_match("/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/", $var)) {
            return false;
        }

        return $var;
    }

    //S_VAR_TEXT_UTF8
    function var_UTF8_txt($var, $max_size = null, $min_size = null) {
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        //  TODO
        if (!preg_match("//u", $var)) {
            return false;
        }

        return $var;
    }

    //S_VAR_CHAR_NUM_UNICODE
    function var_alphanum_unicode($var, $max_size = null, $min_size = null) {
        // NO TESTED: Unicode chars and nums only
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        if (!preg_match('/^[\p{L}\p{N}]+$/', $var)) {
            return false;
        }

        return $var;
    }

    //S_VAR_CHAR_UNICODE
    function var_char_unicode($var, $max_size = null, $min_size = null) {
        // NO TESTED  Unicode chars only
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        if (!preg_match('/^[\p{L}]+$/', $var)) {
            return false;
        }

        return $var;
    }

    //S_VAR_CHAR_MIDDLE_UNDERSCORE_UNICODE
    //TODO  hacer generica, funcion supliendo el caracter deseado que ira en el medio
    //
    function var_char_middle_underscore_unicode($var, $max_size = null, $min_size = null) {
        // NO TESTED Unicode chars and _ in middle

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        if (!preg_match('/^[\p{L}][\p{L}]*(?:_[\p{L}]+)*$/', $var)) {
            return false;
        }
        return $var;
    }

    //S_VAR_CHARNUM_MIDDLE_UNDERSCORE_UNICODE
    //TODO  hacer generica, funcion supliendo el caracter deseado que ira en el medio
    function var_alphanum_middle_underscore_unicode($var, $max_size = null, $min_size = null) {
        // NO TESTED Unicode chars and _ in middle

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }

        if (!preg_match('/^[\p{L}\p{N}][\p{L}\p{N}]*(?:_[\p{L}\p{N}]+)*$/', $var)) {
            return false;
        }
        return $var;
    }

    //S_VAR_CHAR_AZ_NUM
    function var_AlphaNum($var, $max_size = null, $min_size = null) {
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        if (!preg_match('/^[A-Za-z0-9]+$/', $var)) {
            return false;
        }

        return $var;
    }

//COOKIE
    //S_COOKIE_INT
    function cookie_int($var, $max_size = null, $min_size = null) {

        if (empty($_COOKIE[$var]) || !is_numeric($_COOKIE[$var])) {
            return false;
        }
        return $this->var_int($_COOKIE[$var], $max_size, $min_size);
    }

    //S_COOKIE_CHAR_AZNUM
    function cookie_AlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_COOKIE[$var])) {
            return false;
        }
        return $this->var_AlphaNum($_COOKIE[$var], $max_size, $min_size);
    }

    //S_VALIDATE_MEDIA
    function validate_media($url, $max_size = null, $min_size = null, $force_no_remote_check = null) {
        global $cfg;

        $regex = '/\.(' . $cfg['ACCEPTED_MEDIA_REGEX'] . ')(?:[\?\#].*)?$/';

        if (($url = var_URL($url, $max_size, $min_size, $force_no_remote_check)) == false) {
            return -1;
        }

        if (!preg_match($regex, $url)) {
            return -1;
        }

        return $url;
    }

}
