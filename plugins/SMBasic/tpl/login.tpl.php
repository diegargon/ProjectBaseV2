<?php
/**
 *  SMBasic login page template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <?php if (!empty($data['oAuth_data'])) { ?>
        <div class="register_oauth_box">
            <?= ($data['oAuth_data']) ?>
        </div>
    <?php } ?>
    <div class="login_box">
        <form  id="login_form" action="#"  method="post"> 
            <h1><?= $LNG['L_LOGIN'] ?></h1>
            <p>
                <label for="email"><?= $LNG['L_EMAIL'] ?></label>
                <input id="email" name="email" required="required" type="text" maxlength="<?= $cfg['smbasic_max_email'] ?>" minlength="<?= $cfg['smbasic_min_email'] ?>" placeholder="<?= $LNG['L_EMAIL_EXAMPLE'] ?>"/>
            </p>
            <p>
                <label id="label_password" for="password"><?= $LNG['L_PASSWORD'] ?></label>
                <input id="password" name="password" required="required" type="password"  maxlength="<?= $cfg['smbasic_max_password'] ?>" minlength="<?= $cfg['smbasic_min_password'] ?>" placeholder="<?= $LNG['L_PASSWORD_EXAMPLE'] ?>" />
            </p>
            <p class="rememberme">
                <?php if ($cfg['smbasic_persistence']) { ?>
                    <input  type="checkbox" name="rememberme" id="rememberme" value="1" />
                    <label id="label_rememberme" for="rememberme"><?= $LNG['L_REMEMBERME'] ?></label>
                <?php } ?>
                <input type="checkbox" name="reset_password_chk" id="reset_password_chk" value="3" />
                <label  for="reset_password_chk"><?= $LNG['L_RESET_PASSWORD'] ?></label>
            </p>
            <p class="login button">
                <input type="submit" id="login" name="login" class="btnLogin" value="<?= $LNG['L_LOGIN'] ?>" /> 
            </p>
            <p class="login button">
                <input hidden type="submit" id="reset_password_btn" name="reset_password" class="btnReset" value="<?= $LNG['L_RESET_PASSWORD_BTN'] ?>" />
            </p>
            <p class="change_link">
                <?= $LNG['L_REGISTER_MSG'] ?>
                <a href="<?= $data['register_url'] ?>" class="to_register"><?= $LNG['L_REGISTER'] ?></a>
            </p>
        </form>
    </div>
</div>