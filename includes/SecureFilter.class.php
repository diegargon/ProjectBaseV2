<?php

/**
 *  SecureFilter
 * 
 *  Validation filters
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */

/**
 * Class SecureFilter
 */
class SecureFilter {

    /**
     * Max Mysql INT
     * 
     * @var int
     */
    private $max_int;

    /**
     * Do remote checks
     * 
     * @var int
     */
    private $remote_checks;

    /**
     * Media file extension regex
     * 
     * @var string
     */
    private $media_regex;

    /**
     * User name regex
     * 
     * @var string
     */
    private $user_name_regex;

    /**
     * Construct
     * 
     * @global array $cfg
     */
    public function __construct() {
        global $cfg;

        $this->max_int = $cfg['max_int'];
        $this->remote_checks = $cfg['remote_checks'];
        $this->user_name_regex = "/^[A-Za-z][A-Za-z0-9]{5,31}$/";
        $this->media_regex = 'jpe?g|bmp|png|JPE?G|BMP|PNG|gif';
    }

    /**
     * setter name regex
     * 
     * @param string $regex
     */
    function setNameRegex($regex) {
        $this->user_name_regex = $regex;
    }

    /**
     * setter media regex
     * 
     * @param string $regex
     */
    function setMediaRegex($regex) {
        $this->media_regex = $regex;
    }

