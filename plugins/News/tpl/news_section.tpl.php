<?php
/**
 *  News - News section template
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <?php
    isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : null;
    ?>
    <div class="news_section_container">
        <div class="news_row">
            <?= isset($data['news']) ? $data['news'] : null; ?>
        </div>
        <div class="news_section_row">
            <?= isset($data['section_1']) ? $data['section_1'] : null; ?>
        </div>
    </div>
    <?php
    isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : null;
    ?>    
</div>
