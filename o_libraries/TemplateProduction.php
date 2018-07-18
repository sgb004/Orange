<?php
/**
 * Clase que se usará en production
 * @class Template
 * @version 1.0
 * @author @sgb004
 */
class Template extends TemplateBase{
	static function init(){
		parent::init();
	}

	protected static function getSettings(){
		return array(
			'cache' => ABSPATH.'/o_cache/'
		);
	}

	protected static function customFunctions(){
		$twigFunction = new Twig_SimpleFunction('stylesheet', function($url, $file = ''){
			$url = $url.$file;
			$fileName = md5($url);
			echo file_get_contents( ABSPATH.'o_cache/'.$fileName.'.csso' );
		});
		self::$twig->addFunction( $twigFunction );
	}
}
?>