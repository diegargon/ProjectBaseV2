<?php
/**
 *  SimpleFrontend template
 *
 *  Index Layout 2 Template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <?= isset($tpldata['ADD_TOP_INDEX']) ? $tpldata['ADD_TOP_INDEX'] : null; ?>        
    <section class="cols col2">
        <?= isset($data['section_' . "1"]) ? $data['section_' . "1"] : null ?>
    </section>
    <section class="cols col2">
        <?= isset($data['section_' . "2"]) ? $data['section_' . "2"] : null ?>
    </section>    
    <?= isset($tpldata['ADD_BOTTOM_INDEX']) ? $tpldata['ADD_BOTTOM_INDEX'] : null; ?>
</div>
