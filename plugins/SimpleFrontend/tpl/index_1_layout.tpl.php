<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?= isset($tpldata['ADD_TOP_INDEX']) ? $tpldata['ADD_TOP_INDEX'] : null; ?>        
    <section class="cols col1">
        <?= isset($data['section_' . "1"]) ? $data['section_' . "1"] : null ?>
    </section>
    <?= isset($tpldata['ADD_BOTTOM_INDEX']) ? $tpldata['ADD_BOTTOM_INDEX'] : null; ?>
</div>
