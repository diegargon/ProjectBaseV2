<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

/*
  <li class="nav_left">
  <a rel="nofollow" href="/<?php $cfg['FRIENDLY_URL'] ? print $cfg['WEB_LANG'] . "/admin" : print $cfg['CON_FILE'] . "?module=AdminBasic&page=adm&lang={$cfg['WEB_LANG']}";?>">Admin</a>
  </li>
 */

if (!empty($data['TPL_FIRST'])) {
    ?>    
    <li class="nav_right">
        <form action="#" method="post">
            <select name="choose_lang" id='choose_lang' onchange="this.form.submit()">
                <?php
            }
            ?>
            <option <?= $data['selected'] ?> value="<?= $data['iso_code'] ?>"><?= $data['lang_name'] ?></option>
            <?php
            if (!empty($data['TPL_LAST'])) {
                ?>
            </select>
        </form>
    </li>   
    <?php
}
?>