<?php

/**
 *  StdRatings main include file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage StdRatings
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/**
 * general rate get post data function
 * 
 * @global filter $filter
 * @param string $section
 * @return boolean
 */
function rating_rate_getPost($section) {
    global $filter;

    if (!($user_rate = $filter->postInt('rate', 5, 1))) {
        return false;
    }

    if (!($id = $filter->postInt('rate_rid'))) {
        return false;
    }

    return rating_rate($id, $section, $user_rate);
}

/**
 * Rating
 * 
 * @global filter $filter
 * @global db $db
 * @global sm $sm
 * @param int $resource_id
 * @param string $section
 * @param int $rate
 * @return boolean
 */
function rating_rate($resource_id, $section, $rate) {
    global $filter, $db, $sm;

    if (empty($resource_id) || empty($section) || empty($rate)) {
        return false;
    }

    $user = $sm->getSessionUser();

    $ip = $filter->srvRemoteAddr();
    if ($ip == false) {
        return false;
    }

    $insert_ary = [
        'uid' => $user['uid'],
        'ip' => $ip,
        'section' => $section,
        'resource_id' => $resource_id,
        'vote_value' => $rate
    ];

    $r = $db->insert('rating', $insert_ary);

    return $r ? true : null;
}

/**
 * Get all ratings by id/section
 * 
 * @global db $db
 * @param string $ids #comma separate ids
 * @param string $section
 * @return array
 */
function ratings_get_ratings($ids, $section) {
    global $db;

    $where_ary['section'] = $section;
    $where_ary['resource_id'] = ['value' => '(' . $ids . ')', 'operator' => 'IN'];

    $query = $db->selectAll('rating', $where_ary);
    return $db->fetchAll($query);
}

/**
 * rating get content for display rate
 * 
 * @global tpl $tpl
 * @global array $cfg
 * @global sm $sm
 * @param string $section
 * @param int $resource_id
 * @param int $author_id
 * @param int $lang_id
 * @param array $ratings_data
 * @param string $image_vote
 * @return type
 */
function ratings_get_content($section, $resource_id, $author_id, $lang_id, $ratings_data, $image_vote = null) {
    global $tpl, $cfg, $sm;

    //TODO: Manage anonymous rating

    empty($image_vote) ? $img_vote = $cfg['WEB_URL'] . '/' . $cfg['dflt_vote_visuals_url'] : null;
    $rate_data['BTN_EXTRA'] = ' style="background: url(' . $img_vote . ') no-repeat;" ';

    $user = $sm->getSessionUser();

    $btn_disable = 0;
    if ($author_id == $user['uid'] && $user['uid'] > 0) {
        $rate_data['show_pointer'] = 0;
        $btn_disable = 1;
    } else {
        $rate_data['show_pointer'] = 1;
    }
    $vote_counter = 0;
    $sum_votes = 0;

    //buscamos si ya hay algun rating, por usuario al recurso  si es asi deshabilitamos
    foreach ($ratings_data as $rating_row) {
        if (($resource_id == $rating_row['resource_id'])) {
            $vote_counter++;
            $sum_votes = $sum_votes + $rating_row['vote_value'];
            if (($rating_row['uid'] == $user['uid'] && $user['uid'] > 0)) {
                $btn_disable = 1;
                $rate_data['show_pointer'] = 0;
            }
        }
    }

    $btn_disable ? $rate_data['BTN_EXTRA'] .= 'disabled' : null;

    $rate_data['id'] = $resource_id;
    $rate_data['lang_id'] = $lang_id;
    $rate_data['section'] = $section;

    ($vote_counter > 0) ? $rating = $sum_votes / $vote_counter : $rating = 0;
    $rate_stars = rating_css_display($rating);
    $rate_data = array_merge($rate_data, $rate_stars);

    return $tpl->getTplFile('StdRatings', 'display_rate', $rate_data);
}

/**
 * rating css format the rating display
 * 
 * @param float $rating
 * @return string
 */
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
