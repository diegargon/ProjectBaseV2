<?php

/**
 *  PersonalGit Include file
 *  
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage PersonalGit
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

function git_section_nav_elements() {
    global $cfg;
    return '<li><a rel="nofollow" href="' . $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?module=PersonalGit&page=git" onclick="show_loading()">' . $cfg['git_menu_text'] . '</a></li>';
}

function get_user($user, $curl_token) {
    $curl_user_url = 'https://api.github.com/users/' . $user . '';
    return curl_get($curl_user_url, $curl_token);
}

function get_readme($username, $reponame, $curl_token) {
    $curl_readme_url = 'https://api.github.com/repos/' . $username . '/' . $reponame . '/readme';
    $response = curl_get($curl_readme_url, $curl_token);
    if (isset($response->content)) {
        return base64_decode($response->content);
    } else if (isset($response->message)) {
        return $response->message;
    }
}

function get_repos($username, $curl_token) {
    $curl_repo_url = 'https://api.github.com/users/' . $username . '/repos';
    return curl_get($curl_repo_url, $curl_token);

    //return $response; 
}

function curl_get($url, $curl_token) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Awesome-Octocat-App', $curl_token));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
}
