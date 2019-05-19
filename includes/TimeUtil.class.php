<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

//TODO: Check user preferences

Class TimeUtil {

    private $dateformat;
    private $timezone;
    private $server_timezone;
    private $date_db_format;

    function configTime() {
        global $cfg;
        $this->dateformat = $cfg['default_dateformat'];
        $this->timezone = $cfg['default_timezone'];
        $this->server_timezone = $cfg['server_timezone'];
        $this->date_db_format = $cfg['DEFAULT_DB_DATEFORMAT'];
        $this->setTimeZone();
    }

    function setTimeZone() {
        date_default_timezone_set($this->timezone);
    }

    function getTimeZone() {
        return date_default_timezone_get();
    }

    function getTimeNow() {
        $now = new DateTime();
        return $now->format($this->dateformat);
    }

    function timestampToDate($timestamp) {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date->format($this->dateformat);
    }

    function formatDbDate($db_date) {
        $date = DateTime::createFromFormat($this->date_db_format, $db_date);
        return $date->format($this->dateformat);
    }

    function getTzList() {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

}
