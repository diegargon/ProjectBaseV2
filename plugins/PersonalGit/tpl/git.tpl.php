<?php
/**
 *  PersonalGit git page template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage PersonalGit
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <div class="github_user">
        <div class="github_user_top">
            <div class="github_user_top_avatar"><img src="<?= $data['avatar_url'] ?>" alt="avatar"></div>
            <div class="github_user_top_name"><a href="<?= $data['html_url'] ?>" target=_blank><?= $data['name'] ?></a><br/>@<?= $data['login'] ?></div>
        </div>

        <div class="github_user_middle_details">
            <ul>
                <li> <span class="octicon octicon-organization"></span>&nbsp; <?= $data['company'] ?></li>
                <li><span class="octicon octicon-location"></span>&nbsp; <?= $data['location'] ?></li>
                <li><span class="octicon octicon-link"></span>&nbsp; <a href="<?= $data['blog'] ?>"><?= $data['blog'] ?></a></li>
                <li><span class="octicon octicon-mail"></span>&nbsp; <a href="mailto:<?= $data['email'] ?>"><?= $data['email'] ?></a></li>        
            </ul>
        </div>

        <div class="github_user_bottom_details">
            <a  href="https://github.com/<?= $data['login'] ?>" target=_blank><strong><?= $data['public_repos'] ?></strong><span>Repos</span></a>
            <a  href="https://gist.github.com/<?= $data['login'] ?>" target=_blank><strong><?= $data['public_gists'] ?></strong><span>Gist</span></a>
            <a  href="https://github.com/<?= $data['login'] ?>/following" target=_blank><strong><?= $data['followers'] ?></strong><span>Followers</span></a>
        </div>

    </div>

    <div class="github_repo">
        <?= $data['repo_data'] ?>
    </div>    

</div>