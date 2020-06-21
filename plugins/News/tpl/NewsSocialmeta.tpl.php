<?php
/**
 *  News - News meta tag social templates
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<meta name="news_keywords" content="<?= !empty($data['tags']) ? $data['tags'] : null ?> " />
<meta property="og:locale" content="<?= $cfg['WEB_LANG'] ?>">
<meta property="og:title" content="<?= $data['title'] ?>"/>
<meta property="og:type" content="article" />
<meta property="og:url" content="<?= $data['url'] ?>"/>
<meta property="og:site_name" content="<?= $cfg['WEB_NAME'] ?>"/>
<!-- <meta property="article:tag" content="">-->
<meta property="article:section" content="<?= $data['cat_name'] ?>">
<meta property="article:published_time" content="<?= $data['created'] ?>">
<meta property="article:modified_time" content="<?= $data['last_edited'] ?>">
<meta property="og:updated_time" content="<?= $data['last_edited'] ?>">
<?php if (!empty($data['mainimage'])) { ?>
    <meta property="og:image" content="<?= $data['mainimage'] ?>" /> 
    <meta property="og:image:secure" content="<?= $data['mainimage'] ?>" /> 
    <meta property="og:image:width" content="400">
<?php } ?>
<meta property="og:description" content="<?= $data['lead'] ?> "/>