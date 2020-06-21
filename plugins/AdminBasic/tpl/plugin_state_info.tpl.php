<?php
/**
 *  Plugin state info template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="adm_plug_state">
    <span><?= $LNG['L_NAME'] . $LNG['L_SEP'] ?></span>
    <?= $data['plugin_name'] ?>
    <br/>
    <span><?= $LNG['L_PL_VERSION'] . $LNG['L_SEP'] ?></span>
    <?= $data['version'] ?>
    <br/>
    <span><?= $LNG['L_PL_ENABLE'] . $LNG['L_SEP'] ?></span>
    <?= $data['enabled'] ? $LNG['L_PL_YES'] : $LNG['L_PL_NO'] ?>  
    <br/>
    <span><?= $LNG['L_PL_INSTALLED'] . $LNG['L_SEP'] ?></span>
    <?= $data['installed'] ? $LNG['L_PL_YES'] : $LNG['L_PL_NO'] ?>
    <br/>
    <span><?= $LNG['L_PL_PROVIDE'] . $LNG['L_SEP'] ?> </span>
    <?= $data['provide'] ?>
    <br/>
    <span><?= $LNG['L_PL_PRIORITY'] . $LNG['L_SEP'] ?> </span>
    <?= $data['priority'] ?>
    <br/>
    <span><?= $LNG['L_PL_DEPEND'] . $LNG['L_SEP'] ?> </span><br/>
    <?= $data ['depends'] ?>
    <br/>
    <span><?= $LNG['L_PL_AUTOSTART'] . $LNG['L_SEP'] ?></span>
    <?= $data['autostart'] ? $LNG['L_PL_YES'] : $LNG['L_PL_NO'] ?>
    <br/>
    <span><?= $LNG['L_PL_OPTIONAL'] . $LNG['L_SEP'] ?> </span><br/>
    <?= $data['optional'] ?>
    <br/>
    <span><?= $LNG['L_PL_CONFLICTS'] . $LNG['L_SEP'] ?> </span><br/>
    <?= $data['conflicts'] ?>
    <br/>

</div>