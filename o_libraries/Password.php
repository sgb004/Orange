<?php
/**
 * @version 1.0
 * @author sgb004
 */

class Password{
	private $letter = 25;
	private $letters = array();
	private $lettersSize = 0;

	function __construct(){
		$this->letters = array( '-', '&', '.', ';', '$', '*', '#', '%', '-' );
		$this->lettersSize = sizeof($this->letters);
		$this->lettersSize -= 1;
	}

	/**
	 * Funciones que eligen de forma aleatoria un caracter para ser agregados a la contraseña
	 */
	private function Rdp(){return chr(rand(48, 57));}
	private function Rds(){return chr(rand(65, 90));}
	private function Rdt(){return chr(rand(97, 122));}

	/**
	 * Crea la contraseña
	 *
	 * @param password string la contraseña a codificar
	 * @return string devuelve la contraseña codificada
	 */
	function make( $password ){
		$password = $this->letters[rand(0, rand(0, $this->lettersSize))].$password.$this->letters[rand(0, rand(0, $this->lettersSize))];
		$password = md5(md5(md5($password)));
		$rd = rand(1, 3);
		if($rd == 1){$rd = $this->Rdp();}
		else if($rd == 2){$rd = $this->Rds();}
		else{$rd = $this->Rdt();}
		$password = substr($password, 0,$this->letter).$rd.substr($password, $this->letter,strlen($password));

		return $password;
	}

	/**
	 * Compara la contraseña con algun string
	 *
	 * @param passwordPlain string con la contraseña a comprobar
	 * @param password string la contraseña codificada por la funcion make
	 * @return boolean
	 */
	function check($passwordPlain, $password){
		$letterNext = $this->letter+1;

		$passwordPlain = trim($passwordPlain);
		$password = trim($password);
		
		$math = false;
		$password = substr($password, 0,$this->letter).substr($password, $letterNext,strlen($password));
		for($i=0; $i<$this->lettersSize; $i++){
			for($j=0; $j<$this->lettersSize; $j++){
				$passwordCoded = $this->letters[$j].$passwordPlain.$this->letters[$i];
				$passwordCoded = md5(md5(md5($passwordCoded)));
				if($passwordCoded == $password){
					$math = true;
					break;
				}
			}
			if($math == true){
				break;
			}
		}

		return $math;
	}
}
?>