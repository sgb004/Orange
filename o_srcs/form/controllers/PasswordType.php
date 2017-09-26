<?php
class PasswordType extends TextType{
	const NAME = 'PasswordType';
	protected $type = 'password';
	public $minlength = 8;

	public function validate(){
		$isValid = parent::validate();

		if( !preg_match('/[0-9]/', $this->default) ){
			$this->addError( 'Please, include numbers in your password' );
			$isValid = false;
		}

		if( !preg_match('/[a-zA-Z]/', $this->default) ){
			$this->addError( 'Please, include letters in your password' );
			$isValid = false; 
		}

		if( preg_match('/^[0-9a-zA-Z]+$/', $this->default) ){
			$this->addError( 'Please, include some special character in your password' );
			$isValid = false; 
		}

		return $isValid;
	}

}
?>