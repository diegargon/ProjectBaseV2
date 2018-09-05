<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

?>

<div  class="clear bodysize page">
    <?php isset($tpldata['ADD_TOP_INDEX']) ? print $tpldata['ADD_TOP_INDEX'] : null; ?>    

    
    <section class="cols col3">
<?= isset($data['section_' . "1"]) ? $data['section_' . "1"] : null ?>
    </section>
    <section class="cols col3">
<?= isset($data['section_' . "2"]) ? $data['section_' . "2"] : null ?>
    </section>
    <section class="cols col3">
<?= isset($data['section_' . "3"]) ? $data['section_' . "3"] : null ?>
    </section>

    
    <?php isset($tpldata['ADD_BOTTOM_INDEX']) ? print $tpldata['ADD_BOTTOM_INDEX'] : null; ?>
</div>
