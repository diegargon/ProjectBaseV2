<?php
/**
 *  Main body template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">   
    <?= !empty($tpldata['ADD_TOP_ADMIN']) ? $tpldata['ADD_TOP_ADMIN'] : false ?>		
    <div id="admin_container">
        <div id="admin_tabs">
            <ul>
                <?php
                if ($tpldata['ADMIN_TAB_ACTIVE'] == 1) {
                    ?>
                    <li class="tab_active"><a href="<?= $data['url'] ?>&admtab=1" ><?= $LNG['L_GENERAL'] ?></a></li>
                <?php } else { ?>
                    <li class=""><a href="<?= $data['url'] ?>&admtab=1" ><?= $LNG['L_GENERAL'] ?></a></li>
                <?php } ?>

                <?= !empty($tpldata['ADD_ADMIN_MENU']) ? $tpldata['ADD_ADMIN_MENU'] : false ?>		
            </ul>
        </div>
        <div id="admin_content">

            <aside>
                <ul>
                    <?= $tpldata['ADM_ASIDE_MENU_OPT'] ?>
                </ul>    
            </aside>            
            <div id="admin_opt_content">
                <section>    
                    <?= !empty($tpldata['ADM_SECTION_CONTENT']) ? $tpldata['ADM_SECTION_CONTENT'] : false ?>
                </section>
            </div>
            <?= !empty($tpldata['ADD_ADMIN_CONTENT']) ? $tpldata['ADD_ADMIN_CONTENT'] : false ?>
        </div>
    </div>
    <?= !empty($tpldata['ADD_BOTTOM_ADMIN']) ? $tpldata['ADD_BOTTOM_ADMIN'] : false ?>            
</div>