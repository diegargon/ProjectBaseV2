<?php
/**
 *  Plugin config template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage AdminBasic
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div id='divTable' class='divTable'>
        <div class='divTableHeadRow'>
            <div class="divTableHeadCell"></div>
            <div class="divTableHeadCell"></div>
            <div class="divTableHeadCell"></div>
        </div>
        <div class='divTableBody'>
        <?php } ?>
        <!-- REPEAT START -->    
        <div class='divTableRow'>            
            <div class='divTableCell'><?= $data['plugin'] ?></div>
            <div class='divTableCell'><?= $data['cfg_key'] ?></div>
            <div class='divTableCell'>
                <form method='post' action='#'>
                    <input type='text' maxlength='128' size='32' name='cfg_value' value='<?= $data['cfg_value'] ?>'/>
                    <input type='hidden' name='configID' value='<?= $data['cfg_id'] ?>'/>
                    <input type='submit' name='btnSubmitConfig'/>                
                </form>
            </div>            
        </div>
        <!-- REPEAT END -->
        <?php
        if ($data['TPL_FOOT'] == 1) {
            ?>
        </div> <!-- table body -->
        <div class='divTableFootRow'>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
        </div>    
    </div> <!-- divTable -->
<?php } ?>
