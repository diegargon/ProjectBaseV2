<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div id='divTable' class='divTable'>
    <?php } ?>

    <!-- REPEAT START -->  

    <form method='post' action='#'>
        <div id='divRow1' class='divRow'>
            <div class='divCell divCellFixed'><?= $data['plugin'] ?></div>
            <div class='divCell divCellLeft'><?= $data['cfg_key'] ?></div>
            <div class='divCell divCellRight'><input type='text' maxlength='128' size='32' name='cfg_value' value='<?= $data['cfg_value'] ?>'/>
                <input type='hidden' name='configID' value='<?= $data['cfg_id'] ?>'/>
                <input type='submit' name='btnSubmitConfig'/>                
            </div>
        </div>
    </form>

    <!-- REPEAT END -->

    <?php
    if ($data['TPL_CTRL'] == 0) {
        ?>
    </div>
<?php } ?>
