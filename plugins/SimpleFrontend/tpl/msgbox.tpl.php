<?php
/**
 *  SimpleFrontend template
 *
 *  Message Box Template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<!-- MSG BOX -->
<div  class="bodysize page">   
    <div class="standard_box">
        <h1><?= $data['box_title'] ?></h1> 
        <p class="p_center_big"><?= $data['box_msg'] ?></p>
        <p class="p_center_medium">
            <a href="<?= $data['box_backlink'] ?>"><?= $data['box_backlink_title'] ?></a>
        </p>           
    </div>
</div>
<!-- FIN MSG BOX -->