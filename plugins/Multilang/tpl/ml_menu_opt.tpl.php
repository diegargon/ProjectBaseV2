<?php
/**
 *  Multilang menu opt template file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage Multilang
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
/*
  <li class="nav_left">
  <a rel="nofollow" href="/<?= $cfg['FRIENDLY_URL'] ? $cfg['WEB_LANG'] . "/admin" : $cfg['CON_FILE'] . "?module=AdminBasic&page=adm&lang={$cfg['WEB_LANG']}";?>">Admin</a>
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