<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="news_article_container">
    <a href="<?= $data['url'] ?>">
        <article class="newsbox <?= $data['featured'] ? 'featured' : null ?>">
            <p class='p-small'><?= $data['date'] ?></p>
            <h3><?= $data['title'] ?></h3>
        </article>
    </a>
</div>

