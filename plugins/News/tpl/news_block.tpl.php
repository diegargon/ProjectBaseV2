<?php
/**
 *  News - News block template
 *
 *  Headlines, lead, img
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="news_article_container">
    <a href="<?= $data['url'] ?>">
        <article class="newsbox <?= $data['featured'] ? 'featured' : null ?>">
            <?php
            if (isset($data['main_image']) && !empty($data['main_image'])) {
                ?>
                <div class="news_box_article_image"><img src="<?= $data['STATIC_SRV_URL'] . $data['thumb_image'] ?>" alt="<?= $data['thumb_image'] ?>"/></div>
                <?php
            }
            ?>
            <div class="news_box_article_content">
                <p class='p-small'><?= $data['date'] ?></p>
                <h3><?= $data['title'] ?></h3>
                <p><?= $data['lead'] ?></p>
            </div>
        </article>
    </a>    
</div>
