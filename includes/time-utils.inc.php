<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class TimeUtils {

    private $cfg;
    private $db;
    private $timezone;
    private $server_timezone;
    private $db_dateformat;
    private $dateformat;

    public function __construct($cfg, $db) {
        $this->cfg = $cfg;
        $this->db = $db;
        $this->server_timezone = date_default_timezone_get();
        $this->setTimezone();
        $this->setDateformat();
        $this->db_dateformat = $cfg['DEFAULT_DB_DATEFORMAT'];
    }

    public function getTimeNow() {
        return new DateTime(date($this->dateformat, time()));
    }

    public function format_date($date, $timestamp = false) {
        //TODO DateTime
        if ($timestamp) {
            return date($this->dateformat, $date);
        } else {
            return date($this->dateformat, strtotime($date));
        }
    }

    public function timeNowDiff($time) {
        $_time = new DateTime($time);
        $_time->setTimezone(new DateTimeZone($this->timezone));

        $time_now = new DateTime(date($this->db_dateformat, time()));
        $time_now->setTimezone(new DateTimeZone($this->timezone));

        $time_diff = $_time->diff($time_now);

        $result_time['days'] = $time_diff->format("%d");
        $result_time['hours'] = $time_diff->format("%H");
        $result_time['minutes'] = $time_diff->format("%i");
        $result_time['seconds'] = $time_diff->format("%s");

        return $result_time;
    }

    private function setTimezone() {
        //TODO: LANG to TIME ZONE OR DETECT USER TIMEZONE WITH Javascript        
        // OR USER PREF
        date_default_timezone_set($this->cfg['DEFAULT_TIMEZONE']);
        $this->timezone = date_default_timezone_get();
    }

    private function setDateFormat() {
        //TODO: Check user preferences
        $this->dateformat = $this->cfg['DEFAULT_DATEFORMAT'];
    }

}
