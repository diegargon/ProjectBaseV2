<?php
/**
 *  SimpleFrontend header menu template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="header-menu">
    <div class="header-menu-left">
        <?= isset($data['header_menu_elements_left']) ? $data['header_menu_elements_left'] : null; ?>
    </div>
    <div class="header-menu-right">
        <?= isset($data['header_menu_elements_right']) ? $data['header_menu_elements_right'] : null; ?>
        <!-- Drop down menu element -->
        <?php if (!empty($data['header_drop_menu_elements'])) { ?>
            <div class="nav_top">
                <div class="drop_down">

                    <button class="drop_btn" onclick="toggleMenu()" ><?= !empty($data['drop_menu_caption']) ? $data['drop_menu_caption'] : '&#9776;'; ?></button>
                    <div id="drop_content" class="drop_content">
                        <?= isset($data['header_drop_menu_elements']) ? $data['header_drop_menu_elements'] : null; ?>
                    </div>
                </div> <!-- Fin drop_down -->                       
            </div> <!-- fin nav_top -->
        <?php } ?>
        <!-- Dropdown End -->                     
    </div>
</div> 