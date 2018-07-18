<?php
/**
 * ORANGE
 * @author @sgb004
 * @version 0.5a
 */
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$path = __DIR__;
if($path === '__DIR__'){
	$path = getcwd();
}

define('ABSPATH', $path.'/');

//LOAD ORANGE
try{
	require ABSPATH.'o_libraries/orange.php';
}catch (Throwable $t){
	echo '<pre>';
	print_r( $t->getMessage() );
	echo '</pre>';
}
?>