<?php
$path = __DIR__;
if($path === '__DIR__'){
	$path = getcwd();
}

$_SERVER = array(
	'DOCUMENT_ROOT' => $path,
	'REQUEST_URI' => isset( $argv[1] ) ? $argv[1] : '/send_crm',
	'HTTP_HOST' => 'localhost',
	'REQUEST_METHOD' => ''
);

require_once 'index.php';
?>