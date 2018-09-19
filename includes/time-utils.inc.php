<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

//TODO: Check user preferences

date_default_timezone_set($cfg['DEFAULT_TIMEZONE']);
$cfg['dateformat'] = $cfg['DEFAULT_DATEFORMAT'];
$cfg['timezone'] = date_default_timezone_get();

function getTimeNow() {
    global $cfg;
    return new DateTime(date($cfg['dateformat'], time()));
}

function format_date($date, $timestamp = false) {
    global $cfg;
    //TODO DateTime
    if ($timestamp) {
        return date($cfg['dateformat'], $date);
    } else {
        return date($cfg['dateformat'], strtotime($date));
    }
}

function timeNowDiff($time) {
    global $cfg;

    $_time = new DateTime($time);
    $_time->setTimezone(new DateTimeZone($cfg['server_timezone']));
    $time_now = new DateTime(date($cfg['default_db_dateformat'], time()));
    $time_now->setTimezone(new DateTimeZone($this->timezone));
    $time_diff = $_time->diff($time_now);

    $result_time['days'] = $time_diff->format("%d");
    $result_time['hours'] = $time_diff->format("%H");
    $result_time['minutes'] = $time_diff->format("%i");
    $result_time['seconds'] = $time_diff->format("%s");

    return $result_time;
}
