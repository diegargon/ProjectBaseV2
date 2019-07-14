<?php
/**
 *  SMBasic menu options template
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if (isset($data['login_menu'])) {
    ?>
    <div class="nav_top">
        <a class="header-menu-link" style="border: 1px solid black;border-radius: 5px;padding:5px;margin-left:5px;"  href="<?= $data['login_url'] ?>" rel="nofollow"><?= $LNG['L_LOGIN'] ?>
        </a>
    </div>
    <?php
}
if (isset($data['register_menu'])) {
    ?>
    <div class="nav_top">
        <a class="header-menu-link" style="border: 1px solid black;border-radius: 5px;padding:5px;margin-left:5px;" href="<?= $data['register_url'] ?>" rel="nofollow"><?= $LNG['L_REGISTER'] ?>
        </a>
    </div>
    <?php
}
if (isset($data['profile_menu'])) {
    ?>
    <div class="drop_nav_top"><a class="header-menu-link" href="<?= $data['profile_url'] ?>" rel="nofollow"><?= $LNG['L_PROFILE'] ?></a></div>
    <?php
}
if (isset($data['logout_menu'])) {
    ?>
    <div class="drop_nav_top">
        <a class="header-menu-link" href="<?= $data['logout_url'] ?>" rel="nofollow"><?= $LNG['L_LOGOUT'] ?>
        </a>
    </div>
    <?php
}
if (isset($data['drop_menu_caption'])) {
    ?>
    <span class="drop_nav_top">
        <img src="<?= $data['avatar'] ?>" alt=""/>
        <span class="drop_btn_caption"><?= $data['username'] ?></span>
    </span>
    <?php
} 
