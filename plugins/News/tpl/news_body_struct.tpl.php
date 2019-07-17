<?php
/**
 *  News - News body struct template
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<script type="application/ld+json">
    {
    "@context": "http://schema.org",
    "@type": "Article",
    "headline": "<?= !empty($data['title']) ? $data['title'] : null ?>",
    <?php if (!empty($data['ITEM_MAINIMAGE'])) { ?>
        "image": {
        "@type": "imageObject",
        "width" : "600",
        "height": "400",
        "url": "<?= $data['ITEM_MAINIMAGE'] ?>"
        },
    <?php } ?>
    "datePublished": "<?= !empty($data['ITEM_CREATED']) ? $data['ITEM_CREATED'] : null ?>",
    "dateModified": "<?= !empty($data['ITEM_MODIFIED']) ? $data['ITEM_MODIFIED'] : null ?>",
    <?= $data['ITEM_SECTIONS'] ?>
    "creator": "<?= $data['author'] ?>",
    "author": {
        "@type": "Person",
        "name": "<?= $data['author'] ?>"
    },
    "articleBody": "<?= $data['lead'] ?>",
    "publisher": {
        "@type": "Organization",
        "name": "<?= $cfg['WEB_NAME'] ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?= $cfg['WEB_LOGO'] ?>"
        }
    },    
    "mainEntityOfPage": "True"
    }
</script>
