<?php
class TypeUtil{
	public static function renderAttrs( $attrs ){
		$a = '';
		foreach ($attrs as $key => $value) {
			if( is_string( $value ) ){
				$value = trim( $value );
				$a .= $key.'="'.$value.'" ';
			}
		}
		$e = trim( $a );
		return $a;
	}
}
?>