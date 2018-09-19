<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!empty($data['TPL_FIRST'])) {
    ?>
    <div id="comments">
        <section>
            <h2><?= $LNG['L_SC_COMMENTS'] ?></h2>
        <?php } ?>
        <div class="comment">
            <span class="avatar">
                <a href="<?= $data['p_url'] ?>"><img width="35"  src="<?= $data['avatar'] ?>" alt="" /></a>
            </span>

            <span class="c_author"><a href="<?= $data['p_url'] ?>"><?= $data['username'] ?></a></span>
            <span class="c_date"><?= $tUtil->format_date($data['date']) ?></span>
            <?php !empty($data['COMMENT_EXTRA']) ? print $data['COMMENT_EXTRA'] : false; ?>
            <p class="comment_body"><?= $data['comment'] ?></p>
            <?php !empty($data['COMMENT_POST_MESSAGE_EXTRA']) ? print $data['COMMENT_POST_MESSAGE_EXTRA'] : false; ?>                    
        </div>
        <?php if (!empty($data['TPL_LAST'])) { ?>
        </section>
    </div>
    <?php
}