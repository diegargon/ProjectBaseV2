<?php
/**
 *  SMBasic  admin userlist template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>

<tr>
    <td><a href='<?= $data['profile_url'] ?>'><?= $data['username'] ?></a></td>
    <td><?= $data['email'] ?></td>
    <td><?= $data['regdate'] ?></td>
    <td><?= $data['last_login'] ?> </td>
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