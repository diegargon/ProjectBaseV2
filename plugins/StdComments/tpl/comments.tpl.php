<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if (!empty($data['TPL_FIRST'])) {
    ?>
    <div id="comments" class="comment_box">
        <section>
            <h2><?= $LNG['L_SC_COMMENTS'] ?></h2>
        <?php } ?>
        <div class="comment">
            <span class="avatar">
                <a href="<?= $data['p_url'] ?>"><img width="35"  src="<?= $data['avatar'] ?>" alt="" /></a>
            </span>
            <span class="c_author"><a href="<?= $data['p_url'] ?>"><?= $data['username'] ?></a></span>
            <span class="c_date"><?= $data['date'] ?></span>
            <?= !empty($data['COMMENT_EXTRA']) ? $data['COMMENT_EXTRA'] : null; ?>
            <p class="comment_body"><?= $data['comment'] ?></p>
            <?= !empty($data['COMMENT_POST_MESSAGE_EXTRA']) ? $data['COMMENT_POST_MESSAGE_EXTRA'] : null; ?>                    
        </div>
        <?php if (!empty($data['TPL_LAST'])) { ?>
        </section>
    </div>
    <?php
}
