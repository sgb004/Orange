<?php
class NumberType extends TextType{
	const NAME = 'TextType';
	protected $type = 'text';
	public $attrs = array( 'autocomplete' => 'off', 'data-format' => 'only-digits');
	public $patternError = 'Por favor escriba solo números';
	public $minValue = 'infinite';
	public $maxValue = 'infinite';
	public $minValueError = 'El valor debe ser igual o mayor a ';
	public $maxValueError = 'El valor debe ser igual o menor a ';

	public function validate(){
		$isValid = true;

		if( $this->default != '' ){
			if( !preg_match('/^[0-9]+$/', $this->default) ){
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

	public function validateRange(){
		$isValid = true;

		if( $this->minValue != 'infinite' ){
			if( $this->default < $this->minValue ){
				$this->addError( $this->minValueError.$this->minValue );
				$isValid = false;
			}
		}

		if( $this->maxValue != 'infinite' ){
			if( $this->default > $this->maxValue ){
				$this->addError( $this->maxValueError.$this->maxValue );
				$isValid = false;
			}
		}

		return $isValid;
	}

	public function render(){
		$a = parent::render();
		if( $this->minValue != 'infinite' ){
			$a['attrs']['data-minvalue'] = $this->minValue;
			$a['attrs']['data-minvalue-error'] = ( $this->minValueError == '') ? $this->notice : $this->minValueError;
		}
		if( $this->maxValue != 'infinite' ){
			$a['attrs']['data-maxValue'] = $this->maxValue;
			$a['attrs']['data-maxvalue-error'] = ( $this->maxValueError == '') ? $this->notice : $this->maxValueError;
		}
		return $a;
	}
}
?>