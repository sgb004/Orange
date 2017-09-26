<?php
/**
 * Represents the result set obtained from a query against the database *
 * @class MySQLiMySQLResult
 * @version 0.1
 */
final class MySQLiMySQLResult{
	public $result;

	/**
	 * Init
	 * @param resource $result The mysql_result obtained from a query against the database
	 */
	function __construct($result){
		$this->result = $result;
	}

	/**
	 * Fetch a result row as an object
	 * @param The result resource that is being evaluated. This result comes from a call to mysql_query() *
	 * @return Returns an object with string properties that correspond to the fetched row, or FALSE if there are no more rows *
	 */
	function fetch_object(){
		return mysql_fetch_object($this->result);
	}
}
//* Taken from php manual
?>