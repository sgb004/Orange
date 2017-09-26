<?php
/**
 * Class to replace mysqli, use this class in the absence mysqli 
 * @class MySQLiMySQL
 * @version 0.1
 */
class MySQLiMySQL{
	private $conn;
	public $connect_error = false;
	public $error = '';
	public $insert_id;

	/**
	 * Connect to database
	 * @param string $db_host host of database
	 * @param string $db_user user with permissions to connect to database
	 * @param string $db_pass pass associated with the user
	 * @param string $db_database name of database selected
	 */
	function __construct($db_host, $db_user, $db_pass, $db_database){
		$this->conn = mysql_connect($db_host, $db_user, $db_pass);
		if($this->conn){
			mysql_select_db($db_database, $this->conn);
		}else{
			$connect_error = false;
		}
	}

	/**
	 * Sets the default client character set * 
	 * @param string $charset the charset to be set as default *
	 * @return bool returns TRUE on success or FALSE on failure *
	 */
	function set_charset($charset){
		return mysql_set_charset($charset, $this->conn);
	}

	/**
	 * Performs a query on the database *
	 * @param string $query the query string *
	 * @return mixed Returns FALSE on failure. For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysql_result object. For other successful queries mysqli_query() will return TRUE *
	 */ 
	function query($query){
		$result = mysql_query($query, $this->conn);
		$this->error = mysql_error();
		$this->insert_id = mysql_insert_id();
		return new MySQLiMySQLResult($result);
	}

	/**
	 * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection *
	 * @param string $escapestr the string to be escaped.  *
	 * @return mixed returns string scaped, or FALSE on error *
	 */ 
	function real_escape_string($escapestr){
		return mysql_real_escape_string($escapestr);
	}

	/**
	 * Closes a previously opened database connection *
	 */
	function close(){
		mysql_close();
	}

	//* Taken from php manual
}
?>