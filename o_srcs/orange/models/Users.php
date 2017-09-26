<?php
class Users{
	private $db;
	public $userId;
	public $password;
	public $status;
	public $email;

	function __construct(){
		$this->db = new DB();
	}

	function add( $fields = array() ){
		$model = array(
			'email' => $this->email,
			'password' => $this->password,
			'status' => 0,
			'register_date' => date('Y-m-d H:i:s')
		);

		$this->db->add('users', $fields, $model);
		return ($this->db->lastError == '') ? $this->db->insertId : false;
	}

	function getByEmail(){
		$query = 'SELECT * FROM users WHERE email=%s';
		$query = $this->db->prepare( $query, $this->email );
		$query = $this->db->getResult( $query );
		return $query; 
	}

	function updateLastLoginDate(){
		$this->db->update('users', array( 'last_login_date' => date('Y-m-d H:i:s') ), array( 'user_id' => $this->userId ));
	}

	function updateStatus(){
		$this->db->update('users', array( 'status' => $this->status ), array( 'user_id' => $this->userId ));
	}
}
?>