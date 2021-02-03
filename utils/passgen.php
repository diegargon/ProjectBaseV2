<?php

/**
 *  Password generator
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage Utils
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
$use_salt = 1;
$salt = 5565;
$password = "adminadmin";
$enc_pass = "";

/* CODE */

if ($use_salt) {
    $enc_pass = hash('sha512', md5($password . $salt));
} else {
    $enc_pass = hash('sha512', $password);
}

echo $enc_pass . "\n";
