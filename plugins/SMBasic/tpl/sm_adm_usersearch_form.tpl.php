<?php
/**
 *  SMBasic admin usersearch form template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SMBasic
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>

<form action='' method='post'>
    <label for='glob'><?= $LNG['L_SM_GLOB'] ?>: </label><input type='checkbox' name='posted_glob' id='glob' value='1' />
    <label for='email'><?= $LNG['L_EMAIL'] ?>: </label><input type='checkbox' name='posted_email' id='email' value='1' />
    <input type='text' maxlength="<?= $cfg['smbasic_max_username'] ?>" minlength="<?= $cfg['smbasic_min_username'] ?>" name='search_user' id='search_user' required />
    <input type='submit' name='btnSearchUser' id='btnSearchUser' />
</form>
<br/>