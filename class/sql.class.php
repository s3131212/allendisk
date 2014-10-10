<?php

class MySQL {
	public $lastError;
	public $lastQuery;
	public $result;
	public $records;
	public $affected;
	public $rawResults;
	public $arrayedResult;
	
	public $hostname;
	public $username;
	public $password;
	public $database;
	
	public $databaseLink;
	
	function __construct($database, $username, $password, $hostname='localhost'){
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
		
		$this->connect();
	}
	
	private function connect($persistant = false){
		if($persistant){
			$this->databaseLink = new mysqli('p:' . $this->hostname, $this->username, $this->password, $this->database);
		}
		else {
			$this->databaseLink = new mysqli($this->hostname, $this->username, $this->password, $this->database);
		}
		
		if($this->databaseLink->connect_error !== null){
   		    $this->lastError = 'Could not connect to server: ' . mysql_error($this->databaseLink);
			return false;
		}
		
		$this->databaseLink->set_charset("utf8");
		
		return true;
	}
	
	private function SecureData($data){
		if(is_array($data)){
			foreach($data as $key=>$val){
				if(!is_array($data[$key])){
					$data[$key] = $this->databaseLink->real_escape_string($data[$key]);
				}
			}
		}
		else {
			$data = $this->databaseLink->real_escape_string($data, $this->databaseLink);
		}
		
		return $data;
	}
	
	function ExecuteSQL($query){
		$this->lastQuery = $query;
		if($this->result = $this->databaseLink->query($query)){
            if(is_object($this->result)){
                $this->records = $this->result->num_rows;
                $this->affected	= $this->databaseLink->affected_rows;
                
                if($this->records > 0){
                    $this->ArrayResults();
                    return $this->arrayedResult;
                }
                else {
                    return true;
                }
            }
            else {
                return $this->result;
            }
		}
		else {
			$this->lastError = $this->databaseLink->error;
			return false;
		}
	}
	
	function Insert($vars, $table, $exclude = ''){
		if($exclude == ''){
			$exclude = array();
		}
		
		$vars = $this->SecureData($vars);
		$query = "INSERT INTO `{$table}` SET ";
		
		foreach($vars as $key=>$value){
			if(in_array($key, $exclude)){
				continue;
			}
			
			$query .= "`{$key}` = '{$value}', ";
		}
		
		$query = substr($query, 0, -2);
		return $this->ExecuteSQL($query);
	}
	
	function Delete($table, $where='', $limit='', $like=false){
		$query = "DELETE FROM `{$table}` WHERE ";
		
		if(is_array($where) && $where != ''){
			$where = $this->SecureData($where);
			foreach($where as $key=>$value){
				if($like){
					$query .= "`{$key}` LIKE '%{$value}%' AND ";
				}
				else {
					$query .= "`{$key}` = '{$value}' AND ";
				}
			}
			
			$query = substr($query, 0, -5);
		}
		
		if($limit != ''){
			$query .= ' LIMIT ' . $limit;
		}
		
		return $this->ExecuteSQL($query);
	}
	
	function Select($from, $where='', $orderBy='', $limit='', $like=false, $operand='AND'){
		if(trim($from) == ''){
			return false;
		}
		
		$query = "SELECT * FROM `{$from}` WHERE ";
		
		if(is_array($where) && $where != ''){
			$where = $this->SecureData($where);
			foreach($where as $key=>$value){
				if($like){
					$query .= "`{$key}` LIKE '%{$value}%' {$operand} ";
				}
				else {
					$query .= "`{$key}` = '{$value}' {$operand} ";
				}
			}
			
			$query = substr($query, 0, -5);
		}
		else {
			$query = substr($query, 0, -7);
		}
		
		if($orderBy != ''){
			$query .= ' ORDER BY ' . $orderBy;
		}
		
		if($limit != ''){
			$query .= ' LIMIT ' . $limit;
		}
		
		return $this->ExecuteSQL($query);
	}
	
	function Update($table, $set, $where, $exclude = ''){
		if(trim($table) == '' || !is_array($set) || !is_array($where)){
			return false;
		}
		
		if($exclude == ''){
			$exclude = array();
		}
		
		$set = $this->SecureData($set);
		$where = $this->SecureData($where);
		$query = "UPDATE `{$table}` SET ";
		
		foreach($set as $key=>$value){
			if(in_array($key, $exclude)){
				continue;
			}
			
			$query .= "`{$key}` = '{$value}', ";
		}
		
		$query = substr($query, 0, -2);
		$query .= ' WHERE ';
		
		foreach($where as $key=>$value){
			$query .= "`{$key}` = '{$value}' AND ";
		}
		
		$query = substr($query, 0, -5);
		return $this->ExecuteSQL($query);
	}
	
	function ArrayResults(){
		$this->arrayedResult = array();
		while($data = $this->result->fetch_assoc()){
			$this->arrayedResult[] = $data;
		}
		return $this->arrayedResult;
	}
}