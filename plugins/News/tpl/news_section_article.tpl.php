<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="news_article_container">
    <a href="<?= $data['url'] ?>">
        <article class="newsbox <?= $data['featured'] ? 'featured' : null ?>">
            <?php
            if (isset($data['main_image']) && !empty($data['main_image'])) {
                ?>
                <img src="<?= $data['thumb_image'] ?>" alt=""/>
                <?php
            }
            ?>
            <p class='p-small'><?= $data['date'] ?></p>
            <h3><?= $data['title'] ?></h3>
            <p><?= $data['lead'] ?></p>
        </article>
    </a>
</div>

