<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<br/>
<form method="post" action="" id="user_search">
    <input type="text" name="username"/>
    <input type="submit" name="btnSearchUser" value="<?= $LNG['L_SEARCH'] ?>" />
</form>

<?php if (!empty($data['option_groups'])) { ?>
    <p><?= $data['username'] ?></p>
    <form method='post' action='' id='form_user_groups'>
        <select class='option_groups' size='5' name='del_group_id'>
            <?= $data['option_groups'] ?>
        </select>
        <input type="hidden" name="username" value='<?= $data['username'] ?>' />
        <input type='submit' name='btnDeleteGroup' value='<?= $LNG['L_DELETE'] ?>' />     
    </form>    
<?php } else if (!empty($data['username'])) { ?>
    <p><?= $data['username'] ?></p>
    <p><?= $LNG['L_NO_GROUPS_FOUND'] ?></p>
<?php } ?>
<?= isset($data['MSG']) ? "<p> {$data['MSG']} </p>" : false ?>
<?php if (!empty($data['groups'])) { ?>
    <form method='post' action='' id='form_add_groups'>
        <select class='add_group' name='add_group_id'>
            <?= $data['groups'] ?>
        </select>
        <input type="hidden" name="username" value='<?= $data['username'] ?>' />    
        <input type='submit' name='btnAddGroup' value='<?= $LNG['L_ADD'] ?>' />     
    </form>
<?php } ?>
<br/>
