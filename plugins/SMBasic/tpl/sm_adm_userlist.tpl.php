<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<tr>
    <td><a href='<?= $data['profile_url'] ?>'><?= $data['username'] ?></a></td>
    <td><?= $data['email'] ?></td>
    <td><?= $tUtil->format_date($data['regdate']) ?></td>
    <td><?= $tUtil->format_date($data['last_login']) ?> </td>
    <td>
        <form action='' method='post'>
            <input type='hidden' name='member_uid'  value='<?= $data['uid'] ?>' />
            <input type='hidden' name='member_disable' value='<?= $data['disable'] ?>' />
            <input type='submit' name='btnDeleteSubmit' id='btnDeleteSubmit' value='<?= $LNG['L_SM_DELETE'] ?>' />
            <?php if ($data['disable'] > 0) { ?>
                <input type='submit' name='btnDisableSubmit' class='btnSubmit' value='<?= $LNG['L_SM_ACTIVATE'] ?>' />
            <?php } else { ?>
                <input type='submit' name='btnDisableSubmit' class='btnSubmit' value='<?= $LNG['L_SM_DISABLE'] ?>' />
            <?php } ?>
        </form>
    </td>
</tr>