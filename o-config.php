<?php
//DATABASE

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASE', '');

define('DB_CHARSET', 'utf8');

//DATE
date_default_timezone_set('America/Mexico_City');

//MAIL
define('ADMIN_MAIL', '');
define('NO_REPLY_MAIL', '');
define('NO_REPLY_MAIL_NAME', '');

//SITE URL
define('SITE_URL', 'http://sgb004.com/');

//MODULES
$MODULES['orange'] = array();
$MODULES['form'] = array();
$MODULES['leads'] = array();

//DIRS
define('O_SRCS','o_srcs/');
define('O_LIBRARIES','o_libraries/');

//SESSION NAME
define('SESSION_NAME', '');

//DEBUG
define('IS_DEBUG', true);

//TEMPLATE
$template = 'default';
?>