<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="profile_box">
        <form id="profile_form" action="" autocomplete="off" method="post">
            <h1><?= $LNG['L_PROFILE'] ?></h1>
            <div id="avatar">
                <?php
                if (!empty($data['avatar'])) {
                    ?>
                    <img width="125" height="150" src="<?= $data['avatar'] ?>" alt="avatar" />
                    <?php
                } else {
                    ?>
                    <img width="125" height="150" src="<?= $cfg['smbasic_default_img_avatar']; ?>" alt="avatar" />
                <?php } ?>
            </div>
            <?= !empty($tpldata['SMB_PROFILE_POST_AVATAR']) ? $tpldata['SMB_PROFILE_POST_AVATAR'] : false ?>
            <div id="profile_fields">
                <?= !empty($tpldata['SMB_PROFILE_FIELDS_TOP']) ? $tpldata['SMB_PROFILE_FIELDS_TOP'] : false ?>
                <dl>
                    <dt><label><?= $LNG['L_USERNAME'] ?></label><br/>
                        <span class="profile_subtext"><?= $LNG['L_USERNAME_H'] ?> </span>
                    </dt>
                    <dd>
                        <?php
                        if ($cfg['smbasic_can_change_username'] && isset($data['username'])) {
                            ?>
                            <input required id="username" name="username" type="text" value="<?= $data['username'] ?>" title="<?= $LNG['L_USERNAME_H'] ?>" autocomplete="off" />
                            <?php
                        } else if (isset($data['username'])) {
                            ?>
                            <input disabled id="username" name="username" type="text" value="<?= $data['username'] ?>" title="<?= $LNG['L_USERNAME_H'] ?>"/>
                            <?php
                        }
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt><label>Avatar</label></dt>
                    <dd>
                        <input class="avatar_field" name="avatar" type="text"  value="<?= $data['avatar'] ?>" title="" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?= $LNG['L_EMAIL'] ?></label><br/>
                        <span class="profile_subtext"><?= $LNG['L_EMAIL_H'] ?> </span>
                    </dt>
                    <dd>
                        <?php
                        if ($cfg['smbasic_can_change_email']) {
                            ?>
                            <input required id="email" name="email" type="text"  value="<?= $data['email'] ?>" title="<?= $LNG['L_EMAIL_H'] ?>" autocomplete="off"/>
                            <?php
                        } else if (isset($data['email'])) {
                            ?>
                            <input disabled id="email" name="email" type="text" value="<?= $data['email'] ?>" title="<?= $LNG['L_EMAIL_H'] ?>"/>                
                            <?php
                        }
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?= $LNG['L_NEW_PASSWORD'] ?> :</label><br/>
                        <span class="profile_subtext"><?= $LNG['L_NEW_PASSWORD_H'] ?> </span>
                    </dt>
                    <dd>
                        <input  readonly onfocus="this.removeAttribute('readonly');"  type="password" name="new_password" id="new_password" title="<?= $LNG['L_NEW_PASSWORD_H'] ?>" autocomplete="off"/>
                    </dd>
                </dl>
                <dl>
                    <dt><label><?=$LNG['L_RPASSWORD'] ?></label><br/>
                        <span class="profile_subtext"><?= $LNG['L_R_PASSWORD_H'] ?> </span>
                    </dt>
                    <dd>
                        <input  type="password" name="r_password" id="r_password" title="<?= $LNG['L_R_PASSWORD_H'] ?>" autocomplete="off"/>
                    </dd>
                </dl> 
                <dl>
                    <dt><label><?= $LNG['L_PASSWORD'] ?></label><br/>
                        <span class="profile_subtext"><?= $LNG['L_CUR_PASSWORD_H'] ?> </span>
                    </dt>
                    <dd>
                        <input required type="password" name="cur_password" id="cur_password" title="<?= $LNG['L_CUR_PASSWORD_H'] ?>" autocomplete="off"/>
                    </dd>
                </dl>
            </div>
            <?= !empty($tpldata['SMB_PROFILE_FIELDS_BOTTOM']) ? $tpldata['SMB_PROFILE_FIELDS_BOTTOM'] : false; ?>
            <p class="inputBtnSend"><input type="submit" id="profile" name="profile" value="<?= $LNG['L_SEND'] ?>" class=""  /></p>                                    
        </form>
    </div>
</div>