<?php
/**
 *  SimpleCategories modify category template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleCategories
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>

<?php
if ($data['TPL_CTRL'] == 1) {
    ?>
    <div class="catlist">
        <p><?= $LNG['L_CATS_MODIFY_CATS'] ?></p>        
    <?php } ?>        
    <form id="cat_mod" method="post" action="">
        <div class="cat_row <?= $data['catFather'] == 0 ? 'cat_row_main_cat' : null ?>">
            <label>Id
                <input type="text" readonly  name="cid" class="cat_id" size="1" value="<?= $data['catid'] ?>"/>
            </label>
            <?= isset($data['catlist']) ? $data['catlist'] : null ?>
            <label><?= $LNG['L_CATS_FATHER'] ?>
                <input class="cat_father" type="text" maxlength="3" size="1" name="father" value="<?= $data['catFather'] ?>" />
            </label>
            <label><?= $LNG['L_CATS_WEIGHT'] ?>
                <input class="cat_weight" type="text" maxlength="3" size="1" name="weight" value="<?= $data['catWeight'] ?>" />
            </label>
            <label><?= $LNG['L_PL_PLUGINS'] ?>
                <input readonly class="cat_plugin" type="text"  size="<?= strlen($data['plugin']) ?>" name="plugin" value="<?= $data['plugin'] ?>" />
            </label>
            <input type="submit" name="ModCatSubmit" value="<?= $LNG['L_MODIFY'] ?>" />
            <input type="submit" name="DelCatSubmit" value="<?= $LNG['L_DELETE'] ?>" onclick="return confirm('<?= $LNG['L_SURE'] ?>')" />
            <br/><label><?= $LNG['L_CATS_CATIMAGE'] ?>
                <input class="cat_image" type="text" maxlength="255" size="97" name="cat_image" value="<?= $data['image'] ?>" />
            </label>

        </div></form>
    <?php
    if ($data['TPL_FOOT'] == 1) {
        ?>
    </div>
<?php } ?>
