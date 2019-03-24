<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div class='catlist'>
    <p><?= $LNG['L_NEWS_CREATE_CAT'] ?></p>
    <form id='cat_new' method='post' action=''>
        <div>
            <?= isset($data['catrow_new']) ? $data['catrow_new'] : null ?>
            <label><?= $LNG['L_NEWS_FATHER'] ?></label>
            <input class='news_adm_father' type='text' maxlength='3' name='father' value='0' />
            <label><?= $LNG['L_NEWS_ORDER'] ?></label>
            <input class='news_adm_order' type='text' maxlength='3' name='weight' value='0' />
            <input type='submit' name='NewCatSubmit' value='<?= $LNG['L_NEWS_CREATE'] ?>' />
        </div>
    </form>
</div>
<div class='catlist'>
    <p><?= $LNG['L_NEWS_MODIFY_CATS'] ?></p>
    <?= isset($data['catlist']) ? $data['catlist'] : null ?>
</div>