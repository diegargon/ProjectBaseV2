<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
?>
<div class="block_conf">
    <label for='featured'><?= $LNG['L_NEWS_FEATURED'] ?></label><input id='featured' type='checkbox' name='block_conf[featured]'/>
    <label for='frontpage'><?= $LNG['L_NEWS_FRONTPAGE'] ?></label><input id='frontpage' type='checkbox' name='block_conf[frontpage]'/>
    <label for='childs'><?= $LNG['L_NEWS_CHILDS'] ?></label><input id='childs' type='checkbox' name='block_conf[childs]'/>
    <br/>
    <label for='title'><?= $LNG['L_NEWS_BLOCK_TITLE'] ?></label><input maxlength='25' size='25' id='block_title' type='text' name='block_conf[block_title]'/>
    <p><span><?= $LNG['L_NEWS_CATEGORY'] ?></span>
        <?= $data['categories_select'] ?>

        <span><?= $LNG['L_NEWS_DISPLAY_TYPE'] ?></span>
        <select name='block_conf[news_type]' id='news_type'>
            <option value='headlines'><?= $LNG['L_NEWS_TITLES'] ?></option>
            <option value='lead'><?= $LNG['L_NEWS_TITLESLEAD'] ?></option>
            <option value='full'><?= $LNG['L_NEWS_FULLNEWS'] ?></option>
        </select>
    </p>
    <p>
        <?php if (isset($data['lang_select'])) { ?>
            <span><?= $LNG['L_NEWS_LANG'] ?></span>
            <?= $data['lang_select'] ?>
        <?php } ?>
        <span><?= $LNG['L_NEWS_LIMITS'] ?></span>
        <select name='block_conf[limits]'>
            <?= $data['limits'] ?>
        </select>
    </p>
</div>