<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<br/>
<?= isset($data['ADM_TABLE_TITLE']) ? $data['ADM_TABLE_TITLE'] : false ?>        
<?= isset($data['ACL_MSG']) ? "<p> {$data['ACL_MSG']} </p>" : false ?>
<form method="post" action="" id="user_search">
    <input type="text" name="username"/>
    <input type="submit" name="btnSearchUser" value="<?= $LNG['L_ACL_SEARCH'] ?>" />
</form>

<?php if (!empty($data['option_roles'])) { ?>
    <p><?= $data['username'] ?></p>
    <form method='post' action='' id='form_user_roles'>
        <select class='option_roles' size='5' name='del_role_id'>
            <?= $data['option_roles'] ?>
        </select>
        <input type="hidden" name="username" value='<?= $data['username'] ?>' />
        <input type='submit' name='btnDeleteRole' value='<?= $LNG['L_ACL_DELETE'] ?>' />     
    </form>    
<?php } else if (!empty($data['username'])) { ?>
    <p><?= $data['username'] ?></p>
    <p><?= $LNG['L_ACL_NO_ROLES_FOUND'] ?></p>
<?php } ?>
<?php if (!empty($data['roles'])) { ?>
    <form method='post' action='' id='form_add_roles'>
        <select class='add_role' name='add_role_id'>
            <?= $data['roles'] ?>
        </select>
        <input type="hidden" name="username" value='<?= $data['username'] ?>' />    
        <input type='submit' name='btnAddRole' value='<?= $LNG['L_ACL_ADD'] ?>' />     
    </form>
<?php } ?>
<br/>