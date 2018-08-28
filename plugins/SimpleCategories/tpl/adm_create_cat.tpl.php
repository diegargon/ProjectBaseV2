<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div class='catlist'>
    <p><?= $LNG['L_CATS_CREATE_CAT'] ?></p>
    <form id='cat_new' method='post' action=''>
        <div>
            <?= isset($data['catrow_new']) ? $data['catrow_new'] : null ?>
            <label><?= $LNG['L_CATS_FATHER'] ?></label>
            <input class='cat_father' type='text' maxlength='3' size='1' name='father' value='0' />
            <label><?= $LNG['L_CATS_WEIGHT'] ?></label>
            <input class='cat_weight' type='text' maxlength='3' size='1' name='weight' value='0' />
            <input type='submit' name='NewCatSubmit' value='<?= $LNG['L_CREATE'] ?>' />
        </div>
    </form>
</div>
