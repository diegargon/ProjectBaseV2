<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($cfg['ITS_BOT'] && $cfg['INCLUDE_DATA_STRUCTURE']){
?>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Article",
  "headline": "<?= !empty($data['title']) ? $data['title'] : null ?>",
  "image": {
        "@type": "imageObject",
        "width" : "600",
        "height": "400",
        "url": "<?= !empty($data['ITEM_MAINIMAGE']) ? $data['ITEM_MAINIMAGE'] : null ?>"
    },
  "datePublished": "<?= !empty($data['ITEM_CREATED']) ? $data['ITEM_CREATED'] : null ?>",
  "dateModified": "<?= !empty($data['ITEM_MODIFIED']) ? $data['ITEM_MODIFIED'] : null ?>",
  <?= $data['ITEM_SECTIONS'] ?>
  "creator": "<?= $data['author'] ?>",
  "author": "<?= $data['author'] ?>",
  "articleBody": "<?= $data['lead'] ?>",
  "publisher": {
    "@type": "Organization",
    "logo": {
      "@type": "ImageObject",
      "url": "<?= $cfg['WEB_LOGO'] ?>"
    },
    "name": "<?= $cfg['WEB_NAME'] ?>"
    },
  "mainEntityOfPage": "True"
}
</script>
<?php } ?>