<?php
/**
 *  SMBasic register template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <div class="register_box">
        <form  id="register_form" action="#" method="post">
            <h1><?= $LNG['L_REGISTER'] ?></h1>
            <?php if ($cfg['smbasic_need_username']) { ?>
                <p>
                    <label for="username"><?= $LNG['L_USERNAME'] ?> </label>
                    <input id="username" maxlength="<?= $cfg['smbasic_max_username'] ?>" minlength="<?= $cfg['smbasic_min_username'] ?>" name="username"  type="text" placeholder=""/>
                </p> 
            <?php } ?>
            <p>
                <label for="email"><?= $LNG['L_EMAIL'] ?> </label>
                <input id="email" name="email" required="required" maxlength="<?= $cfg['smbasic_max_email'] ?>" minlength="<?= $cfg['smbasic_min_email'] ?>" type="text" placeholder="<?= $LNG['L_EMAIL_EXAMPLE'] ?>"/>
            </p>
            <p>
                <label for="password"><?= $LNG['L_PASSWORD'] ?> </label>
                <input id="password" name="password" required="required" type="password" maxlength="<?= $cfg['smbasic_max_password'] ?>" minlength="<?= $cfg['smbasic_min_password'] ?>" placeholder=""/>
            </p>
            <p>
                <label for="rpassword"><?= $LNG['L_RPASSWORD'] ?> </label>
                <input id="rpassword" name="rpassword" required="required" type="password" maxlength="<?= $cfg['smbasic_max_password'] ?>" minlength="<?= $cfg['smbasic_min_password'] ?>"  placeholder=""/>
            </p>
            <p>
                <a href="<?= $data['terms_url'] ?>" target="_blank"><?= $LNG['L_TOS'] ?></a><input id="tos" name="tos" required="required" type="checkbox"/>
            </p>            
            <p class="register button">
                <input type="submit" id="register" name="register" class="btnRegister" value="<?= $LNG['L_REGISTER'] ?>" />
            </p>
        </form>
    </div>
</div>