<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function rating_rate_getPost($section) {
    global $filter;

    if (!($user_rate = $filter->post_int("rate", 5, 1))) {
        return false;
    }

    if (!($id = $filter->post_int('rate_rid'))) {
        return false;
    }

    return rating_rate($id, $section, $user_rate);
}

function rating_rate($resource_id, $section, $rate) {
    global $filter, $db, $sm;

    if (empty($resource_id) || empty($section) || empty($rate)) {
        return false;
    }

    $user = $sm->getSessionUser();
    $uid = $user['uid'];

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

function ratings_get_ratings($ids, $section) {
    global $db;

    $where_ary['section'] = $section;
    $where_ary['resource_id'] = ['value' => '(' . $ids . ')', 'operator' => 'IN'];

    $query = $db->select_all('rating', $where_ary);
    return $db->fetch_all($query);
}

function ratings_get_content($section, $resource_id, $author_id, $lang_id, $ratings_data, $image_vote = null) {
    global $tpl, $cfg, $sm;

    //TODO: Manage anonymous rating

    empty($image_vote) ? $img_vote = $cfg['dflt_vote_visuals_url'] : false;
    $rate_data['BTN_EXTRA'] = ' style="background: url(' . $img_vote . ') no-repeat;" ';

    $user = $sm->getSessionUser();

    if ($author_id == $user['uid']) {
        $rate_data['show_pointer'] = 0;
        $rate_data['BTN_EXTRA'] .= "disabled";
    } else {
        $rate_data['show_pointer'] = 1;
        $vote_counter = 0;
        $sum_votes = 0;
        foreach ($ratings_data as $rating_row) { //buscamos si ya hay algun rating, por usuario al comentario  si es asi deshabilitamos
            if (($resource_id == $rating_row['resource_id'])) {
                $vote_counter++;
                $sum_votes = $sum_votes + $rating_row['vote_value'];
                if (($rating_row['uid'] == $user['uid'])) {
                    $rate_data['BTN_EXTRA'] .= "disabled";
                    $rate_data['show_pointer'] = 0;
                }
            }
        }
    }
    $rate_data['id'] = $resource_id;
    $rate_data['lang_id'] = $lang_id;
    $rate_data['section'] = $section;

    ($vote_counter > 0) ? $rating = $sum_votes / $vote_counter : $rating = 0;
    $rate_stars = rating_css_display($rating);
    $rate_data = array_merge($rate_data, $rate_stars);

    return $tpl->getTplFile('StdRatings', 'display_rate', $rate_data);
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

function UNUSED_check_already_vote($uid, $resource_id, $section) {
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
