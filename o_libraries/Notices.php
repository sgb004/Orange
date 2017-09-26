<?php
/**
 * Carga la lista de mensajes
 * @class Notices
 * @version 1.0
 */
class Notices{
	static $msgs = array();

	function __construct(){
		if( isset( $_SESSION[ SESSION_NAME.'_messages' ] ) ){
			self::$msgs = $_SESSION[ SESSION_NAME.'_messages' ];
		}
	}

	static function init(){
		
	}

	static function add( $notice, $type ){
		self::$msgs[] = array( 'msg' => $notice, 'type' => $type );
		$_SESSION[ SESSION_NAME.'_messages' ] = self::$msgs;
	}

	static function get(){
		$msgs = array();
		if( isset( $_SESSION[ SESSION_NAME.'_messages' ] ) ){
			$msgs = $_SESSION[ SESSION_NAME.'_messages' ];
		}
		return $msgs;
	}

	static function clear(){
		$_SESSION[ SESSION_NAME.'_messages' ] = array();
	}
	
	/*
	static public $notices = array();
	static public $file = '';
	static public $list = array();

	static function init(){
		self::$file = ABSPATH.O_SRCS.MODULE.'/notices.json';
	}

	static function clear(){
		self::$notices = array();
		self::$list = array();
	}

	static function load(){
		if(empty(self::$list)){
			self::$list = file_get_contents(self::$file);
			self::$list = json_decode(self::$list, true);
		}
	}

	static function get($notice){
		self::load();
		return isset(self::$list[$notice]) ? self::$list[$notice] : '';
	}

	static function get_json($new_notices = array()){
		self::load();

		$temp = array();
		foreach ($new_notices as $key) {
			$temp[$key] = '';
		}
		$new_notices = $temp;

		$temp = self::$list + $new_notices;
		$notices = array_intersect_key($temp, $new_notices);
		unset($temp);

		$notices = json_encode($notices);
		return $notices;
	}
	*/
}
?>