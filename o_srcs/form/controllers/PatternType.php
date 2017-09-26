<?php
class PatternType extends TextType{
	const NAME = 'RegExpType';
	protected $type = 'text';
	public $attrs = array( 'autocomplete' => 'off' );

	public function validate(){
		$isValid = true;
		if( $this->default != '' ){
			$pattern = '/'.$this->pattern.'/';
			if( !preg_match( $pattern, $this->default ) ){
				$notice = ( $this->patternError == '' ) ? $this->notice : $this->patternError;
				$this->addError( $notice );
				$isValid = false;
			}
		}
		return $isValid;
	}
}
?>