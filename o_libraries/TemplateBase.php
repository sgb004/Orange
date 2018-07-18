<?php
require ABSPATH.O_LIBRARIES.'Twig/Autoloader.php';

/**
 * Permite imprimir un template del modulo, se sustituye todo el codigo para dar paso a Twig
 * @class Template
 * @version 3.0
 * @author @sgb004
 */
class TemplateBase{
	static public $twig;
	static public $url = '';
	static public $urlTemplate = '';
	static public $path = 'o_templates';
	static public $pathTemplate = '';

	/**
	 * Inicia Twig
	 */
	static function init(){
		if( !self::$twig ){
			self::$url = URL.self::$path.'/';
			self::$urlTemplate = self::$url.$GLOBALS['template'].'/';
			self::$path = ABSPATH.self::$path.'/';
			self::$pathTemplate = self::$path.$GLOBALS['template'].'/';

			Twig_Autoloader::register();
			$twigLoader = new Twig_Loader_Filesystem(self::$pathTemplate);
			self::$twig = new Twig_Environment($twigLoader, Template::getSettings());
			Template::customFunctions();
			self::$twig->addGlobal( 'url', URL );
			self::$twig->addGlobal( 'notices', new Notices() );
			self::$twig->addGlobal( 'session', false );
		}
	}

	/**
	 * Carga las opciones con las que funcionará twig
	 */
	protected static function getSettings(){
		return array();
	}

	/**
	 * Carga las funciones personalizadas
	 */
	protected static function customFunctions(){}

	/**
	 * Rederiza la vista
	 */
	static function render( $view, $options = array() ){
		echo self::$twig->render($view, $options);
	}

	/**
	 * Rederiza la vista, pero la devuelve en forma de string
	 */
	static function getView( $view, $options = array() ){
		return self::$twig->render($view, $options);
	}

	/**
	 * Imprime un array en un JSON
	 */
	static function renderJson( $data ){
		echo json_encode($data);
		exit;
	}

	/**
	 * Imprime una vista mostrando el tipico error 404
	 */
	static function renderError404(){
		self::render( '404.html.twig' );
	}
}
?>