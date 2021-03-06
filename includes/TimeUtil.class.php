<?php

/**
 *  TimeUtil
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage CORE
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * Class TimeUtil
 * 
 * TODO: Check user preferences
 */
Class TimeUtil {

    /**
     * Dateformat
     * 
     * @var string
     */
    private $dateformat;

    /**
     * Timezone
     * 
     * @var string 
     */
    private $timezone;

    /**
     * Server Timezone
     * 
     * @var string
     */
    private $server_timezone;

    /**
     * Database date time format
     * @var string
     */
    private $date_db_format;

    /**
     * configure time defaults
     * 
     * @global array $cfg
     */
    function configTime() {
        global $cfg;
        $this->dateformat = $cfg['default_dateformat'];
        $this->timezone = $cfg['default_timezone'];
        $this->server_timezone = $cfg['server_timezone'];
        $this->date_db_format = $cfg['DEFAULT_DB_DATEFORMAT'];
        $this->setTimeZone();
    }

    /**
     * get time zone
     * 
     * @return string
     */
    function getTimeZone() {
        return date_default_timezone_get();
    }

    /**
     * get time now
     * 
     * @return string|false
     */
    function getTimeNow() {
        $now = new DateTime();
        return $now->format($this->dateformat);
    }

    /**
     * convert timestamp to date
     * 
     * @param int $timestamp
     * @return string|false
     */
    function timestampToDate($timestamp) {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date->format($this->dateformat);
    }

    /**
     * format a date retrieve from db
     * 
     * @param string $db_date
     * @return string|false
     */
    function formatDbDate($db_date) {
        $date = DateTime::createFromFormat($this->date_db_format, $db_date);
        return $date->format($this->dateformat);
    }

    /**
     * get timezone identifiers
     * 
     * @return array|false
     */
    function getTzList() {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    /**
     * set time zone
     */
    private function setTimeZone() {
        date_default_timezone_set($this->timezone);
    }

}
