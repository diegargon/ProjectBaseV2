<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class SessionManager {

    private $user;
    private $users_cache_db = [];
    private $debug;
    private $perms;

    /*
     * 1 php default php 2 custom 
     */
    private $session_type;
    private $session_start;
    private $session_expire = 86400;
    private $cookie_prefix;
    private $cookie_expire = 86400;
    private $persistence = 0;
    private $salt;

    /*
     * Custom session data array
     */
    private $s_data = [];

    /* */
    private $check_ip;
    private $check_user_agent;

    /* CONFIG */
    private $friendly_url;
    private $file_path;

    function __construct() {
        
    }

    function start() {
        global $debug;

        $this->setConfig();

        $this->debug ? $debug->log('Starting SMBasic session management, type ' . $this->session_type, 'SMBasic', 'DEBUG') : null;
        $this->session_start ? session_start() : false;

        if (!$this->checkSession()) {
            $this->debug ? $debug->log('Check session return false, setting session to anonymous', 'SMBasic', 'DEBUG') : null;
            $this->setAnonSession();
        } else {
            $this->debug ? $debug->log('Check session OK', 'SMBasic', 'DEBUG') : null;
        }
    }

    function setUsersInCacheByIDs($uid_list) {
        global $db;

        $retrieve_ids_list = '';
        $elements = 0;
        $xpl_users_list = explode(',', $uid_list);
        foreach ($xpl_users_list as $user_id) {
            if ($user_id > 0 && !isset($this->users_cache_db[$user_id])) {
                $elements++;
                empty($retrieve_ids_list) ? $retrieve_ids_list = $user_id : $retrieve_ids_list .= ',' . $user_id;
            }
        }

        if (!empty($retrieve_ids_list)) {
            $query = $db->select_all('users', ['uid' => ['value' => '(' . $retrieve_ids_list . ')', 'operator' => 'IN']], 'LIMIT ' . $elements . '');

            while ($user = $db->fetch($query)) {
                $this->users_cache_db[$user['uid']] = $user;
            }
        }
    }

    function getUserByID($uid) {
        global $db;

        if (empty($uid)) {
            return false;
        }
        if (isset($this->users_cache_db[$uid])) {
            return $this->users_cache_db[$uid];
        }

        $query = $db->select_all('users', ['uid' => $uid], 'LIMIT 1');
        if ($db->num_rows($query) <= 0) {
            return false;
        }
        $user = $db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getUserByUsername($username) {
        global $db;

        if (($uid = array_search($username, array_column($this->users_cache_db, 'username')))) {
            return $this->users_cache_db[$uid];
        }
        $query = $db->select_all('users', ['username' => $username], 'LIMIT 1');

        if ($db->num_rows($query) <= 0) {
            return false;
        }
        $user = $db->fetch($query);
        $this->users_cache_db[$user['uid']] = $user;

        return $user;
    }

    function getSessionUser() {
        return $this->user;
    }

    function checkSession() {
        global $debug;
        $this->debug ? $debug->log('CheckSession called', 'SMBasic', 'Info') : null;

        if ($this->checkAnonSession()) {
            $this->debug ? $debug->log('User: checkSession its setting to anonymous, stopping more checks', 'SMBasic', 'DEBUG') : null;
            return true;
        }

        if ($this->session_type == 1) {
            return $this->check_phpbuildin_session();
        } else {
            die('Custom session Not work/tested yet');
            return $this->check_custom_session();
        }
        /*
          } else {
         *  do_action ("check_session_extra")
         */
        return false;
    }

    function getAllUsersArray($order_field = 'regdate', $order = 'ASC', $limit = 20) {
        global $db;
        $extra = 'ORDER BY ' . $order_field . ' ' . $order . ' LIMIT ' . $limit;
        $query = $db->select_all('users', null, $extra);
        while ($user_row = $db->fetch($query)) {
            $users_ary[] = $user_row;
        }

        return $users_ary;
    }

    function searchUser($string, $email = false, $glob = false) {
        global $db;

        $where_ary = [];

        if (!empty($email)) {
            if (empty($glob)) {
                $where_ary = ['email' => ['value' => '\'' . $string . '\'', 'operator' => 'LIKE']];
            } else {
                $where_ary = ['email' => ['value' => '\'%' . $string . '%\'', 'operator' => 'LIKE']];
            }
        } else {
            if (empty($glob)) {
                $where_ary = ['username' => ['value' => '\'' . $string . '\'', 'operator' => 'LIKE']];
            } else {
                $where_ary = ['username' => ['value' => '\'%' . $string . '%\'', 'operator' => 'LIKE']];
            }
        }
        $query = $db->select_all('users', $where_ary);
        if ($db->num_rows($query) > 0) {
            while ($user_row = $db->fetch($query)) {
                $users_ary[] = $user_row;
            }
            return $users_ary;
        }

        return false;
    }

    function setData($key, $value) {

        $this->session_type == 1 ? $_SESSION[$key] = $value : false;

        if ($this->session_type == 2) {
            $this->s_data[$key] = $value;
            $this->saveData();
        }
    }

    function getData($key) {
        if ($this->session_type == 1 && isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        if ($this->session_type == 2 && isset($this->s_data[$key])) {
            return $this->s_data[$key];
        }
    }

    function unsetData($key) {
        if ($this->session_type == 1 && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        if ($this->session_type == 2 && isset($this->s_data[$key])) {
            unset($this->s_data[$key]);
            //TODO update session data table field
        }
    }

    function destroyData() {
        $this->session_type == 1 ? $_SESSION = [] : false;

        if ($this->session_type == 2) {
            $this->s_data = [];
            //TODO clear session table data field
        }
    }

    private function saveData() {
        global $db;

        $data = serialize($this->s_data);
        $next_expire = time() + $this->session_expire;
        $db->update('sessions', ['session_data' => $data, 'session_expire' => $next_expire], ['uid' => $this->user['uid']], 'LIMIT 1');
    }

    private function loadData() {
        global $db;
        $query = $db->select_all('sessions', ['uid' => $this->user['uid']], 'LIMIT 1');
        $session = $db->fetch($query);

        if ($session['session_expire'] < time()) {
            return false; //session expire
        } else if (!empty($session['session_data'])) {
            $this->s_data = unserialize($session['session_data']);
        }
    }

    function setUserSession($user, $remember = 0) {
        global $debug, $filter, $db;

        $this->debug ? $debug->log('setUserSession called', 'SMBasic', 'DEBUG') : null;
        $this->unsetAnonSession();

        //TODO PHP7 supports change session expire? DOIT, <7 will destroy and use default 20m
        $session_expire = time() + $this->session_expire;

        if ($this->session_type == 1) {
            $this->setData('uid', $user['uid']);
            session_regenerate_id(true);
            $sid = session_id();
        } else { //Custom
            //$sid = $this->createSID();
        }

        $this->setData('session_ip', $filter->srv_remote_addr());
        $this->setData('session_user_agent', $filter->srv_user_agent());


        if (!($this->session_type == 1) || ($this->persistence && $remember)) {

            $db->delete('sessions', ['session_uid' => $user['uid']], 'LIMIT 1');

            $q_ary = [
                'session_id' => $sid,
                'session_uid' => $user['uid'],
                'session_ip' => $db->escape_strip($filter->srv_remote_addr()),
                'session_browser' => $db->escape_strip($filter->srv_user_agent()),
                'session_expire' => $session_expire
            ];

            $db->insert('sessions', $q_ary);
            $this->setCookies($sid, $user['uid']);
            $db->update('users', ['last_login' => date('Y-m-d H:i:s', time())], ['uid' => $user['uid']]);
        }

        return $sid;
    }

    function destroy() {
        global $debug, $db;
        $this->debug ? $debug->log('Session destroy called', 'SMBasic', 'DEBUG') : null;

        $this->user = false;
        $db->delete('sessions', ['session_uid' => $this->user['uid']]);
        $this->clearCookies();
        isset($_SESSION) ? session_destroy() : false;
        $this->destroyData();
    }

    function logout() {
        $this->destroy();
        header('Location: /');
    }

    function setAnonSession() {
        global $debug;

        if ($this->session_type == 1) {
            $this->debug ? $debug->log('Setting session as anonymous', 'SMBasic', 'DEBUG') : null;

            $this->clearCookies();
            $this->destroyData();
            $this->setData('anonymous', 1);
        } else {
            $this->debug ? $debug->log('Setting cookies as anonymous', 'SMBasic', 'DEBUG') : null;
            $this->clearCookies();
            $cookie_name_anon = $this->cookie_prefix . 'anonymous';
            setcookie($cookie_name_anon, 1, 0, '/');
        }
    }

    function encryptPassword($password) {
        global $cfg;
        //echo hash('sha512', md5($password . $cfg['smbasic_pw_salt']));
        if (!action_isset('encrypt_password')) {
            if ($cfg['smbasic_use_salt']) {
                return hash('sha512', md5($password . $cfg['smbasic_pw_salt']));
            } else {
                return hash('sha512', $password);
            }
        } else {
            return do_action('encrypt_password');
        }
    }

    function getPage($page) {
        if ($page == 'login') {
            return $this->file_path . 'login';
        }
        if ($page == 'logout') {
            return $this->file_path . 'logout';
        }
        if ($page == 'register') {
            return $this->file_path . 'register';
        }
        if ($page == 'profile') {
            return $this->file_path . 'profile';
        }
        if ($page == 'terms') {
            return $this->file_path . 'terms';
        }
    }

    function setPerms($cfg) {

        $this->perms['register_enable'] = $cfg['smbasic_register_enable'];
        $this->perms['login_enable'] = $cfg['smbasic_login_enable'];
        /*
          if(defined('ACL')) {
          $this->setACLPerms();
          } else {
          $this->setNoNACLPerms();
          }
         */
    }

    function setACLPerms() {
        
    }

    function setNonACLPerms() {
        
    }

    function getPerms() {
        return $this->perms;
    }

    function getPerm($perm) {
        return ($this->perms[$perm]) ? $this->perms[$perm] : false;
    }

    private function setConfig() {
        global $cfg;

        (defined('DEBUG') && $cfg['smbasic_debug']) ? $this->debug = 1 : $this->debug = 0;

        $this->setPerms($cfg);

        if ($cfg['FRIENDLY_URL']) {
            $this->file_path = '/' . $cfg['WEB_LANG'] . '/';
        } else {
            $this->file_path = '/' . $cfg['CON_FILE'] . '?module=SMBasic&lang=' . $cfg['WEB_LANG'] . '&page=';
        }

        if ($cfg['smbasic_default_session']) {
            $this->session_type = 1;
        } else { //Custom
            $this->session_type = 2;
            $this->loadData();
        }
        if ($cfg['smbasic_session_start'] || $cfg['smbasic_default_session']) {
            $this->session_start = 1;
        }
        $this->salt = $cfg['smbasic_session_salt'];
        $this->check_ip = $cfg['smbasic_check_ip'];
        $this->check_user_agent = $cfg['smbasic_check_user_agent'];
        !empty($cfg['smbasic_session_expire']) ? $this->session_expire = $cfg['smbasic_session_expire'] : false;
        !empty($cfg['smbasic_persistence']) ? $this->persistence = $cfg['smbasic_persistence'] : false;
        !empty($cfg['smbasic_cookie_prefix']) ? $this->cookie_prefix = $cfg['smbasic_cookie_prefix'] : false;
        !empty($cfg['smbasic_cookie_expire']) ? $this->cookie_prefix = $cfg['smbasic_cookie_expire'] : false;
    }

    /*
      private function createSID() {
      global $filter;

      $hash_string = mt_rand(0, mt_getrandmax()) .
      md5(substr($filter->srv_remote_addr(), 0, 5)) .
      $this->salt .
      md5(microtime(true) . time());

      return hash('sha256', $hash_string);
      }
     */

    private function getCookies() {
        global $filter;

        $c['uid'] = $filter->cookie_int($this->cookie_prefix . 'uid');
        $c['sid'] = $filter->cookie_AlphaNum($this->cookie_prefix . 'sid', 255);

        return $c;
    }

    private function clearCookies() {
        $cookie_name_anon = $this->cookie_prefix . 'anonymous';
        $cookie_name_sid = $this->cookie_prefix . 'sid';
        $cookie_name_uid = $this->cookie_prefix . 'uid';
        unset($_COOKIE[$cookie_name_sid]);
        unset($_COOKIE[$cookie_name_uid]);
        unset($_COOKIE[$cookie_name_anon]);
        setcookie($cookie_name_sid, 0, time() - 3600, '/');
        setcookie($cookie_name_uid, 0, time() - 3600, '/');
        setcookie($cookie_name_anon, 0, time() - 3600, '/');
        $this->session_type == 1 ? setcookie('phpsessid', 0, time() - 3600) : false;
    }

    private function setCookies($sid, $uid) {

        $cookie_name_sid = $this->cookie_prefix . 'sid';
        $cookie_name_uid = $this->cookie_prefix . 'uid';
        if ($this->cookie_expire > 0) {
            $cookie_expire = time() + $this->cookie_expire;
        } else {
            $cookie_expire = 0; //this session only
        }
        setcookie($cookie_name_sid, $sid, $cookie_expire, '/');
        setcookie($cookie_name_uid, $uid, $cookie_expire, '/');
    }

    private function checkIp($session_ip) {
        global $filter, $debug;

        $ip = $filter->srv_remote_addr();

        $this->debug ? $debug->log('IP check ' . $ip . ' == ' . $session_ip, 'SMBasic', 'DEBUG') : null;

        return ($ip == $session_ip) ? true : false;
    }

    private function check_user_agent() {
        $session_user_agent = $this->getData('session_user_agent');
        $user_agent = srv_user_agent();
        return ($user_agent == $session_user_agent) ? true : false;
    }

    //TODO... do better later 
    private function checkAnonSession() {
        global $debug;

        if ($this->session_type == 1) {
            $this->debug ? $debug->log('Checking if is anonymous (buildin)', 'SMBasic', 'DEBUG') : null;
            return isset($_SESSION['anonymous']) ? true : false;
        } else {
            $this->debug ? $debug->log('Cheking anon (custom/cookies)', 'SMBasic', 'DEBUG') : null;
            $cookie_name_anon = $this->cookie_prefix . 'anonymous';
            return isset($_COOKIE[$cookie_name_anon]) ? true : false;
        }
    }

    private function unsetAnonSession() {
        global $debug;

        if ($this->session_type == 1) {
            $this->debug ? $debug->log('Unsetting anonymous session', 'SMBasic', 'DEBUG') : null;
            $this->unsetData('anonymous');
        } else {
            $this->debug ? $debug->log('SMBasic: Unsetting anonymous cookie ', 'SMBasic', 'DEBUG') : null;
            $this->debug ? $debug->log('Unsetting anonymmous cookie', 'SMBasic', 'DEBUG') : null;
            $cookie_name_anon = $this->cookie_prefix . 'anonymous';
            unset($_COOKIE[$cookie_name_anon]);
            setcookie($cookie_name_anon, 0, time() - 3600, '/');
        }
    }

    private function check_phpbuildin_session() {
        global $debug;

        $this->debug ? $debug->log('Check phpbuildin session', 'SMBasic', 'INFO') : null;
        $uid = $this->getData("uid");

        if (!empty($uid)) {
            if ($this->check_ip && ($this->checkIp($this->getData('session_ip')) == false)) {
                $this->debug ? $debug->log('IP validation FALSE', 'SMBasic', 'WARNING') : null;
                return false;
            }

            if ($this->check_user_agent && ($this->check_user_agent() == false)) {
                $this->debug ? $debug->log('User agent validation FALSE', 'SMBasic', 'WARNING') : null;
                return false;
            }
            $this->user = $this->getUserByID($uid);
            $this->updateExpire($uid);
            return true;
        }

        if (empty($uid) && $this->persistence) {
            $cookies = $this->getCookies();
            if (empty($cookies['uid']) || empty($cookies['sid'])) {
                $this->debug ? $debug->log('Cookies empty', 'SMBasic', 'DEBUG') : null;
                return false;
            } else {
                $this->debug ? $debug->log('Checking persistence (buildin)', 'SMBasic', 'DEBUG') : null;

                if (!($uid = $this->check_persistence($cookies))) {
                    $this->debug ? $debug->log('Cookies invalid detected', 'SMBasic', 'DEBUG') : null;
                    $this->clearCookies();
                    return false;
                } else {
                    $this->user = $this->getUserByID($uid);
                    $this->setUserSession($this->user, $this->persistence);
                    return true;
                }
            }
        }

        return false;
    }

    /*
      private function check_custom_session() {
      global $debug;

      $uid = $this->getData("uid");

      $cookies = $this->getCookies();
      if (empty($cookies['uid']) || empty($cookies['sid'])) {
      return false;
      }

      if ($uid != $cookies['uid']) {
      return false;
      }
      if ($this->persitence) {
      $this->debug ? $debug->log("Checking persistence (custom)", "SMBasic", "DEBUG") : null;

      $session = $this->check_persistence($cookies);
      if ($session) {
      $this->user = $this->getUserByID($session['session_uid']);
      $this->setData("uid", $this->user['uid']);
      $this->regenerate_sid(1);
      return true;
      } else {
      return false;
      }
      }

      }
     */

    private function check_persistence($cookies) {
        global $debug, $db;
        $sid = $cookies['sid'];
        $uid = $cookies['uid'];

        $this->debug ? $debug->log('Check persistence ' . $uid . ',' . $sid, 'SMBasic', 'DEBUG') : null;
        $query = $db->select_all('sessions', ['session_id' => $sid, 'session_uid' => $uid], 'LIMIT 1');

        if ($db->num_rows($query) < 1) {
            return false;
        }
        $session = $db->fetch($query);
        $db->free($query);

        if ($this->check_ip == 1 && !($this->checkIp($session['session_ip']))) {
            $this->debug ? $debug->log('IP validated FALSE', 'SMBasic', 'DEBUG') : null;
            return false;
        }
        if ($this->check_user_agent == 1 && (!$this->check_user_agent())) {
            $this->debug ? $debug->log('UserAgente validated FALSE', 'SMBasic', 'DEBUG') : null;
            return false;
        }

        $now = time();
        $next_expire = time() + $this->session_expire;
        if ($session['session_expire'] < $now) {
            $debug->log('SMBasic: db session expired', 'SM_DEBUG');
            $db->delete('sessions', ['session_id' => $sid], 'LIMIT 1');
            return false;
        }
        $this->updateExpire($uid);

        return $uid;
    }

    function updateExpire($uid) {
        global $db;

        $expire = time() + $this->session_expire;
        $db->update('sessions', ['session_expire' => $expire], ['session_uid' => $uid], 'LIMIT 1');
    }

}
