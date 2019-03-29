<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<a href="<?= $data['url'] ?>">
    <div class="">
        <article class="newsbox <?= $data['featured'] ? "featured" : null ?>">
            <p class='p-small'><?= $data['date'] ?></p>
            <h3><?= $data['title'] ?></h3>
            <p><?= isset($data['lead']) ? $data['lead'] : null ?></p>
        </article>
    </div>
</a>    
