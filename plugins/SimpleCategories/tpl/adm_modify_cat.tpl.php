<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<?php
if ($data['TPL_CTRL'] == 1) {
    ?>
    <div class='catlist'>
        <p><?= $LNG['L_CATS_MODIFY_CATS'] ?></p>        
    <?php } ?>        
    <form id='cat_mod' method='post' action=''>
        <div>
            <label>Id</label>
            <input type='text' readonly  name='cid' class='cat_id' size='1' value='<?= $data['catid'] ?>'/>    
            <?= isset($data['catlist']) ? $data['catlist'] : null ?>
            <label><?= $LNG['L_CATS_FATHER'] ?></label>
            <input class='cat_father' type='text' maxlength='3' size='1' name='father' value='<?= $data['catFather'] ?>' />
            <label><?= $LNG['L_CATS_WEIGHT'] ?></label>
            <input class='cat_weight' type='text' maxlength='3' size='1' name='weight' value='<?= $data['catWeight'] ?>' />
            <label><?= $LNG['L_PL_PLUGINS'] ?></label>
            <input readonly class='cat_plugin' type='text'  size='<?= strlen($data['plugin']) ?>' name='plugin' value='<?= $data['plugin'] ?>' />
            <input type='submit' name='ModCatSubmit' value='<?= $LNG['L_MODIFY'] ?>' />
            <input type='submit' name='DelCatSubmit' value='<?= $LNG['L_DELETE'] ?>' />
        </div></form>
    <?php
    if ($data['TPL_FOOT'] == 1) {
        ?>        
    </div>
<?php } ?>
