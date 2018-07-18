<?php
class PasswordType extends TextType{
	const NAME = 'PasswordType';
	protected $type = 'password';
	public $minlength = 8;

	public function validate(){
		$isValid = parent::validate();

		if( $this->default != '' ){
			if( !preg_match('/[0-9]/', $this->default) ){
				$this->addError( 'Por favor, incluye números en la contraseña.' );
				$isValid = false;
			}

			if( !preg_match('/[a-zA-Z]/', $this->default) ){
				$this->addError( 'Por favor, incluye letras en la contraseña.' );
				$isValid = false; 
			}

			if( preg_match('/^[0-9a-zA-Z]+$/', $this->default) ){
				$this->addError( 'Por favor, incluye algún carácter especial en la contraseña.' );
				$isValid = false; 
			}
		}

		return $isValid;
	}

}
?>