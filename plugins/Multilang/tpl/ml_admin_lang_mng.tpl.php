<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<?php if ($data['TPL_CTRL'] == 1) { ?>
    <section><h3><?= $LNG['L_ML_MODIFY_LANGS'] ?></h3>
    <?php } ?>
    <form id='form_modify' action='#' method='post'>
        <label><?= $LNG['L_NAME'] ?>: </label>
        <input maxlength='32' type='text' name='lang_name' id='lang_name' value='<?= $data['lang_name'] ?>' />
        <label><?= $LNG['L_ML_ACTIVE'] ?>: </label>
        <?php if ($data['active']) { ?>
            <input checked type='checkbox' name='active' id='active'  value='1' />
        <?php } else { ?>
            <input type='checkbox' name='active' value='1'/>
        <?php } ?>        
        <label><?= $LNG['L_ML_ISOCODE'] ?>: </label>
        <input maxlength='2' type='text' name='iso_code' id='iso_code' value='<?= $data['iso_code'] ?>' />
        <input type='hidden' name='lang_id' value='<?= $data['lang_id'] ?>' />
        <input type='submit' id='btnModifyLang' name='btnModifyLang' value='<?= $LNG['L_ML_MODIFY'] ?>' />
        <input type='submit' id='btnDeleteLang' name='btnDeleteLang' value='<?= $LNG['L_ML_DELETE'] ?>' onclick="return confirm('<?= $LNG['L_ML_SURE'] ?>')" />
    </form>
    <?php if ($data['TPL_FOOT'] == 1) { ?>    
    </section>
    <section><h3><?= $LNG['L_ML_CREATE_LANG'] ?></h3>
        <form id='form_create' action='#' method='post'>
            <label><?= $LNG['L_NAME'] ?>:</label><input required maxlength='32' type='text' name='lang_name' id='lang_name' value='' />
            <label><?= $LNG['L_ML_ACTIVE'] ?>: </label><input checked type='checkbox' name='active' id='active' value='1' />
            <label><?= $LNG['L_ML_ISOCODE'] ?>: </label><input required maxlength='2' type='text' name='iso_code' id='iso_code' value=''/>
            <input type='submit' id='btnCreateLang' name='btnCreateLang' value='<?= $LNG['L_ML_CREATE'] ?>' />
        </form>
    </section>
    <?php
}