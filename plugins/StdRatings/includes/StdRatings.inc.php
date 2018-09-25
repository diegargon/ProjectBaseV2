<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function rating_rate($uid, $resource_id, $section, $rate) {
    global $filter, $db;

    if (!isset($uid) || empty($resource_id) || empty($section) || empty($rate)) {
        return false;
    }

    $ip = $filter->srv_remote_addr();
    if ($ip == false) {
        return false;
    }

    $insert_ary = [
        'uid' => $uid,
        'ip' => $ip,
        'section' => $section,
        'resource_id' => $resource_id,
        'vote_value' => $rate
    ];

    $r = $db->insert('rating', $insert_ary);

    return $r ? true : false;
}

function check_already_vote($uid, $resource_id, $section) {
    global $cfg, $filter, $db;

    $where_ary = [];

    if ($cfg['one_ip_one_vote']) {
        $ip = $filter->srv_remote_addr();
        if (!$ip) {
            return false;
        }
        $where_ary = [
            'ip' => $ip,
        ];
    } else if ($cfg['allow_anonoymous_vote'] && ($uid < 1)) {
        return true; // if not ip check and user its anonymous always allow; 
    }

    $where_ary = [
        'uid' => $uid,
        'section' => $section,
        'resource_id' => $resource_id,
    ];

    $query = "SELECT uid, vote_value, date FROM rating WHERE section = $section AND resource_id = $resource_id ";
    if ($cfg['one_ip_one_vote']) {
        if ($cfg['allow_anonymous_vote']) {
            $query .= 'AND ip = ' . $ip;
        } else {
            $query .= "AND (uid = $uid OR ip = $ip)";
        }
    } else {
        $query .= 'AND uid = ' . $uid;
    }
    $query .= ' LIMIT 1';

    $result = $db->query($query);


    if ($db->num_rows($result) > 0) {
        return $db->fetch($result);
    } else {
        return false;
    }
}

function rating_css_display($rating) {
    if ($rating <= 0.25 || empty($rating)) {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 0.75) {
        $rate['rating1'] = 'vHalf';
        $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vFull';
    } else if ($rating <= 1.25) {
        $rate['rating1'] = 'vFull';
        $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 1.75) {
        $rate['rating1'] = 'vFull';
        $rate['rating2'] = 'vHalf';
        $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 2.25) {
        $rate['rating1'] = $rate['rating2'] = 'vFull';
        $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 2.75) {
        $rate['rating1'] = $rate['rating2'] = 'vFull';
        $rate['rating3'] = 'vHalf';
        $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 3.25) {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = 'vFull';
        $rate['rating4'] = $rate['rating5'] = 'vVoid';
    } else if ($rating <= 3.75) {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = 'vFull';
        $rate['rating4'] = 'vHalf';
        $rate['rating5'] = 'vVoid';
    } else if ($rating <= 4.25) {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = 'vFull';
        $rate['rating5'] = 'vVoid';
    } else if ($rating <= 4.75) {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = 'vFull';
        $rate['rating5'] = 'vHalf';
    } else {
        $rate['rating1'] = $rate['rating2'] = $rate['rating3'] = $rate['rating4'] = $rate['rating5'] = 'vFull';
    }

    return $rate;
}
