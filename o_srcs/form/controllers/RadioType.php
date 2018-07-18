<?php
class RadioType extends TextType{
	const NAME = 'RadioType';
	protected $type = 'radio';
	public $choices = array();
	public $choiceError = 'La opción seleccionada no se encuentra disponible.';

	public function render(){
		$a = array(
			'type' => $this->type,
			'name' => $this->name,
			'id' => $this->id,
			'value' => $this->default,
			'required' => $this->required,
			'attrs' => $this->attrs,
			'choices' => $this->choices
		);
		return $a;
	}

	public function validate(){
		$r = true;
		if( $this->required || $this->default != '' ){
			$r = array_key_exists( $this->default, $this->choices );
			if( !$r ){
				$this->addError( $this->choiceError );
			}
		}
		return $r;
	}
}
?>