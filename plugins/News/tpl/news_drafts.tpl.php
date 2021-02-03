<?php
/** News drafts template file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage  News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div  class="bodysize page">
        <div class="divTable">
            <div class="divTableHeadRow">
                <div class="divTableHeadCell"><?= $LNG['L_NEWS_NEWS_LINK'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_NEWS_NPAGE'] ?></div>
            </div>
            <div class="divTableBody">
            <?php } ?>  

            <div class="divTableRow">                
                <div class="divTableCell"><a href="<?= $data['draft_url'] ?>"><?= $data['title'] ?></a></div>
                <div class="divTableCell"><?= $data['page'] ?></div>
            </div>            
            <?php if ($data['TPL_FOOT'] == 1) { ?>
            </div>
            <div class="divTableFootRow">
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
            </div>
        </div>
    </div>
<?php } ?>