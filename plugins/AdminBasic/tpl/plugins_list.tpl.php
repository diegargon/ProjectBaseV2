<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
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

<div style="border:1px solid gray;width:200px;height:200px;overflow:auto;margin:2px;padding:5px;float:left">
    <form action='' method='post' id='formPlugin'> 
        <p>
            <span><?= $data['plugin_name'] ?></span> <?= $data['version'] ?><br/>
            <?= $LNG['L_PL_PROVIDE'] . $data['provide'] ?> <br/>
            <?= $data['DEPENDS'] . "<br/>" . $data['OPTIONAL'] . "<br/>" . $data['CONFLICTS'] ?>
            <?php if ($data['installed'] == 1) { ?>
                <span style="color:red">Installed</span><br/>
            <?php } ?>            
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
