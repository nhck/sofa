<?php

class User{
	var $uid;
	var $name;
	var $email;
	var $passhash;
	var $shifts=array();

	//argument can be id or name
	function User($arg){
		if($arg[0] == '_'){
			$this->uid=(int) substr($arg,1);
			$this->select();
		}else{
			$this->name=$arg;
		}
	}

	function insert(){
		global $sql, $cfg;
		$qry=sprintf("insert into %susers (name, email, fon, passhash) values (%s, %s, %s, %s);", $cfg['dbprefix'], $this->name, $this->email, $this->fon, $this->passhash);
		return $sql->query($qry);
	}

	function update(){
		global $sql, $cfg;
		$name= $sql->quote($this->name);
		$email= $sql->quote($this->email);
		$passhash= $sql->quote($this->passhash);
		$qry=sprintf("update %susers set name=%s, email=%s, passhash=%s ".
			"where uid=%s;", 
			$cfg['dbprefix'], $name, $email, $passhash, $this->uid);
		return $sql->query($qry);
	}

	function select(){
		global $cfg, $sql;
		$qry=sprintf('SELECT * FROM %susers WHERE uid=%s;',
			$cfg['dbprefix'], $this->uid);
		$result=$sql->query($qry);
		$row=$result->fetch();
		$result->closeCursor();
		$this->name=$row['name'];
		$this->email=$row['email'];
		$this->passhash=$row['passhash'];
		$result=$sql->query(sprintf('SELECT * FROM %sshifts WHERE uid=%s ORDER BY day,start;', $cfg['dbprefix'], $this->uid));
		$this->shifts=array();
		foreach($result as $row){
			$this->shifts[]=new Shift($row['start'], $row['end'], $row['day'], $this, $row['sid']);
		}
		$result->closeCursor();
	}

	function login(){
		global $cfg, $sql;
		$name=$sql->quote(htmlspecialchars($this->name));
		$passhash=$sql->quote(htmlspecialchars($this->passhash));
		$result=$sql->query(sprintf("SELECT * FROM %susers WHERE name=%s AND passhash=%s;",$cfg['dbprefix'], $name, $passhash));
		if($row=$result->fetch()){
			$result->closeCursor();
			$this->uid=$row['uid'];
			$this->select();
			return $row;
		}else
			return false;
	}

}

?>
