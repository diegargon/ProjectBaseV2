<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div  class="clear bodysize page">
        <?php
        isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : null;
    }
    ?>
    <div class="cols col<?= $data['NUM_SECTIONS'] ?>">
        <?= $data[$data['TPL_CTRL']] ?>
    </div>
    <?php
    if ($data['TPL_FOOT']) {
        isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : null;
        ?>    
    </div>
<?php } ?>