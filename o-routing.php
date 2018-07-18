<?php
$routes = array(
	'download' => array(
		'paths' => array('/download', '/download/', '/descargar', '/descargar/'),
		'm' => 'leads',
		'c' => 'Leads',
		'v' => 'download'
	),
	'thanks' => array(
		'paths' => array('/thanks', '/thanks/', '/gracias', '/gracias/'),
		'm' => 'leads',
		'c' => 'Leads',
		'v' => 'thanks'
	),
	'add' => array(
		'paths' => array('/add', '/add/'),
		'm' => 'leads',
		'c' => 'Leads',
		'v' => 'add'
	),
	'test_mailing' => array(
		'paths' => array('/test_mailing', '/test_mailing/'),
		'm' => 'leads',
		'c' => 'Leads',
		'v' => 'testMailing'
	),
	'home' => array(
		'paths' => array('/'),
		'm' => 'leads',
		'c' => 'Leads',
		'v' => 'index'
	)
)
?>