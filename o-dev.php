<?php
if( isset( $argv[1] ) ){
	$path = __DIR__;
	if($path === '__DIR__'){
		$path = getcwd();
	}

	define('ABSPATH', $path.'/');

	require_once ABSPATH.'o-config.php';
	
	switch ( $argv[1] ){
		case 'update':
			require_once ABSPATH.'o_dev/update.php';
	}
}
?>
