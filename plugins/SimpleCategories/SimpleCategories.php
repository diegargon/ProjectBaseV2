<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleCategories_init() {
    global $ctgs, $cfg, $LNG, $db, $ml;
    print_debug("SimpleCategories initiated", "PLUGIN_LOAD");

    !defined('MULTILANG') || !isset($ml) ? $ml = null : null;

    includePluginFiles("SimpleCategories");
    //$tpl->getCSS_filePath("SimpleCategories");
    //$tpl->getCSS_filePath("SimpleCategories", "SimpleCategories-mobile");
    !isset($ctgs) ? $ctgs = new Categories($cfg, $LNG, $db, $ml) : null;
}
