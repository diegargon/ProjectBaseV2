<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>

<div  class="clear bodysize page">
    <?= isset($tpldata['ADD_TOP_INDEX']) ? $tpldata['ADD_TOP_INDEX'] : null; ?>    
    <div class="cols col3">
        <?= isset($data['section_' . "1"]) ? $data['section_' . "1"] : null ?>
    </div>
    <div class="cols col3">
        <?= isset($data['section_' . "2"]) ? $data['section_' . "2"] : null ?>
    </div>
    <div class="cols col3">
        <?= isset($data['section_' . "3"]) ? $data['section_' . "3"] : null ?>
    </div>
    <?= isset($tpldata['ADD_BOTTOM_INDEX']) ? $tpldata['ADD_BOTTOM_INDEX'] : null; ?>
</div>
