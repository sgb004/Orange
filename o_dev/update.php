<?php
defined( 'ABSPATH' ) or exit( 'Por favor use o-dev.' );
echo 'Actualizando rutas.'."\r\n";
$routesFull = '<?php $routes = array(';
$MODULES = array_reverse( $MODULES );
foreach ( $MODULES as $moduleName => $attrs ) {
	$routes = ABSPATH.O_SRCS.$moduleName.'/routing.php';
	if( file_exists( $routes ) ){
		require_once $routes;

		foreach ($routes as $routeKey => $routeAttrs) {
			$routesFull .= "'".$routeKey."' => array( 'paths' => array( '".implode( "', '", $routeAttrs['paths'] )."' ), 'm' => '".$routeAttrs['m']."', 'c' => '".$routeAttrs['c']."', 'v' => '".$routeAttrs['v']."' ), ";
		}
	}
}
$routesFull .= '); ?>';
file_put_contents( ABSPATH.'o-routing.php' , $routesFull );
echo 'Rutas actualizadas.'."\r\n";
?>