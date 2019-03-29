<?php

/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
/* CFG */
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