<?php
/**
 *  MiniEditor KeyLinks template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div><?= $data['title'] ?></div>
    <div id="divTable" class="divTable">
        <div class="divTableHeadRow">
            <div class="divTableHeadCell"></div>
        </div>
        <div class='divTableBody'>
        <?php } ?>
        <!-- REPEAT START -->
        <div class='divTableRow'>            
            <div class="divTableCell">
                <form method="post" action="#">
                    <input type="text" maxlength="255" size="16" name="keyword" value="<?= $data['extra'] ?>"/>
                    <input type="text" maxlength="255" size="64" name="link" value="<?= $data['link'] ?>"/>
                    <input type="hidden" name="link_id" value="<?= $data['link_id'] ?>"/>
                    <input type="submit" value="<?= $data['btn_caption']?>" name="btnSubmitKeyLink"/>
                    <input type="submit" value="<?= $LNG['L_EDITOR_DEL_KEYLINKS']?>" name="btnDeleteKeyLink"/>
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
        </div>    
    </div> <!-- divTable -->
<?php } ?>