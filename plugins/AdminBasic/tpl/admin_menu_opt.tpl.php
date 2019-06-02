<?php
/**
 *  Menu options template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<li class="nav_left">
    <a rel="nofollow" href="/<?= $cfg['FRIENDLY_URL'] ? $cfg['WEB_LANG'] . "/admin" : $cfg['CON_FILE'] . "?module=AdminBasic&page=adm&lang={$cfg['WEB_LANG']}"; ?>">Admin</a>
</li>