    /**
     * GET & check integer
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * GET & check A-Z character
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function getAZChar($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->varAzChar($_GET[$var], $max_size, $min_size);
    }

    /**
     * GET & check utf8 text
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function getUtf8Txt($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }
        return $this->varUtf8Txt($_GET[$var], $max_size, $min_size);
    }

    /**
     * GET & check email
     * 
     * @param string $var
     * @return boolean
     */
    function getEmail($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_VALIDATE_EMAIL);
    }

    /**
     * GET & check a url
     * @param string $var
     * @return boolean
     */
    function getUrl($var) {
        if (empty($_GET[$var])) {
            return false;
        }
        return filter_input(INPUT_GET, $var, FILTER_SANITIZE_URL);
    }

    /**
     * GET & check and strict char list chars & '_'
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function getStrictChars($var, $max_size = null, $min_size = null) {
        if (empty($_GET[$var])) {
            return false;
        }

        return $this->varStrictChars($_GET[$var], $max_size, $min_size);
    }

    /**
     * POST & check password
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postPassword($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varPassword($_POST[$var], $max_size = null, $min_size = null);
    }

    /**
     * POST & Check password
     * 
     * @param string $var
     * @return boolean
     */
    function postEmail($var) {
        if (empty($_POST[$var])) {
            return false;
        }
        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_EMAIL);
    }

    /**
     * POST & check a string with char and numbers
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postAlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAlphaNum($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & check A-Z char
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postAZChar($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAzChar($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & check a utf8 text
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postUtf8Txt($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varUtf8Txt($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & check a strict list of chars
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postStrictChars($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varStrictChars($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & Check int
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postInt($var, $max_size = null, $min_size = null) {

        if ((!isset($_POST[$var])) || !is_numeric($_POST[$var]) || (!empty($max_size) && (strlen($_POST[$var]) > $max_size) ) ||
                (!empty($min_size) && (strlen($_POST[$var]) < $min_size))
        ) {
            return false;
        }
        if ($_POST[$var] > $this->max_int) {
            return false;
        }

        return filter_input(INPUT_POST, $var, FILTER_VALIDATE_INT);
    }

    /**
     * POST & check url
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postUrl($var, $max_size = null, $min_size = null, $force_no_remote_checks = null) {

        if (empty($_POST[$var])) {
            return false;
        }
        if (is_array($_POST[$var])) {
            $var_ary = $_POST[$var];
            foreach ($var_ary as $key => $value) {
                $ret = $this->varUrl($value, $max_size, $min_size, $force_no_remote_checks);
                if (!$ret) {
                    $var_ary[$key] = false;
                } else {
                    $var_ary[$key] = $ret;
                }
            }
            return $var_ary;
        } else {
            return $this->varUrl($_POST[$var], $max_size, $min_size, $force_no_remote_checks);
        }
    }

    /**
     * POST & Check a username (custom regex)
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postUsername($var, $max_size = null, $min_size = null) {
        if (!isset($_POST[$var])) {
            return false;
        }

        return $this->varUsername($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & Check Alpha Numeric characteres Unicode
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function postAlphaUnderscoreUnicode($var, $max_size = null, $min_size = null) {
        if (empty($_POST[$var])) {
            return false;
        }

        return $this->varAlphanumUnicode($_POST[$var], $max_size, $min_size);
    }

    /**
     * POST & Check a array
     * 
     * @param string $var
     * @return boolean
     */
    function postArray($var) {
        $val = filter_input(INPUT_POST, $var, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $val == null ? $val = false : null;
        return $val;
    }

    /**
     * Return the server http_referer
     * 
     * @return string|boolean|null
     */
    function srvReferer() {
        if (empty($_SERVER['HTTP_REFERER'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
    }

    /**
     * get the request uri filtered
     * 
     * @return string|boolean|null
     */
    function srvRequestUri() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
    }

    /**
     * get the user agent filtered
     * 
     * @return string|boolean|null
     */
    function srvUserAgent() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW);
    }

    /**
     * get the remote addr filtered
     * @return string|boolean|null
     */
    function srvRemoteAddr() {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            return false;
        }
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    }

    /**
     * get the _SERVER
     * 
     * @param string $var
     * @return string|boolean|null
     */
    function srvUrl($var) {
        if (empty($_SERVER[$var])) {
            return false;
        }
        return $this->varUrl($_SERVER[$var]);
    }

    /**
     * get the accept language
     * 
     * @return string|boolean|null
     */
    function srvAcceptLang() {
        return filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    /**
     * check password
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return string|boolean
     */
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
        if (!preg_match('/^(\S+)+$/', $var)) {
            return false;
        }
        return $var;
    }

    /**
     * check int
     * 
     * @param int $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * check A-Z char
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function varAzChar($var, $max_size = null, $min_size = null) {

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        if (preg_match('/[^A-Za-z]/', $var)) {
            return false;
        }

        return $var;
    }

    /**
     * Check filename
     * 
     * @param string $file
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * check for valid url
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @param int $force_no_remote_checks
     * @return boolean
     */
    function varUrl($var, $max_size = null, $min_size = null, $force_no_remote_checks = null) {

        if (empty($var)) {
            return false;
        }
        if ((strpos($var, 'http://') !== 0) && (strpos($var, 'https://') !== 0)) {
            $var = 'https://' . $var;
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
        if (!$force_no_remote_checks && $this->remote_checks && !remote_check($url)) {
            return false;
        }
        return $url;
    }

    /**
     * check for a strict char list
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

        if (!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $var)) {
            return false;
        }

        return $var;
    }

    /**
     * check for a utf8 text
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function varUtf8Txt($var, $max_size = null, $min_size = null) {
        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))
        ) {
            return false;
        }
        // TODO check if work the regex
        if (!preg_match('//u', $var)) {
            return false;
        }

        return $var;
    }

    /**
     * Check for alpha numeric unicode string
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * check for char unicode
     * 
     * @param string $var
     * @param string $max_size
     * @param string $min_size
     * @return boolean
     */
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

    /**
     * Check for unicode char with underscore in middle
     * 
     * TODO  hacer generica, funcion supliendo el caracter deseado que ira en el medio
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     *  Check for unicode char and numeric with underscore in middle
     * 
     * TODO  hacer generica, funcion supliendo el caracter deseado que ira en el medio
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * Check for alpha numeric 
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
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

    /**
     * check user name
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function varUsername($var, $max_size = null, $min_size = null) {

        if ((empty($var) ) || (!empty($max_size) && (strlen($var) > $max_size) ) || (!empty($min_size) && (strlen($var) < $min_size))) {
            return false;
        }
        if (!preg_match($this->user_name_regex, $var)) {
            return false;
        }

        return $var;
    }

    /**
     * Check for int in a cookie
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function cookieInt($var, $max_size = null, $min_size = null) {

        if (empty($_COOKIE[$var]) || !is_numeric($_COOKIE[$var])) {
            return false;
        }
        return $this->varInt($_COOKIE[$var], $max_size, $min_size);
    }

    /**
     * check for alpha numeric in a cookie
     * 
     * @param string $var
     * @param int $max_size
     * @param int $min_size
     * @return boolean
     */
    function cookieAlphaNum($var, $max_size = null, $min_size = null) {
        if (empty($_COOKIE[$var])) {
            return false;
        }
        return $this->varAlphaNum($_COOKIE[$var], $max_size, $min_size);
    }

    /**
     * Validate a media file url, and opt, do a remote check
     * 
     * @param string $url
     * @param int $max_size
     * @param int $min_size
     * @param int $force_no_remote_check
     * @return type
     */
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
