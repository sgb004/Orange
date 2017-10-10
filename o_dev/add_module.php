<?php
defined( 'ABSPATH' ) or exit( 'Por favor use o-dev.' );

if( isset( $argv[2] ) ){
	$modulePath = ABSPATH.O_SRCS.$argv[2];
	if( file_exists( $modulePath ) ){
		echo 'El modulo ya existe, por favor seleccione otro nombre.'."\r\n";
	}else{
		if( mkdir( $modulePath ) ){
			/* REGISTRO DEL CONTROLADOR */
			$controllerPath = $modulePath.'/controllers';
			if( mkdir( $controllerPath ) ){
				$controllerName = explode( '_', $argv[2] );
				foreach ( $controllerName as $key => $value) {
					$controllerName[ $key ] = ucfirst( $value );
				}
				$controllerName = implode( '', $controllerName ).'Controller';

				$controller = '<?php
class '.$controllerName.'{

}
?>';
				$controllerPath = $controllerPath.'/'.$controllerName.'.php';
				if( file_put_contents( $controllerPath, $controller ) ){
					echo 'Se registro el controllador '.$controllerName.'.'."\r\n";
				}else{
					echo 'Ocurrio un error al registrar el controlador '.$controllerName.'.'."\r\n";
				}
			}else{
				echo 'Ocurrio un error al registrar la carpeta controllers.'."\r\n";
			}

			/* REGISTRO DEL MODELO */
			$modelPath = $modulePath.'/models';
			if( mkdir( $modelPath ) ){
				$modelName = explode( '_', $argv[2] );
				foreach ( $modelName as $key => $value) {
					$modelName[ $key ] = ucfirst( $value );
				}
				$modelName = implode( '', $modelName ).'Model';

				$model = '<?php
class '.$modelName.'{
	private $db;

	function __contruct(){
		$this->db = new DB();
	}
}
?>';
				$modelPath = $modelPath.'/'.$modelName.'.php';
				if( file_put_contents( $modelPath, $model ) ){
					echo 'Se registro el modelo '.$modelName.'.'."\r\n";
				}else{
					echo 'Ocurrio un error al registrar el modelo '.$modelName.'.'."\r\n";
				}
			}else{
				echo 'Ocurrio un error al registrar la carpeta models.'."\r\n";
			}

			$routing = '<?php
$routes = array(

);
?>';	
			
			/* REGISTRO DEL MODULO EN EL TEMPLATE */
			if( isset( $argv[3] ) ){
				require_once ABSPATH.O_LIBRARIES.'Template.php';
				$view = ( $argv[3] === 'true' ) ? $argv[2] : $argv[3];
				$viewPath = ABSPATH.Template::$path.'/'.$template.'/'.$view;

				$existsViewContainer = file_exists( $viewPath );

				if( $existsViewContainer ){
					echo 'Ya existe el directorio '.$viewPath.'.'."\r\n";
				}else{
					$existsViewContainer = mkdir( $viewPath );
					if( !$existsViewContainer ){
						echo 'Ocurrio un error al registrar la directorio '.$viewPath.'.'."\r\n";
					}
				}

				if( $existsViewContainer ){	
					$viewPath = $viewPath.'/'.$view.'.html.twig';
					$existsView = file_exists( $viewPath );

					if( $existsView ){
						echo 'Ya existe la vista '.$viewPath."\r\n";
					}else if( file_put_contents( $viewPath, ' ' ) ){
						echo 'Se registro la vista '.$viewPath."\r\n";
					}else{
						echo 'Ocurrio un error al registrar la vista '.$viewPath.'.'."\r\n";
					}
				}
			}

			/* REGISTRO DEL ARCHIVO DE RUTAS */
			$routingPath = $modulePath.'/routing.php';
			if( file_put_contents( $routingPath, $routing ) ){
				echo 'Se registro el archivo de rutas.'."\r\n";
			}else{
				echo 'Ocurrio un error al registrar el archivo de rutas.'."\r\n";
			}

			/* REGISTRO DEL MODULO EN EL ARCHIVO DE CONFIGURACION */
			if( isset( $MODULES[ $argv[2] ] ) ){
				echo 'El modulo ya esta registrado en el archivo o-config.php.'."\r\n";
			}else{
				$seccionModulesFind = false;
				$lines = file( ABSPATH.'o-config.php', FILE_IGNORE_NEW_LINES );
				foreach ( $lines as $lineKey => $line ) {
					if( $seccionModulesFind && $line == "" ){
						$lines[$lineKey] = $lines[$lineKey].'$MODULES['."'".$argv[2]."'".'] = array();'."\r\n";
						break;
					}

					if( $line == '//MODULES' ){
						$seccionModulesFind = true;
					}
				}

				if( file_put_contents( ABSPATH.'o-config.php', implode( "\n", $lines ) ) ){
					echo 'Se registro el modulo en el archivo o-config.php.'."\r\n";
				}else{
					echo 'Ocurrio un error al registrar el modulo en el archivo o-config.php.'."\r\n";
				}
			}

			echo 'Se registro el modulo '.$argv[2].'.'."\r\n";
		}else{
			echo 'Ocurrio un error al registrar el modulo.'."\r\n";
		}
	}
}else{
	echo 'Seleccione un nombre para el nuevo modulo.';
}
?>