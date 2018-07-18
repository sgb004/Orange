<?php
class TokensModel{
	protected $db;
	protected $tableName = 'tokens';
	public $tokenKey = '';
	private $type = 0;
	public $ip = '';
	public $userId = 0;
	private $time = '';

	function __construct( $type = 0, $time = '-1 hour' ){
		$this->db = new DB();
		$this->setType( $type );
		$this->time = $time;
		$this->clear();
	}

	function add(){
		$this->db->add($this->tableName, array( 'token_key' => $this->tokenKey, 'user_id' => $this->userId, 'ip' => $this->ip, 'type' => $this->type, 'register_date' => date('Y-m-d H:i:s') ));
		$return = ($this->db->lastError == '') ? $this->db->insertId : false;
		return $return;
	}

	function clear(){
		$date = date( 'Y-m-d H:i:s', strtotime( $this->time ) );
		$query = 'DELETE FROM '.$this->tableName.' WHERE register_date <= %s AND type=%s';
		$query = $this->db->prepare( $query, $date, $this->type );
		$this->db->execute( $query );
	}

	function check( $log = false ){
		$query = 'SELECT token_id FROM '.$this->tableName.' WHERE token_key=%s AND type=%s';
		$query = $this->db->prepare( $query, $this->tokenKey, $this->type );
		$query = $this->db->getResult( $query );
		return isset( $query[0]['token_id'] );
	}

	function getByIp(){
		$query = 'SELECT user_id FROM '.$this->tableName.' WHERE token_key=%s AND ip=%s AND type=%s';
		$query = $this->db->prepare( $query, $this->tokenKey, $this->ip, $this->type );
		$query = $this->db->getResult( $query );
		return isset( $query[0]['user_id'] ) ? $query[0]['user_id'] : 0;
	}

	function getUserId(){
		$query = 'SELECT user_id FROM '.$this->tableName.' WHERE token_key=%s AND type=%s';
		$query = $this->db->prepare( $query, $this->tokenKey, $this->type );
		$query = $this->db->getResult( $query );
		return isset( $query[0]['user_id'] ) ? $query[0]['user_id'] : 0 ;
	}

	function delete(){
		$query = 'DELETE FROM '.$this->tableName.' WHERE token_key=%s';
		$query = $this->db->prepare( $query, $this->tokenKey );
		$this->db->execute( $query );
	}

	/* */
	function setType( $type ){
		$this->type = intval( $type );
		return $this->type;
	}

	function getType( $type ){
		return $this->type;
	}
}
?>