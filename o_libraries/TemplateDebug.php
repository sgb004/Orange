<?php
/**
 * Clase que se usara en depuracion
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
			'cache' => false
			//'cache' => ABSPATH.'/o_cache/'
		);
	}

	protected static function customFunctions(){
		$twigFunction = new Twig_SimpleFunction('stylesheet', function($url, $file = ''){
			$url = $url.$file;
			$u = explode('/', $url);
			array_pop( $u );
			$u = implode('/', $u);
			$u .= '/';

			$fileName = md5($url);
			$styleContent = file_get_contents( $url );
			$styleContent = str_replace('../', $u.'../', $styleContent);
			$styleContent = str_replace('../'.$u.'..', '../..', $styleContent);

			$styleContent = '<style type="text/css">'.$styleContent.'</style>';
			ob_start("ob_gzhandler");
			echo $styleContent;
			file_put_contents( ABSPATH.'o_cache/'.$fileName.'.csso', ob_get_clean());
			echo '<link rel="stylesheet" type="text/css" href="'.$url.'">';
		});
		self::$twig->addFunction( $twigFunction );
	}
}
?>