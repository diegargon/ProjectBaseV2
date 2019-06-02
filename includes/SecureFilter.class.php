<?php

/*
 *  Copyright @ 2018 Diego Garcia
 * 
 *  Validation filters
 * 
 */

class SecureFilter {

    private $max_int; //MYSQL INT
    private $remote_checks;
    private $media_regex;
    private $user_name_regex;

    public function __construct() {
        global $cfg;

        $this->max_int = $cfg['max_int'];
        $this->remote_checks = $cfg['remote_checks'];
        $this->user_name_regex = "/^[A-Za-z][A-Za-z0-9]{5,31}$/";
        $this->media_regex = 'jpe?g|bmp|png|JPE?G|BMP|PNG|gif';
    }

    function setNameRegex($regex) {
        $this->user_name_regex = $regex;
    }

    function setMediaRegex($regex) {
        $this->media_regex = $regex;
    }

//$_GET
    //S_GET_INT
    function getInt($var, $max_size = null, $min_size = null) {
        if ((!isset($_GET[$var])) || !is_numeric($_GET[$var]) || (!empty($max_size) && ( strlen((string) $_GET[$var]) > $max_size) ) ||
                (!empty($min_size) && (strlen((string) $_GET[$var]) < $min_size))
        ) {
            return false;
        }
        if ($_GET[$var] > $this->max_int) {
            return false;
        }

        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_INT);
    }

    //S_GET_CHAR_AZ
    function getAZChar($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->varAzChar($_GET[$var], $max_size, $min_size);
    }

    //S_GET_TEXT_UTF8
    function getUtf8Txt($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->varUtf8Txt($_GET[$var], $max_size, $min_size);
    }

    //S_GET_EMAIL
    function getEmail($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_EMAIL);
    }

    //S_GET_URI
    function getUrl($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_URL);
    }

    //S_GET_STRICT_CHARS
    function getStrictChars($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }

        return $this->varStrictChars($_GET[$var], $max_size, $min_size);
    }

