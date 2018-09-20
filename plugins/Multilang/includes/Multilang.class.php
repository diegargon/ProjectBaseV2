<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class Multilang {

    private $web_lang;
    private $set_to_visit_lang;
    private $active_langs;
    private $site_langs;

    function __construct($cfg) {
        $this->retrieve_db_langs();
        $this->set_to_visit_lang = $cfg['ml_set_to_visit_lang'];
        register_action("header_menu_element", [$this, "get_nav"], 6);
        $this->setLang();
        $this->web_lang = $cfg['WEB_LANG'];
    }

    function setLang() {
        global $filter, $cfg;


        /*
         * 1 choosed lang
         * 2 Cookie lang
         * 3 visit_lang if exist (ACCEPT_LANGUAGE), 
         * 4 URL lang 
         * 5 default web lang  
         */


        if (isset($_POST['choose_lang']) && (($choosed_lang = $filter->post_AZChar("choose_lang", 2, 2)) != false)) {
            if ($this->checkExists($choosed_lang)) {
                $cfg['WEB_LANG'] = $choosed_lang;
                setcookie("WEB_LANG", $choosed_lang, 2147483647, '/');
                return;
            }
        } else {
            if (!empty(($cookie_lang = $filter->cookie_AlphaNum("WEB_LANG", 255, 1))) && $cookie_lang != false) {
                $cfg['WEB_LANG'] = $cookie_lang;
                return;
            }
        }

        if ($this->set_to_visit_lang) {
            $user_pref_lang = $filter->srv_accept_language();

            if (isset($user_pref_lang) && $user_pref_lang != false) {
                $user_pref_lang = substr($user_pref_lang, 0, 2);
            }
            if ($this->checkExists($user_pref_lang)) {
                $cfg['WEB_LANG'] = $user_pref_lang;
                return;
            }
        }


        $url_lang = $filter->get_AZChar("lang", 2, 2);

        if (isset($url_lang) && $url_lang != false && $this->checkExists($url_lang)) {
            $cfg['WEB_LANG'] = $url_lang;
        }
        return;
    }

    function get_nav() {
        global $tpl, $cfg;

        $content = "";
        $element = 1;
        $elements = count($this->get_site_langs());

        foreach ($this->get_site_langs() as $lang) {
            if ($element == 1) {
                $data['TPL_FIRST'] = 1;
            } else if ($element == $elements) {
                unset($data['TPL_FIRST']);
                $data['TPL_LAST'] = 1;
            } else {
                unset($data['TPL_FIRST']);
            }

            if ($lang['iso_code'] == $cfg['WEB_LANG']) {
                $data['selected'] = "selected";
            } else {
                $data['selected'] = "";
            }

            $element++;

            $data['iso_code'] = $lang['iso_code'];
            $data['lang_name'] = $lang['lang_name'];
            $content .= $tpl->getTPL_file("Multilang", "ml_menu_opt", $data);
        }

        return $content;
    }

    function checkExists($lang) {
        foreach ($this->get_site_langs() as $site_lang) {
            if ($site_lang['iso_code'] == $lang) {
                return true;
            }
        }
        return false;
    }

    function get_site_langs($active = 1) {
        if ($active) {
            return $this->active_langs;
        } else {
            return $this->site_langs;
        }
    }

    function getSessionLangID() {
        global $cfg;
        return $this->iso_to_id($cfg['WEB_LANG']);
    }

    function getSessionLang() {
        global $cfg;

        $lid = $this->iso_to_id($cfg['WEB_LANG']);
        return $this->active_langs[$lid];
    }

    function iso_to_id($isolang) {
        foreach ($this->get_site_langs() as $lang) {
            if ($lang['iso_code'] == $isolang) {
                return $lang['lang_id'];
            }
        }
        return false;
    }

    function id_to_iso($lang_id) {
        foreach ($this->get_site_langs() as $lang) {
            if ($lang['lang_id'] == $lang_id) {
                return $lang['iso_code'];
            }
        }
        return false;
    }

    function get_sitelangs_select($name) {

        $site_langs = $this->get_site_langs();

        if (empty($site_langs)) {
            return false;
        }

        $select = "<select name='$name' id='$name'>";
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $this->web_lang) {
                $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            } else {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            }
        }
        $select .= "</select>";

        return $select;
    }

    private function retrieve_db_langs() {
        global $db;

        $query = $db->select_all("lang");

        while ($lang_row = $db->fetch($query)) {
            if ($lang_row['active'] == 1) {
                $this->active_langs[$lang_row['lang_id']] = $lang_row;
                $this->site_langs[$lang_row['lang_id']] = $lang_row;
            } else {
                $this->site_langs[$lang_row['lang_id']] = $lang_row;
            }
        }
        $db->free($query);
    }

}
