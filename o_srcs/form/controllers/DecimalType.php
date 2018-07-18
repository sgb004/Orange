<?php
class DecimalType extends NumberType{
	const NAME = 'TextType';
	protected $type = 'text';
	public $attrs = array( 'autocomplete' => 'off', 'data-format' => 'only-digits');
	public $decimals = 2;

	public function __construct( $prop = array() ){
		$this->pattern = '^[0-9]+([.]+[0-9]{'.$this->decimals.'}|[0-9])$';
		$this->patternError = ( $this->decimals == 1 ) ? 'Por favor escriba solo números y 1 decimal.' : 'Por favor escriba solo números y '.$this->decimals.' decimales.';
		parent::__construct( $prop );
	}

	public function validate(){
		$isValid = true;

		if( $this->default != '' ){
			$pattern = '/'.$this->pattern.'/';

			if( !preg_match('/^[0-9]+$/', $this->default) && !preg_match($pattern, $this->default) ){
				$this->addError( $this->patternError );
				$isValid = false;
			}

			$isValid = $this->validateMinMax( $isValid );

			if( $isValid ){
				$isValid = $this->validateRange();
			}
		}
		return $isValid;
	}
}
?>