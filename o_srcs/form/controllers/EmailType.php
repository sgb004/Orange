<?php
class EmailType extends PatternType{
	const NAME = 'EmailType';
	protected $type = 'email';
	public $pattern = '^[a-z0-9-_.+%]+@[a-z0-9-.]+\.[a-z]{2,4}$';
	public $patternError = 'Por favor escriba una dirección de correo válida';

	public function render(){
		return parent::render();
	}
}
?>