<?php
/**
 *  Plugin list template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <form action='#' method='post' id='formRescan'>
        <p><input type='submit' name='btnReScan'  value='<?= $LNG['L_PL_RESCAN'] ?>'></p>
    </form>
    <hr/>

<?php } ?>

<!--- REPEAT START-->    

<div style="border:1px solid gray;width:200px;height:300px;overflow:auto;margin:2px;padding:5px;float:left">
    <form action='' method='post' id='formPlugin'> 
        <p>
            <span><?= $data['plugin_name'] ?></span> <?= $data['version'] ?><br/>
            <?= $LNG['L_PL_PROVIDE'] . $data['provide'] ?> <br/>
            <?= $data['DEPENDS'] . "<br/>" . $data['OPTIONAL'] . "<br/>" . $data['CONFLICTS'] ?>
            <?php if ($data['installed'] == 1) { ?>
                <span style="color:blue">Installed -</span>
            <?php } ?>
            <?php if (!empty($data['started'])) { ?>
                <span style="color:blue">Started -</span> 
            <?php } else { ?>
                <span style="color:green">Stopped -</span>
            <?php } ?>
            <?php if (!empty($data['fail'])) { ?>
                <span style="color:red">Start Fail -</span>
            <?php } ?>
            <br/>                
            <?= $data['BUTTOMS_CODE'] ?>       
        </p>
        <input type='hidden' name='plugin_id' value='<?= $data['plugin_id'] ?>'/>
    </form>
</div>
<!--- REPEAT END -->
<?php
if ($data['TPL_FOOT'] == 1) {
    ?>

<?php } ?>
