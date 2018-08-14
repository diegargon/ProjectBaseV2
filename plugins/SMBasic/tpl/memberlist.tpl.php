<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<br/>
<h3><?= isset($data['ADM_TABLE_TITLE']) ? $data['ADM_TABLE_TITLE'] : false ?></h3>
<table class='memberlist_table'>
    <tr>
        <th><?= $LNG ['L_SM_USERNAME'] ?></th>
        <th><?= $LNG ['L_EMAIL'] ?></th>
        <th><?= $LNG ['L_SM_REGISTERED'] ?></th>
        <th><?= $LNG ['L_SM_LASTLOGIN'] ?></th>
        <th><?= $LNG ['L_SM_ACTIONS'] ?></th>
        <?= isset($data['ADM_TABLE_TH']) ? $data['ADM_TABLE_TH'] : false ?>        
    </tr>
    <?= isset($data['ADM_TABLE_ROW']) ? $data['ADM_TABLE_ROW'] : false ?>

</table>
<br/>