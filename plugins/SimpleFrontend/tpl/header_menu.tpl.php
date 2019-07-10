<?php
/**
 *  SimpleFrontend header menu template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="header-menu">
    <div class="header-menu-left">
        <?= isset($data['header_menu_elements']) ? $data['header_menu_elements'] : null; ?>
    </div>
    <div class="header-menu-right">
        <!-- Drop down menu element -->
        <div class="nav_top">
            <div class="drop_down">
                <button class="drop_btn" onclick="toggleMenu()" ><?= !empty($data['drop_menu_caption']) ? $data['drop_menu_caption'] : '&#9776;'; ?></button>
                <div id="drop_content" class="drop_content">
                    <?= isset($data['header_drop_menu_elements']) ? $data['header_drop_menu_elements'] : null; ?>
                </div>
            </div> <!-- Fin drop_down -->                       
        </div> <!-- fin nav_top -->
        <!-- Dropdown End -->                     
    </div>
</div> 