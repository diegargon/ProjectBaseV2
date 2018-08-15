<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SimpleACL_init() {
    global $acl_auth;
    print_debug("SimpleACL Inititated", "PLUGIN_LOAD");

    includePluginFiles("SimpleACL");

    empty($acl_auth) ? $acl_auth = new ACL : false;
}
