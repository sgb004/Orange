<?php
$routes = array(
	'download' => array(
		'paths' => array('/download', '/download/', '/descargar', '/descargar/'),
		'm' => 'registrations',
		'c' => 'registrations',
		'v' => 'download'
	),
	'register' => array(
		'paths' => array('/register', '/register/'),
		'm' => 'registrations',
		'c' => 'registrations',
		'v' => 'register'
	),
	'home' => array(
		'paths' => array('/'),
		'm' => 'registrations',
		'c' => 'registrations',
		'v' => 'index'
	)
)
?>