<?php
class TextType{
	const NAME = 'TextType';
	protected $type = 'text';
	public $label = '';
	public $id = '';
	public $name = '';
	public $attrs = array( 'autocomplete' => 'off');
	public $default = '';
	public $required = true;
	public $typeAttrs = array();
	public $labelAttrs = array();
	public $isRender = false;
	public $minlength = 0;
	public $maxlength = 0;
	public $pattern = '';
	public $patternError = '';
	public $notice = 'Este campo es requerido.';
	public $notlengthError = 'El número de caracteres que debe tener este campo es de ';
	public $minlengthError = 'El número mínimo de caracteres es de ';
	public $maxlengthError = 'El número máximo de caracteres es de ';
	private $errors = array();

	public function __construct( $prop = array() ){
		$_prop = get_object_vars( $this );
		unset( $_prop['type'] );
		foreach ($_prop as $key => $value) {
			if( isset($prop[$key]) ){
				if( is_array( $this->$key ) && is_array( $prop[$key] ) ){
					$this->$key = $this->$key + $prop[$key];
				}else{
					$this->$key = $prop[$key];
				}
			}
		}
	}

	public function render(){
		$a = array(
			'type' => $this->type,
			'name' => $this->name,
			'id' => $this->id,
			'value' => $this->default,
			'required' => $this->required,
			'attrs' => $this->attrs
		);

		if( $this->minlength > 0 ){
			$a['attrs']['minlength'] = $this->minlength;
		}

		if( $this->maxlength > 0 ){
			$a['attrs']['maxlength'] = $this->maxlength;
		}

		if( $this->pattern != '' ){
			$a['attrs']['data-pattern'] = $this->pattern; 
			$a['attrs']['data-pattern-error'] = ( $this->pattern != '' && $this->patternError == '') ? $this->notice : $this->patternError;
		}

		return $a;
	}

	public function setDefault( $default ){
		$this->default = trim( $default );
	}

	public function isEmpty(){
		$r = false;
		if( $this->required ){
			$r = ( trim($this->default) == '' );
			if( $r ){
				$this->addError( $this->notice );
			}
		}
		return $r;
	}

	public function validate(){
		$isValid = true;
		
		if( $this->default != '' ){
			if( $this->pattern != '' ){
				$pattern = '/'.$this->pattern.'/';
				if( !preg_match( $pattern, $this->default ) && $this->default != '' ){
					$this->addError( $this->patternError );
					$isValid = false;
				}
			}

			$isValid = $this->validateMinMax( $isValid );
		}

		return $isValid;
	}

	protected function validateMinMax( $isValid ){
		if( $this->minlength > 0 || $this->maxlength > 0 ){
			$size = strlen( $this->default );
			
			if( $this->minlength > 0 && $this->maxlength > 0 && $this->minlength == $this->maxlength && $size > 0 && $size < $this->minlength ){
				$this->addError( $this->notlengthError.$this->minlength );
				$isValid = false;
			}else if( $this->minlength > 0 && $size < $this->minlength && $size > 0 ){
				$this->addError( $this->minlengthError.$this->minlength );
				$isValid = false;
			}else if( $this->maxlength > 0 && $size > $this->maxlength && $size > 0 ){
				$this->addError( $this->maxlengthError.$this->maxlength );
				$isValid = false;
			}
		}

		return $isValid;
	}

	public function addError( $notice ){
		$this->errors[] = $notice;
	}

	public function getErrors(){
		return $this->errors;
	}

	public function getType(){
		return $this->type;
	}
}
?>