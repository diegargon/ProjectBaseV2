<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="nav_top">
    <form id="search" action="<?= $data['searchUrl'] ?>" method="get">
        <input id="searchTextInput" type="text" name="q" value="" />
    </form>
</div>