<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <?php
    isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : null;
    ?>
    <div class="news_row">
        <?= isset($data['section_1']) ? $data['section_1'] : null; ?>
    </div>
    <div class="news_row">
        <?= isset($data['section_2']) ? $data['section_2'] : null; ?>
    </div>
    <div class="news_row">        
        <?= isset($data['section_3']) ? $data['section_3'] : null; ?>
    </div>        
    <?php
    isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : null;
    ?>    
</div>
