<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<section><h3><?= $LNG['L_ML_CREATE_LANG'] ?></h3>
    <form id='form_create' action='#' method='post'>
        <label><?= $LNG['L_NAME'] ?>:</label><input required maxlength='32' type='text' name='lang_name' id='lang_name' value='' />
        <label><?= $LNG['L_ML_ACTIVE'] ?>: </label><input checked type='checkbox' name='active' id='active' value='1' />
        <label><?= $LNG['L_ML_ISOCODE'] ?>: </label><input required maxlength='2' type='text' name='iso_code' id='iso_code' value=''/>
        <input type='submit' id='btnCreateLang' name='btnCreateLang' value='<?= $LNG['L_ML_CREATE'] ?>' />
    </form>
</section>
