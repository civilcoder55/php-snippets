<?php


/* to make cookies work across sub-domains domain must = primary domain name started with dot
   this will work for www.example.com - test.example.com - anything.example.com */


/* for normal cookies */
setcookie('cookie-name', 'somedata', time() + 3600, '/', '.example.com');



/* for session cookies */
$session_cookie_params = session_get_cookie_params(); // get default session cookie params
$session_cookie_params["domain"] = '.example.com'; // override session cookie domain parameter
session_set_cookie_params($session_cookie_params); // set updated session cookie params
session_start(); // start the session




/* for session cookies (older php versions) */

$session_cookie_params = session_get_cookie_params(); // get default session cookie params

session_set_cookie_params(
    $session_cookie_params["lifetime"],
    $session_cookie_params["path"],
    '.example.com',
    $session_cookie_params["secure"],
    $session_cookie_params["httponly"]
); // set session cookie params like old but override domain
session_start(); // start the session
