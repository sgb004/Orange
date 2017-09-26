<?php
class Registrations{
	protected $table = 'registrations';
	protected $db;
	public $file = '';

	function __construct(){
		$this->db = new DB();
	}

	function add($fields){
		$model = array(
			'name' => '',
			'email' => '',
			'phone' => '',
			'comments' => '',
			'register_date' => date('Y-m-d H:i:s')
		);

		$this->db->add($this->table, $fields, $model);
		$return = ($this->db->lastError == '') ? $this->db->insertId : false;
		return $return;
	}

	function get(){
		$query = 'SELECT * FROM '.$this->table;
		$query = $this->db->getResult($query);
		return $query;
	}
}
?>