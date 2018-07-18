<?php
/**
 * Crea un conexión con una base de datos sqlite, facilita las peticiones
 * @class DBSQLite
 * @version 2.0
 * @author sgb004
 */
class DBSQLite extends DB{
	static protected $conexion = false;
	public $lastError = '';
	public $lastQuery = '';
	public $insertId = false;

	function __construct(){
		if(self::$conexion === false){
			self::$conexion = new DBSQLiteCL();
			if( !self::$conexion ){
				$this->lastError = self::$conexion->lastErrorMsg();
			}
		}
	}

	function close(){ 
		if(self::$conexion === false){
			self::$conexion->close();
		}
	}

	function execute($query,$isSelect=false){
		$this->lastQuery = $query;
		$res = self::$conexion->query($query);

		$dev = array();
		$error = self::$conexion->lastErrorMsg();

		if($error != '' && $error != 'not an error'){
			$this->lastError = $error;
		}else{
			if($isSelect == true){
				while ($obj = $res->fetchArray(SQLITE3_ASSOC)) {
					$dev[] = $obj;
				}
			}else{
				$this->insertId = self::$conexion->lastInsertRowid();
			}
		}
		return $dev;
	}

	function escapeString( $s ){
		return self::$conexion->escapeString( $s );
	}
}

class DBSQLiteCL extends SQLite3{
	function __construct(){
		$this->open( ABSPATH.'/o_db/site.sqlite3' );
	}
}
?>