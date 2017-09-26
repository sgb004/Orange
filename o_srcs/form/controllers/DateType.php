<?php
class DateType extends TextType{
	const NAME = 'DateType';
	protected $type = 'text';
	public $attrs = array( 'autocomplete' => 'off' );
	public $validator;

	public function validate(){
		$isValid = true;
		if( $this->default != '' ){
			$pattern = '/'.$this->pattern.'/';
			$isValid = preg_match( $pattern, $this->default );

			if( $isValid ){
				$validator = $this->validator;
				if( is_callable( $validator ) ){
					$isValid = $validator( $this->default );
				}
			}

			if( !$isValid ){
				$notice = ( $this->patternError == '' ) ? $this->notice : $this->patternError;
				$this->addError( $notice );
			}
		}
		return $isValid;
	}
}
?>