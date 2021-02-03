<?php
/**
 *  SimpleFrontend template
 *
 *  Admin Layout Template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<p><?= $LNG['L_FR_INDEX_LAYOUTS'] ?></p>
<form id="form_layouts" action="#" method="POST">      
    <select name="admin_layout">
        <?= $data['layouts_select'] ?>
    </select>
    <input type="submit" name="btnChangeLayout"/>
</form>
