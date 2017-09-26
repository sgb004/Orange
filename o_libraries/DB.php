<?php
/**
 * Crea un conexión con MySQL, facilita las peticiones
 * @class DB
 * @version 2.0
 * @author sgb004
 */
class DB{
	static protected $conexion = false;
	public $lastError = '';
	public $lastQuery = '';
	public $insertId = false;

	function __construct(){
		if(self::$conexion === false){
			self::$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
			if (self::$conexion->connect_errno) {
				$this->lastError = 'Error to connect';
				self::$conexion = false;
			}else{
				if( defined('DB_CHARSET') ){
					self::$conexion->set_charset( DB_CHARSET );
				}
			}
		}
	}

	function add($table, $data, $model=array()){
		if($model != array()){$data = $this->comArray($data,$model);}
		$columns = implode(',',array_keys($data));
		$values = $this->getArraySafe($data,false,false);

		$query = 'INSERT INTO '.$table.' ('.$columns.') VALUES ('.$values.')';
		$this->execute($query);
		return $this->insertId;
	}

	function update($table, $data, $where, $model=array()){
		if($model != array()){$data = $this->comArray($data,$model);}
		$values = $this->getArraySafe($data,true,false);
		$conditions = $this->getArraySafe($where,true,true);

		$sql = 'UPDATE '.$table.' SET '.$values.' WHERE '.$conditions;
		return $this->execute($sql);
	}

	function delete($table, $condi){
		$conditions = $this->getArraySafe($condi, true, true);
		$sql = 'DELETE FROM '.$table.' WHERE '.$conditions;

		return $this->execute($sql);
	}

	function prepare(){
		$args = func_get_args();
		$query = $args[0];

		$values = array();
		if(is_array($args[1])){
			$values = $args[1];
		}else{
			$args_size = func_num_args();
			for($i=1; $i<$args_size; $i++){
				$values[] = $args[$i];
			}
		}

		$parts = explode('%s', $query);
		$partsSize = sizeof($parts);
		$query = $parts[0];

		for($i=1; $i<$partsSize; $i++){
			$query .= $this->getStringSafe($values[$i-1]).$parts[$i];
		}

		return $query;
	}

	function getResult($query){return $this->execute($query,true);}
	function close(){self::$conexion->close();}

	function execute($query,$isSelect=false){
		$this->lastQuery = $query;
		$response = self::$conexion->query($query);
		$result = array();
		if( self::$conexion->error != ''){
			$this->lastError = self::$conexion->error;
			if( !$isSelect ){
				$result = 0;
			}
		}else{
			if( $isSelect ){
				while ($o = $response->fetch_assoc() ) {
					$result[] = $o;
				}
			}else{
				$this->insertId = self::$conexion->insert_id;
			}
		}
		return $result;
	}

	function getArraySafe($arr,$upd,$con){
		$arrStr = '';

		foreach($arr as $inp => $val){
			$arrStrInp = '';
			if($upd){$arrStrInp = $inp.'=';}
			$tipSep = ',';
			if($con){$tipSep = ' AND';}
			$arrStr = $arrStr.$arrStrInp.'"'.$this->escapeString($val).'"'.$tipSep.' ';
		}
		if($con){
			$arrStr = substr($arrStr,0,-5);
		}else{
			$arrStr = substr($arrStr,0,-2);
		}

		return $arrStr;
	}	

	function getStringSafe($str){
		$str = "'".$this->escapeString($str)."'";
		return $str;
	}

	function escapeString( $s ){
		return self::$conexion->real_escape_string( $s );
	} 

	/**
	 * Sin uso
	 */
	function getGroup($ids,$column){
		$idsStr = '';
		foreach ($ids as $k => $v) {
			$v = $this->getStringSafe($v);
			$idsStr .= $column.'='.$v.' OR ';
		}
		$idsStr = substr($idsStr, 0, -4);
		return $idsStr;
	}

	function comArray($data,$model){
		$input = $data + $model;
		$input = array_intersect_key($input, $model);
		return $input;
	}
}	
?>