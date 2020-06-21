<?php
/**
 *  SimpleFrontend template
 *
 *  Admin Layout Template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<p><?= $LNG['L_FR_INDEX_LAYOUTS'] ?></p>
<form id="form_layouts_<?= $data['page_name'] ?>" action="#" method="POST">
    <input type="hidden" name="page" value="<?= $data['page_name'] ?>"/>
    <p style="font-weight: bold;"><?= $data['page_name'] ?></p>
    <select name="admin_layout">
        <?= $data['layouts_select'] ?>
    </select>
    <input type="submit" name="btnChangeLayout"/>
</form>
