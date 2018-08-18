<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <br/>
    <?= isset($data['ADM_TABLE_TITLE']) ? $data['ADM_TABLE_TITLE'] : false ?>

    <div class='divTable'>
        <div class='divTableHeading'>
            <div class='divTableCell'><?= $LNG['L_ACL_LEVEL'] ?></div>
            <div class='divTableCell'><?= $LNG['L_ACL_ROLE_GROUP'] ?></div>
            <div class='divTableCell'><?= $LNG ['L_ACL_ROLE_TYPE'] ?></div>
            <div class='divTableCell'><?= $LNG ['L_ACL_ROLE_NAME'] ?></div>
            <div class='divTableCell'><?= $LNG ['L_ACL_ROLE_DESC'] ?></div>
            <div class='divTableCell'><?= $LNG ['L_ACL_ROLE_ACTIONS'] ?></div>
        </div>
    <?php } ?>       


    <?php if (isset($data['ACL_SPLIT'])) { ?>
        <div class='divTableRow divTableSep'>
            <div class='divTableCell'></div>
            <div class='divTableCell'></div>
            <div class='divTableCell'></div>
            <div class='divTableCell'></div>
            <div class='divTableCell'></div>
            <div class='divTableCell'></div>
        </div>
    <?php } ?>


    <div class='divTableRow'>       


        <div class='divTableCell'><?= $data['level'] ?></div>
        <div class='divTableCell'><?= $data['role_group'] ?></div>
        <div class='divTableCell'><?= $data['role_type'] ?></div>
        <div class='divTableCell'><?= $LNG[$data['role_name']] ?></div>
        <div class='divTableCell'><?= isset($LNG[$data['role_description']]) ? $LNG[$data['role_description']] : false; ?></div>
        <div class='divTableCell'>
            <form method="post" action="" id="roles_mng">   
                <input type='submit' name='btnRoleDelete' value='<?= $LNG['L_ACL_DELETE'] ?>' />
                <input type='hidden' name='role_id' value='<?= $data['role_id'] ?>' />
            </form>        
        </div>

    </div>


    <?php
    if ($data['TPL_CTRL'] == 0) {
        ?>
        <div class='divTableFoot'></div>
    </div>

    <?= isset($data['ACL_MSG']) ? "<p> {$data['ACL_MSG']} </p>" : false ?>
    <form method="post" action="" id="new_role">
        <div class="divTable">
            <div class='divTableRow'>
                <?= isset($data['ADM_TABLE_TH']) ? $data['ADM_TABLE_TH'] : false ?>
            </div>
            <div class='divTableRow'>
                <div class='divTableCell'><input name="r_level" type="text" maxlength="2" size="1" required /></div>
                <div class='divTableCell'><input name="r_group" type="text" maxlength="18" size="11" required /></div>
                <div class='divTableCell'><input name="r_type" type="text" maxlength="14" size="11" required /></div>
                <div class='divTableCell'><input name="r_name" type="text" maxlength="32" size="22" required /></div>
                <div class='divTableCell'><input name="r_description" type="text" maxlength="255" size="22" /></div>
                <div class='divTableCell'><input name="btnNewRole" type="submit" value='<?= $LNG['L_ACL_SEND'] ?>' /></div>
            </div>
        </div>
    </form>
    <br/>
<?php } ?>