//$_POST
    //S_POST_PASSWORD
    function postPassword($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varPassword($_POST[$var], $max_size = null, $min_size = null);
    }

    //S_POST_EMAIL
    function postEmail($var) {
        if (empty($_POST[$var])) {
            return false;
        }
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_EMAIL);
    }

    //S_POST_CHAR_AZNUM
    function postAlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAlphaNum($_POST[$var], $max_size, $min_size);
    }

    //S_POST_CHAR_AZ
    function postAZChar($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAzChar($_POST[$var], $max_size, $min_size);
    }

    //S_POST_TEXT_UTF8
    function postUtf8Txt($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varUtf8Txt($_POST[$var], $max_size, $min_size);
    }

    //S_POST_STRICT_CHARS
    function postStrictChars($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varStrictChars($_POST[$var], $max_size, $min_size);
    }

    //S_POST_INT
    function postInt($var, $max_size = null, $min_size = null) {
        if ((!isset($_POST[$var])) || !is_numeric($_POST[$var]) || (!empty($max_size) && ($_POST[$var] > $max_size) ) ||
                (!empty($min_size) && ($_POST[$var] < $min_size))
        ) {
            return false;
        }
        if ($_POST[$var] > $this->max_int) {
            return false;
        }

        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_INT);
    }

    //S_POST_URL
    function postUrl($var, $max_size = null, $min_size = null) {

        if (empty($_POST[$var])) {
            return false;
        }
        if (is_array($_POST[$var])) {
            $var_ary = $_POST[$var];
            foreach ($var_ary as $key => $value) {
                $ret = $this->varUrl($value, $max_size, $min_size);
                if (!$ret) {
                    $var_ary[$key] = false;
                } else {
                    $var_ary[$key] = $ret;
                }
            }
            return $var_ary;
        } else {
            return $this->varUrl($_POST[$var], $max_size, $min_size);
        }
    }

    function postUsername($var, $max_size = null, $min_size = null) {
        if (!isset($_POST[$var])) {
            return false;
        }

        return $this->varUsername($_POST[$var], $max_size, $min_size);
    }

    //S_POST_CHARNUM_MIDDLE_UNDERSCORE_UNICODE
    function postAlphaUnderscoreUnicode($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAlphanumUnicode($_POST[$var], $max_size, $min_size);
    }

    function postArray($var) {
        $val = filter_input(INPUT_POST, $var, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $val == null ? $val = false : null;
        return $val;
    }

    //$_SERVER
    // S_SERVER_REQUEST_URI
    function srvRequestUri() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
    }

    //S_SERVER_USER_AGENT
    function srvUserAgent() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW);
    }

    //S_SERVER_REMOTE_ADDR
    function srvRemoteAddr() {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    }

    //S_SERVER_URL
    function srvUrl($var) {
        if (empty($_SERVER[$var])) {
            return false;
        }
        return $this->varUrl($_SERVER[$var]);
    }

    function srvAcceptLang() {
        return filter_input(INPUT_SERVER, "HTTP_ACCEPT_LANGUAGE", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    //VAR
    //S_VAR_PASSWORD
    function varPassword($var, $max_size = null, $min_size = null) {

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
    function varInt($var, $max_size = null, $min_size = null) {

        if ((!isset($var) ) || (!empty($max_size) && ($var > $max_size) ) || (!empty($min_size) && ($var < $min_size)) || !is_numeric($var)
        ) {
            return false;
        }
        if ($var > $this->max_int) {
            return false;
        }

        return filter_var($var, FILTER_VALIDATE_INT);
    }

    //S_VAR_CHAR_AZ
    function varAzChar($var, $max_size = null, $min_size = null) {

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
    function varFilename($file, $max_size = null, $min_size = null) {

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
    function varUrl($var, $max_size = null, $min_size = null, $force_no_remote_checks = null) {

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
        if ($this->remote_checks && (!remote_check($url))) {
            return false;
        }
        return $url;
    }

    //S_VAR_STRICT_CHARS
    function varStrictChars($var, $max_size = null, $min_size = null) {
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
    function varUtf8Txt($var, $max_size = null, $min_size = null) {
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
    function varAlphanumUnicode($var, $max_size = null, $min_size = null) {
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
    function varCharUnicode($var, $max_size = null, $min_size = null) {
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
    function varCharUnderscoreUnicode($var, $max_size = null, $min_size = null) {
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
    function varAlphaUnderscoreUnicode($var, $max_size = null, $min_size = null) {
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
    function varAlphaNum($var, $max_size = null, $min_size = null) {
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        if (!preg_match('/^[A-Za-z0-9]+$/', $var)) {
            return false;
        }

        return $var;
    }

    //REALNAME
    function varUsername($var, $max_size = null, $min_size = null) {

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        if (!preg_match($this->user_name_regex, $var)) {
            return false;
        }

        return $var;
    }

//COOKIE
    //S_COOKIE_INT
    function cookieInt($var, $max_size = null, $min_size = null) {

        if (empty($_COOKIE[$var]) || !is_numeric($_COOKIE[$var])) {
            return false;
        }
        return $this->varInt($_COOKIE[$var], $max_size, $min_size);
    }

    //S_COOKIE_CHAR_AZNUM
    function cookieAlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_COOKIE[$var])) {
            return false;
        }
        return $this->varAlphaNum($_COOKIE[$var], $max_size, $min_size);
    }

    //S_VALIDATE_MEDIA
    function valMedia($url, $max_size = null, $min_size = null, $force_no_remote_check = null) {

        $regex = '/\.(' . $this->media_regex . ')(?:[\?\#].*)?$/';

        if (($url = $this->varUrl($url, $max_size, $min_size, $force_no_remote_check)) == false) {
            return -1;
        }

        if (!preg_match($regex, $url)) {
            return -1;
        }

        return $url;
    }

}
