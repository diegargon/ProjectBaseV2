<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
?>
<meta name="news_keywords" content="<?= !empty($data['tags']) ? $data['tags'] : null ?> " />
<meta property="og:title" content="<?= $data['title'] ?>"/>
<meta property="og:url" content="<?= $data['url'] ?>"/>
<meta property="og:site_name" content="<?= $data['PAGE_TITLE'] ?>"/>
<meta property="og:type" content="article" />
<?php if (!empty($data['mainimage'])) { ?>
    <meta property="og:image" content="<?= $data['mainimage'] ?>" /> 
<?php } ?>
<meta property="og:description" content="<?= $data['lead'] ?> "/>