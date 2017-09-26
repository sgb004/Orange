<?php
class FileType extends TextType{
	private $_isEmpty = false;
	protected $type = 'file';
	public $typesFile = array();
	public $typesFileError = 'El tipo de archivo seleccionado no esta permitido';
	public $multiple = false;
	public $multipleLimit = 1;
	public $multipleLimitError = 'El número de archivos que este campo permite enviar es de ';
	public $sizeMax = 0;
	public $sizeMaxError = 'El archivo excede el limite máximo de ';

	public function setDefault( $default ){
		if( $this->multiple ){
			$temp = array();
			foreach ($default as $key => $file) {
				$_file = array();
				foreach ($file as $position => $value) {
					if( !isset( $temp[ $position ] ) ){
						$temp[ $position ] = array();
					}

					$temp[ $position ][ $key ] = $value;
				}
			}
			$default = $temp;
		}
		$this->default = $default;
	}

	public function isEmpty(){
		if( $this->required ){
			$default = ( $this->multiple ) ? $this->default : array( $this->default );
			foreach ($default as $file) {
				$errorFile = isset( $file['error'] ) ? $file['error'] : 1;

				if( $errorFile != 0 ){
					$this->_isEmpty = true;
					$this->addError( $this->notice );
				}
			}
		}
		return $this->_isEmpty;
	}

	public function validate(){
		$isValid = true;
		if( !$this->_isEmpty && !empty( $this->typesFile ) ){
			$isValid = false;
			$default = ( $this->multiple ) ? $this->default : array( $this->default );
			if( sizeof( $default ) <= $this->multipleLimit ){
				foreach ( $default as $file ) {
					$isValid = false;

					$type = isset($file['type'] ) ? $file['type'] : '';

					foreach ($this->typesFile as $_type) {
						if( $_type === $type ){
							$isValid = true;
							break;
						}
					}
					
					if( !$isValid ){
						$this->addError( $this->typesFileError );
					}

					if( $this->sizeMax > 0 && $file['size'] > $this->sizeMax ){
						$isValid = false;
						$this->addError( $this->sizeMaxError );
					}

					if( !$isValid ){
						break;
					}
				}
			}else{
				$this->addError( $this->multipleLimitError.$this->multipleLimit );
			}
		}

		return $isValid;
	}
}
?>