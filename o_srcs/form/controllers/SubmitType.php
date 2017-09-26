<?php
class SubmitType extends TextType{
	const NAME = 'SubmitType';
	public $type = 'submit';
	public $text = '';
	public $required = false;

	public function render(){
		return $a = array(
			'type' => $this->type,
			'name' => $this->name,
			'id' => $this->id,
			'value' => $this->default,
			'required' => $this->required,
			'attrs' => $this->attrs,
			'text' => $this->text
		);
		return $a;
	}
}
?>