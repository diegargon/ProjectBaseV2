<?php
/**
 *  SimpleFrontend template
 *
 *  Admin index Template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<p><?= $LNG['L_FR_INDEX_LAYOUTS'] ?></p>

<form id="form_index_layout" action="#" method="POST">
    <select name="index_layout">
        <?= $data['layouts_select'] ?>
    </select>
    <input type="submit" name="btnChangeLayout"/>
</form>
