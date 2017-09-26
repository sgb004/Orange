<?php
/**
 * Busca la clase en los recursos srsc o en el directorio libreraies
 */
function oIncludeClases($class, $dir=''){
	$path = '';

	if($dir == ''){

		$path = ABSPATH.O_SRCS.MODULE.'/models/'.$class.'.php';

		if(!file_exists($path)){
			$path = ABSPATH.O_SRCS.MODULE.'/controllers/'.$class.'.php';

			if( !file_exists($path) ){
				global $MODULES;
				$modules = $MODULES;

				unset( $modules[MODULE] );

				foreach ($modules as $key => $value) {
					$path = ABSPATH.O_SRCS.$key.'/models/'.$class.'.php';
					if( file_exists($path) ){ break; }
					$path = ABSPATH.O_SRCS.$key.'/controllers/'.$class.'.php';
					if( file_exists($path) ){ break; }
					$path = ABSPATH.O_SRCS.$class.'.php';
					if( file_exists($path) ){ break; }
				}
				unset( $modules );
			}
		}
	}else{
		$path = ABSPATH.$dir.$class.'.php';
	}

	if(!file_exists($path)){$path = ABSPATH.'o_libraries/'.$class.'.php';}
	if(file_exists($path)){
		require_once $path;
	}
}
spl_autoload_register('oIncludeClases');

/**
 * Redirige a otra pagina
 */
function oRedirect($link=''){
	if($link != ''){
		header('Location: '.$link);
		exit;
	}
}
?>