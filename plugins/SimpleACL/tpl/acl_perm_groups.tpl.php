<?php
/**
 *  SimpleACL - groups perm template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleACL
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>

<?= isset($data['MSG']) ? "<p> {$data['MSG']} </p>" : false ?>

<div class="divTable">
    <div class="divTableBody">
        <div class='divTableRow'>
        </div>
        <div class='divTableRow'>            
            <div class='divTableCell'>Seleccione grupo</div>
        </div>
        <div class='divTableRow'>
            <form method="post" action="" id="select_group">
                <div class='divTableCell'>
                    <select name="group_selected" onchange="this.form.submit()"><?= $data['select_groups'] ?></select>
                </div>
            </form>
        </div>
        <div class='divTableRow'>            
            <div class='divTableCell'>Permisos</div>
        </div>
        <div class='divTableRow'>
            <form method="post" action="" id="delete_perm"> 
                <div class='divTableCell'><select name="perm_id"><?= $data['select_group_perms'] ?></select></div>
                <div class='divTableCell'>
                    <input name="group_id" type="hidden" value='<?= $data['group_selection'] ?>' />
                    <input name="btnDelPerm" type="submit" value='<?= $LNG['L_DELETE'] ?>' />

                </div>
            </form>
        </div>
        <div class='divTableRow'>
            <div class='divTableCell'>Añadir Permisos</div>
        </div>
        <div class='divTableRow'>
            <form method="post" action="" id="new_perm">        
                <div class='divTableCell'><select name="perm_id"><?= $data['select_perms'] ?></select></div>
                <div class='divTableCell'>
                    <input name="btnNewPerm" type="submit" value='<?= $LNG['L_ADD'] ?>' />
                    <input name="group_id" type="hidden" value='<?= $data['group_selection'] ?>' />                    
                </div>
            </form>
        </div>

    </div>
</div>
<br/>
