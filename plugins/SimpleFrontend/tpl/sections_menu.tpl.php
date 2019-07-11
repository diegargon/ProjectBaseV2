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
<nav id="sections_nav">
    <ul>
        <?= $data['sections_menu'] ?>
    </ul>
</nav>
<?php
if (!empty($data['sections_sub_menu'])) {
    ?>
    <nav id="sections_submenu">
        <ul>
            <?= $data['sections_sub_menu'] ?>
        </ul>
    </nav>
<?php } ?>