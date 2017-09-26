<?php
//Orange v0.6
session_start();

$path = __DIR__;
if($path === '__DIR__'){
	$path = getcwd();
}

define('ABSPATH', $path.'/');

//LOAD ORANGE
require ABSPATH.'o_libraries/orange.php';
?>