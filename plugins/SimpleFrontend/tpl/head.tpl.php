<?php
/**
 *  SimpleFrontend template
 *
 *  Head Template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<!DOCTYPE html>
<html <?= isset($cfg['WEB_DIR']) ? 'dir="' . $cfg['WEB_DIR'] . '"' : null ?> <?= isset($cfg['WEB_LANG']) ? 'lang="' . $cfg['WEB_LANG'] . '"' : null ?> > 
    <head>
        <?= isset($cfg['CHARSET']) ? '<meta charset="' . $cfg['CHARSET'] . '" />' : null; ?>
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1" />
        <title><?= isset($cfg['PAGE_TITLE']) ? $cfg['PAGE_TITLE'] : $cfg['WEB_NAME'] ?></title>
        <meta name="robots" content="all" />
        <meta name="referrer" content="origin-when-crossorigin" />
        <meta name="distribution" content="global"  />
        <meta name="resource-type" content="document"  />
        <meta name="theme-color" content="#ffffff" />
        <?php
        isset($cfg['WEB_LANG']) ? print '<meta name="language" content="' . $cfg['WEB_LANG'] . '" />' : null;
        isset($cfg['PAGE_KEYWORDS']) ? print '<meta name="keywords" content="' . $cfg['PAGE_KEYWORDS'] . '" /> ' : null;
        isset($cfg['PAGE_KEYWORDS']) ? print '<meta name="news_keywords" content="' . $cfg['PAGE_KEYWORDS'] . '" /> ' : null;
        isset($cfg['PAGE_DESC']) ? print '<meta name="description" content="' . $cfg['PAGE_DESC'] . '" />' : null;
        isset($cfg['PAGE_AUTHOR']) ? print '<meta name="author" content="' . $cfg['PAGE_AUTHOR'] . '" />' : null;
        isset($cfg['WEB_NAME']) ? print '<meta name="organization" content="' . $cfg['WEB_NAME'] . '" />' . "\n" : null;
        isset($tpldata['HEAD']) ? print $tpldata['HEAD'] : null;
        isset($tpldata['META']) ? print $tpldata['META'] : null;
        ?>
        <link rel="dns-prefetch" href="<?= $cfg['STATIC_SRV_URL'] ?>" />
        <?php isset($tpldata['LINK']) ? print $tpldata['LINK'] : null; ?>
        <link rel="icon" type="image/png" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon-16x16.png" sizes="16x16" />                
        <link rel="icon" href="<?= $cfg['STATIC_SRV_URL'] ?>favicon.ico" type='image/x-icon' />   
        <?php
        isset($tpldata['SCRIPTS_TOP']) ? print $tpldata['SCRIPTS_TOP'] : null;
        ?>
    </head>
    <body id="Top">    