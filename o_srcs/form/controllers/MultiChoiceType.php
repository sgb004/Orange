<?php
class MultiChoiceType extends RadioType{
	const NAME = 'MultiChoiceType';
	protected $type = 'checkbox';
	public $choices = array();
	public $choiceError = 'La opción seleccionada no se encuentra disponible.';

	public function validate(){
		$r = true;
		if( $this->required ){
			foreach ( $this->default as $value) {
				$r = array_key_exists( $value, $this->choices );
				if( !$r ){
					$this->addError( $this->choiceError );
					break;
				}
			}
		}
		return $r;
	}

	public function setDefault( $default ){
		$this->default = $default;
		if( is_array( $this->default ) ){
			$temp = array();
			foreach ($this->default as $value) {
				if( is_string( $value ) ){
					$temp[ $value ] = $value;
				}
			}
			$this->default = $temp;
		}else{
			$this->default = array( $this->default => $this->default );
		}
	}

	public function isEmpty(){
		$r = false;
		if( $this->required ){
			$r = empty( $this->default );
			if( $r ){
				$this->addError( $this->notice );
			}
		}
		return $r;
	}
}
?>