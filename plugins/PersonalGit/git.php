<?php

/**
 *  Git page
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage PersonalGit
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ': ' . "Github";
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ': ' . "Github";

$tpl->getCssFile('PersonalGit');
$tpl->addStdCSS('octicons');

$curl_token = 'Authorization: token ' . $cfg['git_token'];
$user_info = get_user($cfg['git_user'], $curl_token);

if (isset($user_info->message)) {
    $LNG['GIT_ERROR'] = $user_info->message; //TODO: posibilidad de mandar mensaje directo al msgbox sin ser a traves de LNG
    $msgbox['msg'] = 'GIT_ERROR';
    $frontend->messageBox($msgbox);
    return false;
}

$response = get_repos($cfg['git_user'], $curl_token);
$repo_data = '';

if (!empty($response)) {
    foreach ($response as $repo) {
        $readme_content = get_readme($cfg['git_user'], $repo->name, $curl_token);
        if (strlen($readme_content) > $cfg['max_readme_chars']) {
            $readme_content = substr($readme_content, 0, $cfg['max_readme_chars']) . "...";
        }
        // TODO format readme
        $repo_data .= '<b>Project</b>: <a href="' . $repo->html_url . '" target="_blank">' . $repo->name . '</a><br />';
        $repo_data .= '<div class="github_readme">';
        $repo_data .= '<p>Readme:</p>';
        $repo_data .= nl2br(htmlspecialchars($readme_content));
        $repo_data .= '<br/>';
        $repo_data .= '</div>';
        $repo_data .= '<br/>';
    }
}
$git_data = [
    'git_token' => $cfg['git_token'],
    'git_user' => $cfg['git_user'],
    'avatar_url' => $user_info->avatar_url,
    'html_url' => $user_info->html_url,
    'name' => $user_info->name,
    'login' => $user_info->login,
    'company' => $user_info->company,
    'location' => $user_info->location,
    'blog' => $user_info->blog,
    'email' => $user_info->email,
    'public_repos' => $user_info->public_repos,
    'public_gists' => $user_info->public_gists,
    'followers' => $user_info->followers,
    'repo_data' => $repo_data,
];

$tpl->addtoTplVar('ADD_TO_BODY', $tpl->getTplFile('PersonalGit', 'git', $git_data));
