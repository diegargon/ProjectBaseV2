<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <br/>

    <div class='divTable'>
        <div class='divTableHeading'>
            <div class='divTableCell'><?= $LNG['L_GROUP_NAME'] ?></div>
            <div class='divTableCell'><?= $LNG['L_GROUP_DESC'] ?></div>
            <div class='divTableCell'><?= $LNG['L_GROUP_TYPE'] ?></div>
            <div class='divTableCell'><?= $LNG['L_GROUP_PLUGIN'] ?></div>
            <div class='divTableCell'><?= $LNG ['L_GROUP_ACTIONS'] ?></div>
        </div>
    <?php } ?>       

    <div class='divTableRow'>       


        <div class='divTableCell'><?= $data['group_name'] ?></div>
        <div class='divTableCell'><?= $data['group_desc'] ?></div>
        <div class='divTableCell'><?= $data['group_type'] ?></div>        
        <div class='divTableCell'><?= $data['plugin'] ?></div>
        <div class='divTableCell'>
            <form method="post" action="" id="group_mng">   
                <input type='submit' name='btnDeleteGroup' value='<?= $LNG['L_DELETE'] ?>' />
                <input type='hidden' name='group_id' value='<?= $data['group_id'] ?>' />
            </form>        
        </div>

    </div>


    <?php
    if ($data['TPL_CTRL'] == 0) {
        ?>
        <div class='divTableFoot'></div>
    </div>

    <?= isset($data['MSG']) ? "<p> {$data['MSG']} </p>" : false ?>
    <form method="post" action="" id="new_group">
        <div class="divTable">
            <div class='divTableRow'>

            </div>
            <div class='divTableRow'>
                <div class='divTableCell'><input name="group_name" type="text" maxlength="18" size="18" required /></div>
                <div class='divTableCell'><input name="group_desc" type="text" maxlength="255" size="22" /></div>
                <div class='divTableCell'><input name="btnNewGroup" type="submit" value='<?= $LNG['L_SEND'] ?>' /></div>
            </div>
        </div>
    </form>
    <br/>
<?php } ?>