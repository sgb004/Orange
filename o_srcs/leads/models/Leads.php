<?php
class Leads{
	protected $table = 'leads';
	protected $db;
	public $leadId = 0;
	public $name = '';
	public $email = '';
	public $phone = '';
	public $message = '';
	public $campaign = '';

	function __construct(){
		$this->db = new DB();
	}

	function add(){
		$fields = array(
			'name' => $this->name,
			'email' => $this->email,
			'phone' => $this->phone,
			'message' => $this->message,
			'campaign' => $this->campaign,
			'register_date' => date('Y-m-d H:i:s'),
		);
		$this->db->add($this->table, $fields);
		$return = ($this->db->lastError == '') ? $this->db->insertId : 0;
		return $return;
	}

	function get(){
		$query = 'SELECT * FROM '.$this->table;
		$query = $this->db->getResult($query);
		return $query;
	}
}
?>