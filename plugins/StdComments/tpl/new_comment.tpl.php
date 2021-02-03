<?php
/**
 *  StdComments - new comment template
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage StdComments
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<p><a href='#new_comment_wrap' class="btnShow comment_box"><?= $LNG['L_SC_NEW_COMMENT'] ?></a></p>

<div id="new_comment_wrap" class="comment_box">
    <p><?= !empty($data['msg']) ? $data['msg'] : null ?></p>
    <form method="post" action="#" id="form_new_comment">
        <textarea id="new_comment" name="news_comment" ></textarea>
        <input id="btnSendNewComment" name="btnSendNewComment" type="submit" value="<?= $LNG['L_SEND'] ?>" />
    </form>
</div>