<?php
class Form{
	private $fields = array();
	private $name;
	private $type = 'POST';
	private $useToken = false;
	private $_isValid = false;
	private $filters = array();
	private $notices = array();

	public function __construct( $formName, $type = 'POST'){
		$this->name = $formName;
		$type = strtoupper( $type );
		if( $type != 'GET' ){$type = 'POST';}
		$this->type = $type ;

		$this->filters = array(
			'pre_validate_data' => array(),
			'post_set_data' => array(),
			'post_validate_data' => array()
		);
	}

	public function addToken(){
		$this->add( '__token', TokenType::NAME, array(
			'name' => '__token'
		));
		$this->useToken = true;
		return $this;
	}

	public function add( $name, $type, $prop = array() ){
		$_type = new $type();
		$attrs = get_object_vars( $_type );
		$attrs['id'] = $this->name.'_'.$name;
		$attrs['name'] = $this->name.'['.$name.']';
		$prop = $prop + $attrs;
		if( !isset( $prop['attrs'] ) ){ $prop['attrs'] = array(); }
		$prop['attrs'] = $prop['attrs'] + array(
			'class' => ''
		);
		$this->fields[ $name ] = array(
			'type' => $type,
			'prop' => $prop,
			'required' => $_type->required,
			'errors' => array(),
			'isRender' => false,
			'mask' => ''
		);
		return $this;
	}

	public function row( $name ){
		return isset( $this->fields[$name]->typeAttrs ) ? $this->fields[$name]->typeAttrs : array();
	}

	public function label( $name ){
		$this->getField( $name );
		$a = array();
		if( $this->fields[$name]->label != '' ){
			$a['attrs'] = isset( $this->fields[ $name ]->labelAttrs ) ? $this->fields[$name]->labelAttrs : array();
			$a['attrs']['for'] = $this->fields[ $name ]->id;
			$a['required'] = $this->fields[ $name ]->required;
			$a['text'] = $this->fields[ $name ]->label;
		}
		return $a;
	}

	public function field( $name ){
		$type = $this->getField( $name );
		$this->fields[ $name ]->isRender = true;
		$a = $type->render();
		if( $this->fields[ $name ]->getType() == 'password' ){
			$a['value'] = '';
		}
		return $a;
	}

	public function child( $name, $id ){
		$type = $this->getField( $name );
		$this->fields[ $name ]['isRender'] = true;
		echo $type->renderChild( $id );
	}

	public function errors( $name ){
		$type = $this->getField( $name );
		return $type->getErrors();
	}

	public function errorsList(){
		$errors = array();
		foreach ($this->fields as $name => $field) {
			$errors[ $name ] = $field->getErrors();
		}
		return $errors;
	}

	/**
	 * Convierte las carateristcas del field en un objeto
	 */
	private function getField( $name ){
		if( is_array( $this->fields[ $name ] ) ){
			$this->fields[ $name ] = new $this->fields[ $name ]['type']( $this->fields[ $name ]['prop']  );
		}
		return $this->fields[ $name ];
	}

	public function getFields(){
		return $this->fields;
	}

	public function submit( $data = array() ){
		$this->_isValid = true;

		if( empty( $data ) ){
			if( isset( $_POST[ $this->name ] ) || isset( $_GET[ $this->name ] ) ){
				$data = ($this->type == 'POST') ? $_POST[ $this->name ] : $_GET[ $this->name ];
			}else{
				$this->addNotice( 'Ocurrió un error al procesar el formulario', 'danger' );
				$this->_isValid = false;
			}
		}

		if( $this->_isValid ){
			if( $this->useToken ){
				$data['__token'] = ($this->type == 'POST') ? $_POST[ '__token' ] : $_GET[ '__token' ];
			}

			foreach( $this->fields as $name => $field ){
				$this->getField( $name );
				if( $this->fields[ $name ]->getType() == 'submit' ){ continue; }

				if( isset( $data[$name] ) ){
					$this->fields[ $name ]->setDefault( $data[$name] );
				}
			}

			$this->fields = $this->applyFilter( 'pre_validate_data', $this->fields );

			foreach( $this->fields as $name => $field ){
				if( $this->fields[ $name ]->getType() == 'submit' ){ continue; }

				$isEmpty = $this->fields[ $name ]->isEmpty();

				if( $isEmpty ){
					$this->_isValid = false;
				}
			}

			if( $this->_isValid ){
				$this->fields = $this->applyFilter( 'post_set_data', $this->fields );

				if( $this->fields != null ){
					foreach( $this->fields as $name => $field ){
						if( $this->fields[ $name ]->getType() == 'submit' ){ continue; }

						$isValid = $this->fields[ $name ]->validate();
						if( !$isValid ){
							$this->_isValid = false;
						}
					}

					if( $this->_isValid ){
						$this->_isValid = $this->applyFilter( 'post_validate_data', $this, $this->_isValid );
					}
				}
			}
		}
	}

	public function isValid(){
		return $this->_isValid;
	}

	public function addFilter( $key, $fn = false ){
		if( isset( $this->filters[ $key ] ) ){
			if( $fn !== false ){
				$this->filters[ $key ][] = $fn;
			}
		}
	}

	public function applyFilter(){
		$args = func_get_args();
		$key = $args[0];
		$response = isset( $args[1] ) ? $args[1] : null;

		if( isset( $this->filters[ $key ] ) ){
			$values = array();
			$argsSize = func_num_args();
			for($i=1; $i<$argsSize; $i++){
				$values[] = $args[$i];
			}

			$i = $i - 2;
			$response = $values[ $i ];

			foreach ($this->filters[ $key ] as $fn) {
				$response = $values[ $i ] = call_user_func_array( $fn, $values );
			}
		}

		return $response;
	}

	public function addNotice( $msg, $type = '' ){
		$this->notices[] = array( 'msg' => $msg, 'type' => $type );
	}

	public function getNotices(){
		return $this->notices;
	}

	public function setRequired( $name, $state ){
		$this->fields[ $name ][ 'prop' ][ 'required' ] = $state;
	}

	/**
	 * Agrega los valores a los campos provenientes de un array
	 */
	public function setData( $data ){
		foreach ($data as $key => $value) {
			if( isset( $this->fields[ $key ] ) ){
				if( isset( $this->fields[ $key ]['prop']['default'] ) ){
					$this->fields[ $key ]['prop']['default'] = $value;
				}else{
					$this->fields[ $key ]['default'] = $value;
				}
			}
		}
	}

	/**
	 *
	 */
	public function setProperty( $fieldName, $property, $value, $propValue = '' ){
		if( $property == 'attrs' ){
			$this->fields[ $fieldName ]['prop'][ 'attrs' ][ $value ] = $propValue;
		}else{
			$this->fields[ $fieldName ]['prop'][ $property ] = $value;
		}
	}

	/**
	 *
	 */
	public function addFieldError( $fieldName, $notice ){
		$this->fields[ $fieldName ]->addError( $notice );
	}
}
?>