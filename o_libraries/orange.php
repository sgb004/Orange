<?php
/**
 * ORANGE
 * @author @sgb004
 * @version 0.5a
 */
require ABSPATH.'o-config.php';
require ABSPATH.O_LIBRARIES.'check_request_content.php';
require ABSPATH.O_LIBRARIES.'functions.php';
require ABSPATH.O_LIBRARIES.'Notices.php';

function orange(){
	require ABSPATH.'o-routing.php';

	$url = str_replace('\\', '/', ABSPATH);
	$url = substr_replace($url, '', 0, strlen($_SERVER['DOCUMENT_ROOT']));
	$url = str_replace('//', '/', $url);

	$route = preg_replace('/'.str_replace($url, '/', '\/').'/', '', $_SERVER['REQUEST_URI'], 1);
	$route = '/'.$route;
	$route = substr_replace($route, '', 0, strlen($url));

	$url = $_SERVER['HTTP_HOST'].'/'.$url;
	$url = str_replace('//', '/', $url);
	$url = 'http://'.$url;

	$route = preg_replace('/\\?.*/', '', $route);
	$route = '/'.$route;
	//VERIFICAR
	$route = str_replace('//', '/', $route);

	//NO BORRAR
	//[\/]+[hello]+[\/]+[\s\S]+[\/]+[prueba]+[\/]+[\s\S]
	//[\/]+[hello]+[\/]+[[\s\S]]+[\/]+[prueba]+[\/]+[[\s\S]]
	//([{]+[\s\S]+[}])
			
	define('URL', $url);
	define('ROUTE', $route);

	$callViewParams = array();

	//echo '<pre>';
	//print_r( $url );
	//echo '</pre>';

	//echo '<pre>';
	//print_r( $route );
	//echo '</pre>';

	//Obtiene el modulo, el controlador y la vista visitada
	foreach ($routes as $route) {
		if(! is_array($route['paths']) ) { $route['paths'] = array($route['paths']); } 

		foreach ($route['paths'] as $path) {

			if($path === '/'){
				if( isset($route['m']) ){ $module = $route['m']; }
				if( isset($route['c']) ){ $controller = $route['c']; }
				if( isset($route['v']) ){ $view = $route['v']; }
			} else {
				$path = $pattern = explode('/', $path);

				foreach ($pattern as &$pattern_string) {
					$pattern_string = preg_replace('/[{]+[\s\S]+[}]/', '[\s\S]', $pattern_string);
				}

				$pattern = implode(')+[\/]+(', $pattern);

				$pattern = substr($pattern, 2);
				$pattern = '/'.$pattern.')/';
				$pattern = str_replace('([', '[', $pattern);
				$pattern = str_replace('])', ']', $pattern);
				$pattern = str_replace('+[]', '', $pattern);

				if( preg_match($pattern, ROUTE) ){

					if( isset($route['m']) ){ $module = $route['m']; }
					if( isset($route['c']) ){ $controller = $route['c']; }
					if( isset($route['v']) ){ $view = $route['v']; }

					$route = explode('/', ROUTE);

					foreach ($path as $path_key => $pathString) {
						if( !preg_match('/[{]+[\s\S]+[}]/', $pathString) ) continue;
						$callViewParams[] = $route[$path_key];
					}

					break 2; //Opcional
				}
			}
		}
	}

	//echo '<pre>';
	//print_r( $module );
	//echo '</pre>';

	//echo '<pre>';
	//print_r( $module );
	//echo '</pre>';
	
	//echo '<pre>';
	//print_r( $controller );
	//echo '</pre>';

	//echo '<pre>';
	//print_r( $view );
	//echo '</pre>';

	define('MODULE', $module);
	define('CONTROLLER', $controller);
	define('VIEW', $view);

	if( IS_DEBUG ){
		require ABSPATH.O_LIBRARIES.'TemplateDebug.php';
	}else{
		require ABSPATH.O_LIBRARIES.'TemplateProduction.php';
	}

	//Init template
	Template::init();

	//Init notices;
	Notices::init();

	//loading controller
	$controller = ucwords($controller);
	$controller .= 'Controller';
	$controllerPath = ABSPATH.O_SRCS.MODULE.'/controllers/'.$controller.'.php';

	if( file_exists( $controllerPath ) ){
		require_once ABSPATH.O_SRCS.MODULE.'/controllers/'.$controller.'.php';
		//loading check view in controller
		//$controller = $module_str.'\\'.$controller;
		$controller = new $controller;

		//Call a view in controller
		$view = $view.'Action';
		if(method_exists($controller,$view)){
			$reflection = new ReflectionMethod($controller, $view);
			if ($reflection->isPublic()) {
				call_user_func_array(array($controller, $view), $callViewParams);
				//$controller->$view();
			}else{
				Template::renderError404();
			}
		}else{
			Template::renderError404();
		}
	}else{
		Template::renderError404();
	}
}

$MODULES['orange'] = array();
orange();
?>