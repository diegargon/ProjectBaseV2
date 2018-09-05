<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<p><?= $LNG['L_FR_INDEX_LAYOUTS'] ?></p>

<form action="#" method="POST">
    <select name="index_layout">
        <?= $data['layouts_select'] ?>
    </select>
    <input type="submit" name="btnChangeLayout"/>
</form>
