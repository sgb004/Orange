<?php
class TokenType extends TextType{
	const NAME = 'TokenType';
	protected $type = 'hidden';

	public function __construct( $prop = array() ){
		parent::__construct( $prop );
	}

	public function render(){
		$token = new TokenController();
		$this->default = $token->get();
		$r = parent::render();
		return $r;
	}

	public function isEmpty(){
		$r = ( trim($this->default) == '' );
		if( $r ){
			$this->addError( 'Token is a required field' );
		}
		return $r;
	} 

	public function validate(){
		$token = new TokenController();
		$r = $token->check( $this->default );
		if( !$r ){
			$this->addError( 'The session is expired, please, reload the page' );
		}
		return $r;
	}
}