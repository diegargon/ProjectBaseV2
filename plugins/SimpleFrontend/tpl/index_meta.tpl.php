<?php
/**
 *  Index page meta tag social templates
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<meta property="og:locale" content="<?= $cfg['WEB_LANG'] ?>">
<meta property="og:title" content="<?= $cfg['WEB_NAME'] ?>"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="<?= $cfg['WEB_URL'] ?>"/>
<meta property="og:site_name" content="<?= $cfg['WEB_NAME'] ?>"/>
<meta property="og:description" content="<?= $cfg['WEB_DESC'] ?> "/>