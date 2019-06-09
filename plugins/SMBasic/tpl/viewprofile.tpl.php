<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="profile_box">
        <h1><?= $LNG['L_VIEWPROFILE'] ?></h1>
        <div id="avatar">
            <?php
            if (!empty($data['avatar'])) {
                ?>
                <img class="image_link" width="125" height="150" src="<?= $data['avatar'] ?>" alt="" />
                <?php
            } else {
                ?>
                <img width="125" height="150" src="<?= $cfg['smbasic_default_img_avatar'] ?>" alt="" />
            <?php } ?>
        </div>
        <div id="profile_fields">
            <dl>
                <dt><label><?= $LNG['L_USERNAME'] ?> </label></dt>
                <dd><span><?= $data['username'] ?> </span></dd>
            </dl>
            <dl>
                <dt><label><?= $LNG['L_SM_REGISTERED'] ?></label></dt>
                <dd><span><?= $data['regdate'] ?> </span></dd>
            </dl>
            <dl>
                <dt><label><?= $LNG['L_SM_LASTLOGIN'] ?> </label></dt>
                <dd><span><?= $data['last_login'] ?> </span></dd>
            </dl>
            <?= !empty($tpldata['SMB_VIEWPROFILE_FIELDS_BOTTOM']) ? $tpldata['SMB_VIEWPROFILE_FIELDS_BOTTOM'] : false; ?>            
        </div>
        <p class='p_center_medium'><a href="<?= $data['BACKLINK'] ?>"><?= $LNG['L_BACK'] ?></a></p>
    </div>
</div>
