<?php
//DATABASE

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASE', 'orange');

define('DB_CHARSET', 'utf8');

//DATE
date_default_timezone_set('America/Mexico_City');

//MAIL
define('ADMIN_MAIL', 'admin_mail@site.com');
define('NO_REPLY_MAIL', 'no-reply@site.com');
define('NO_REPLY_MAIL_NAME', 'Sitio');

//SITE URL
define('SITE_URL', 'http://site.com/');

//MODULES
//If you does not define a modules Orange try to find in re-content
//MODULES
$MODULES['form'] = array();
$MODULES['orange'] = array();
$MODULES['registrations'] = array();

//DIRS
define('O_SRCS','o_srcs/');
define('O_LIBRARIES','o_libraries/');

//SESSION NAME
define('SESSION_NAME', 'site');

//TEMPLATE
$template = 'default';
?>