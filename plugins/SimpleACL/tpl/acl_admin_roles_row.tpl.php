<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
?>
<?php if (isset($data['ACL_SPLIT'])) { ?>
    <tr class='acl_table_sep'><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php } ?>
<tr>
    <td><?= $data['level'] ?></td>
    <td><?= $data['role_group'] ?></td>
    <td><?= $data['role_type'] ?></td>
    <td><?= $data['role_name'] ?></td>
    <td><?= $data['role_description'] ?></td>
    <td>
        <input type='submit' name='btnRoleDelete' value='<?= $LNG['L_ACL_DELETE'] ?>' />
        <input type='hidden' name='role_id' value='<?= $data['role_id'] ?>' />
    </td>
</tr>