<?php
/**
 *  SimpleCategories admin create category template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleCategories
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<p><?= isset($data['msg']) ? $data['msg'] : null ?></p>
<div class="catlist">
    <p><?= $LNG['L_CATS_CREATE_CAT'] ?></p>
    <form id="cat_new" method="post" action="">
        <div>
            <?= isset($data['catrow_new']) ? $data['catrow_new'] : null ?>
            <label><?= $LNG['L_CATS_FATHER'] ?>
                <input class="cat_father" type="text" maxlength="3" size="1" name="father" value="0" />
            </label>
            <label><?= $LNG['L_CATS_WEIGHT'] ?>
                <input class="cat_weight" type="text" maxlength="3" size="1" name="weight" value="0" />
            </label>            
            <input type="submit" name="NewCatSubmit" value="<?= $LNG['L_CREATE'] ?>" />
            <br/><label><?= $LNG['L_CATS_CATIMAGE'] ?>
                <input class="cat_image" type="text" maxlength="255" size="97" name="cat_image" value="" />
            </label>
        </div>
    </form>
</div>
