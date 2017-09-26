<?php
class TokenController{
	static function get( $type = 0, $time = '-1 hour', $userId = 0 ){
		$token = new Token();
		$token->ip = $_SERVER['REMOTE_ADDR'];
		$token->userId = $userId;
		$token->setType( $type );
		$password = new Password();
		do{
			$token->tokenKey = $password->make( date('Y-m-d H:i:s').' '.uniqid() );
			$token->tokenKey = md5( $token->tokenKey );
		} while( $token->check() );
		$token->add();

		return $token->tokenKey;
	}

	static function check( $tokenKey, $type = 0, $time = '-1 hour', $userId = 0 ){
		$token = new Token();
		$token->ip = $_SERVER['REMOTE_ADDR'];
		$token->tokenKey = $tokenKey;
		$token->userId = $userId;
		$token->setType( $type );
		return $token->check();
	}

	static function delete( $tokenKey, $type = 0, $time = '-1 hour', $userId = 0 ){
		$token = new Token();
		$token->tokenKey = $tokenKey;
		$token->userId = $userId;
		$token->setType( $type );
		return $token->delete();
	}
}
?>