<?php
/**
 *  News - News section article template
 *
 *  Only headlines
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="news_article_section_container">
    <a href="<?= $data['url'] ?>">
        <article class="section_newsbox <?= $data['featured'] ? 'featured' : null ?>">
            <p class='p-small'><?= $data['date'] ?></p>
            <h4><?= $data['title'] ?></h4>
        </article>
    </a>
</div>

