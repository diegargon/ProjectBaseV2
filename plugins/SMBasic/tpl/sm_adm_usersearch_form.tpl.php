<?php
/*
 *  Copyright @ 2017 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<form action='' method='post'>
    <label for='glob'><?= $LNG['L_SM_GLOB'] ?>: </label><input type='checkbox' name='posted_glob' id='glob' value='1' />
    <label for='email'><?= $LNG['L_EMAIL'] ?>: </label><input type='checkbox' name='posted_email' id='email' value='1' />
    <input type='text' maxlength='32' name='search_user' id='search_user' required />
    <input type='submit' name='btnSearchUser' id='btnSearchUser' />
</form>
<br/>