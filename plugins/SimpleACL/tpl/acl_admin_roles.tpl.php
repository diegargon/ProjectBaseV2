<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<br/>
<?= isset($data['ADM_TABLE_TITLE']) ? $data['ADM_TABLE_TITLE'] : false ?>
<?= isset($data['ACL_MSG']) ? "<p> {$data['ACL_MSG']} </p>" : false ?>

<form method="post" action="" id="roles_mng">  
    <table class='acl_table'>
        <tr>
            <th><?= $LNG['L_ACL_LEVEL'] ?></th>
            <th><?= $LNG['L_ACL_ROLE_GROUP'] ?></th>
            <th><?= $LNG ['L_ACL_ROLE_TYPE'] ?></th>
            <th><?= $LNG ['L_ACL_ROLE_NAME'] ?></th>
            <th><?= $LNG ['L_ACL_ROLE_DESC'] ?></th>
            <th><?= $LNG ['L_ACL_ROLE_ACTIONS'] ?></th>
        </tr>
        <?= isset($data['ADM_TABLE_ROW']) ? $data['ADM_TABLE_ROW'] : false ?>
    </table>
</form>
<form method="post" action="" id="new_role">
    <table class="acl_table">
        <tr>
            <?= isset($data['ADM_TABLE_TH']) ? $data['ADM_TABLE_TH'] : false ?>
        </tr>
        <tr>
            <td><input name="r_level" type="text" maxlength="2" size="1" required /></td>
            <td><input name="r_group" type="text" maxlength="18" size="11" required /></td>
            <td><input name="r_type" type="text" maxlength="14" size="11" required /></td>
            <td><input name="r_name" type="text" maxlength="32" size="22" required /></td>
            <td><input name="r_description" type="text" maxlength="255" size="22" /></td>
            <td><input name="btnNewRole" type="submit" value='<?= $LNG['L_ACL_SEND'] ?>' /></td>
        </tr>
    </table>
</form>
<br/>