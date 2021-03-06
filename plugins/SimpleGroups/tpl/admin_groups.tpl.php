<?php
/**
 *  SimpleGroups - Adm admin groups
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleGroups
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <br/>
    <div class='divTable'>
        <div class='divTableHeadRow'>
            <div class='divTableHeadCell'><?= $LNG['L_GROUP_NAME'] ?></div>
            <div class='divTableHeadCell'><?= $LNG['L_GROUP_DESC'] ?></div>
            <div class='divTableHeadCell'><?= $LNG['L_GROUP_TYPE'] ?></div>
            <div class='divTableHeadCell'><?= $LNG['L_GROUP_PLUGIN'] ?></div>
            <div class='divTableHeadCell'><?= $LNG ['L_GROUP_ACTIONS'] ?></div>
        </div>
        <div class='divTableBody'>
        <?php } ?>       
        <!-- REPEAT -->
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
        <!-- REPEAT -->
        <?php
        if ($data['TPL_FOOT']) {
            ?>
        </div>
        <div class='divTableFootRow'>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>               
        </div>
    </div>

    <?= isset($data['MSG']) ? "<p> {$data['MSG']} </p>" : false ?>
    <form method="post" action="" id="new_group">
        <div class="divTable">
            <div class='divTableRow'></div>
            <div class='divTableRow'>
                <div class='divTableCell'><input name="group_name" type="text" maxlength="18" size="18" required /></div>
                <div class='divTableCell'><input name="group_desc" type="text" maxlength="255" size="22" /></div>
                <div class='divTableCell'><input name="btnNewGroup" type="submit" value='<?= $LNG['L_SEND'] ?>' /></div>
            </div>
        </div>
    </form>
    <br/>
<?php } ?>
