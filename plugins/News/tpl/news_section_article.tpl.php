<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<a href="<?= $data['url'] ?>">
    <article class="newsbox <?= $data['featured'] ? "featured" : null ?>">
        <p class='p-small'><?= $data['date'] ?></p>
        <h3><?= $data['title'] ?></h3>
        <p><?= $data['lead'] ?></p>
    </article>
</a>

