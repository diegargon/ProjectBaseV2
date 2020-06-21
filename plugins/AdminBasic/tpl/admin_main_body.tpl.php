<?php
/**
 *  Main body template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">   
    <?= !empty($tpldata['ADD_ADMIN_TOP']) ? $tpldata['ADD_ADMIN_TOP'] : false ?>		
    <div id="admin_container">
        <div id="admin_tabs">
            <ul>
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
    <?= !empty($tpldata['ADD_ADMIN_BOTTOM']) ? $tpldata['ADD_ADMIN_BOTTOM'] : false ?>            
</div>