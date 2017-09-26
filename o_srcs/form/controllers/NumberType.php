<?php
class NumberType extends TextType{
	const NAME = 'TextType';
	protected $type = 'text';
	public $attrs = array( 'autocomplete' => 'off', 'data-format' => 'only-digits');
	public $patternError = 'Por favor escriba solo números';

	public function validate(){
		$isValid = true;

		if( $this->default != '' ){
			if( !preg_match('/^[0-9]+$/', $this->default) ){
				$this->addError( $this->patternError );
				$isValid = false;
			}

			$isValid = $this->validateMinMax( $isValid );
		}
		return $isValid;
	}
}
?>