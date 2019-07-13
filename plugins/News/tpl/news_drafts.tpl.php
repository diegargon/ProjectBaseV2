<?php
/** News drafts template file
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage  News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div  class="bodysize page">
    <?php } ?>    
    <p><a href="<?= $data['draft_url'] ?>"><?= $data['title'] ?></a></p>
    <?php if ($data['TPL_FOOT'] == 1) { ?>
    </div>
<?php } ?>