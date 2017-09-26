<?php
//Version 1.1;
class Validate{
	public $inputs = array();	//Guarda los inputs procesados
	public $result = array();	//Guarda el resultado individual de cada input de la validacion
	public $errors = array();	//Guarda los inputs con errores
	public $success = false;	//Resultado de la validacion

	function __construct($model, $compare){
		$this->check($model, $compare);
	}

	function clear(){
		$this->inputs = array();
		$this->result = array();
		$this->errors = array();
	}

	//Valida
	function check($model, $compare){
		$type = '';

		$default = array(
			'notice' => '',			//Mensaje general a ser mostrado
			'type' => '',			//Tipo de elemento: alphanumeric, numeric y email
			'type_notice' => '',	//Mensaje a mostrar si el tipo del valor no es correcto
			'length' => 0,			//Tamaño del valor
			'length_max' => 0,		//Tamaño maximo del valor
			'length_min' => 0,		//Tamaño minimo del valor
			'length_notice' => '',	//Mensaje a mostrar si el tamaño del valor no es correcto
			'options' => '',		//Opciones aceptadas del valor, ej. '/^(value-1|value-2|etc.)$/'
			'options_notice' => '',	//Mensaje a mostrar si el valor no coincide con las opciones
			'obligatory' => true, 	//Si el valor el obligatorio
			'in_array_use_keys' => true //En inputs tipo array prmite usar las keys como valores
		);

		foreach ($model as $model_key => $model_settings) {
			$is_array = false;

			//Si no es array toma el valor de model_setting como el error
			if(!is_array($model_settings)){
				$model_settings = array('notice' => $model_settings);
			}

			//Agrega las propiedades faltantes
			$model_settings = $model_settings + $default;

			$model[$model_key] = $model_settings;

			//Comprueba que exista 
			if(isset($compare[$model_key])){
				$is_array = is_array($compare[$model_key]);

				//No valida objetos, los transforma en string vacios
				if(is_object($compare[$model_key])){
					$compare[$model_key] = '';
				}
			}else{
				//Si no existe la variable se agrega
				switch($model_settings['type']){
					case 'numeric':
						$compare[$model_key] = 0;
						break;

					default:
						$compare[$model_key] = '';
						break;
				}
			}

			//
			$result = array(
				'type' => $model_settings['type'],
				'value' => $compare[$model_key],
				'notice' => '',
				'is_empty' => false,
				'validate_type' => true,
				'validate_length' => true,
				'validate_option' => true
			);

			//Guarda el resultado de la validacion
			$this->result[$model_key] = &$result;
			//Guarda los inputs evaluados
			$this->inputs[$model_key] = $compare[$model_key];

			//Si no es obligatorio, continua con el siguiente
			//if(!$model_settings['obligatory']) continue;

			//Valida
			if($is_array){
				$temp = array(
					'inputs' => array(),
					'result' => array(),
					'errors' => array()
				);
				$temp_error = array();

				foreach ($compare[$model_key] as $compare_key => $compare_value) {
					if($model_settings['in_array_use_keys']){$compare[$model_key][$compare_key] = $compare_key;}

					//Permite un array
					if(is_array($compare_value) || is_object($compare_value)){
						$compare_value = '';
						$compare[$model_key][$compare_key] = $compare_value;
					}

					$this->check(array($model_key => $model_settings), array($model_key => $compare[$model_key][$compare_key]));

					$temp['result'][$compare_key] = $this->result[$model_key];

					$temp['inputs'][$compare_key] = $compare_value;

					if($this->result[$model_key]['is_empty'] || !$this->result[$model_key]['validate_type'] || !$this->result[$model_key]['validate_length'] || !$this->result[$model_key]['validate_option']){
						$temp['errors'][$compare_key] = $this->result[$model_key]['notice'];
					}
				}

				$this->inputs[$model_key] = $temp['inputs'];
				$this->result[$model_key] = $temp['result'];

				$error_size = sizeof($temp['errors']);
				if($error_size > 0){
					$this->errors[$model_key] = $temp['errors'];
				}

				unset($temp);
			}else{
				//limpia
				$compare[$model_key] = trim($compare[$model_key]);

				if($compare[$model_key] == '' && !$model_settings['obligatory']){
					continue;
				}else if($compare[$model_key] == '' && $model_settings['obligatory']){
					$result['notice'] = $model_settings['notice'];
					$result['is_empty'] = true;
				}else{
					//Valida el tipo
					switch ($model_settings['type']) {
						case 'alphanumeric':
							$result['validate_type'] = self::is_alphanumeric($compare[$model_key]);
							break;
						
						case 'numeric':
							$result['validate_type'] = self::is_numeric($compare[$model_key]);
							break;

						case 'email':
							$result['validate_type'] = self::is_email($compare[$model_key]);
							break;
					}

					//Si valido comprueba el tamanio del string
					if($result['validate_type']){
						$length = strlen($compare[$model_key]);

						if($model_settings['length'] > 0 && $model_settings['length'] != $length){$result['validate_length'] = false;}
						if($model_settings['length_min'] > 0 && $length < $model_settings['length_min']){$result['validate_length'] = false;}
						if($model_settings['length_max'] > 0 && $length > $model_settings['length_max']){$result['validate_length'] = false;}

						if($result['validate_length']){
							if($model_settings['options'] != ''){
								$result['validate_option'] = preg_match($model_settings['options'], $compare[$model_key]);
								if(!$result['validate_option']){
									$result['notice'] = ($model_settings['options_notice'] == '') ? $model_settings['notice'] : $model_settings['options_notice'];
								}
							}
						}else{
							$result['notice'] = ($model_settings['length_notice'] == '') ? $model_settings['notice'] : $model_settings['length_notice'];
						}
					}else{
						$result['notice'] = ($model_settings['type_notice'] == '') ? $model_settings['notice'] : $model_settings['type_notice'];
					}
				}

				if($result['is_empty'] || !$result['validate_type'] || !$result['validate_length'] || !$result['validate_option']){
					$this->errors[$model_key] = $result['notice'];
				}
			}

			//Remueve la referencia
			unset($result);
		}

		//Resultado de toda la validacion
		$error_size = sizeof($this->errors);
		$this->success = ($error_size == 0);
	}

	static public function is_alphanumeric($value){
		return preg_match("/^[a-zA-Z0-9]+$/", $value);
	}

	static public function is_numeric($value){
		return preg_match("/^[0-9]+$/", $value);
	}

	static public function is_email($value){
		return preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $value);
	}
}
?>