<?php

class Shift{
	var $start;
	var $end;
	var $user;
	var $sid;
	var $day;

	function Shift($start, $end, $day, $user=NULL, $sid=NULL){
		date_default_timezone_set('Europe/Berlin');

		$this->start = new DateTime($start);
		$this->end = new DateTime($end);
		$this->day = $day;
		$this->user = $user;
		$this->sid = $sid;
	}

	function is_now($time){
		//time: wochentag,stunde:minute
		$time= explode(',', $time);
		if ($time[0] != $this->day) return false;
		$time=new DateTime($time[1]);
		if(($this->start <= $time) && ($time < $this->end)){
			return true;
		}else
			return false;
	}

	function collides(){
		global $cfg, $sql;
		$result = $sql->query(sprintf(
			'SELECT start,end FROM %sshifts WHERE day=%s', 
				$cfg['dbprefix'], $this->day));
		foreach($result as $shift){
			$shift = new Shift($shift['start'], $shift['end'], $this->day);
			if($shift->day != $this->day) 
				return false;
			if( !( ($this->end < $shift->start) or ($this->end > $shift->start) ) ){
				return true;
			}
		}
		return false;
	}

	function update(){
		global $qry, $cfg;
		$start=$sql->quote(htmlspecialchars($this->start->format('H:i')));
		$end=$sql->quote(htmlspecialchars($this->end->format('H:i')));
		$day=$sql->quote(htmlspecialchars($this->day));
		$uid=$sql->quote(htmlspecialchars($this->user->uid));
		$sid=$sql->quote(htmlspecialchars($this->sid));
		$sql->query(
			sprintf("update %sshifts set start=%s, end=%s, day=%s, uid=%s ".
			"where sid=%s;", $cfg['dbprefix'], $start, $end, $day, $uid, $sid));
	}

	function insert(){
		global $sql, $cfg;
		$start=$sql->quote(htmlspecialchars($this->start->format('H:i')));
		$end=$sql->quote(htmlspecialchars($this->end->format('H:i')));
		$day=$sql->quote(htmlspecialchars($this->day));
		$uid=$sql->quote(htmlspecialchars($this->user->uid));
		$qry=sprintf("insert into %sshifts (start, end, day, uid) "
			."values (%s, %s, %s, %s);", 
			$cfg['dbprefix'], $start, $end, $day, $uid);
		return $sql->query($qry);
	}

	function select(){
		global $cfg, $sql;
		print $sid;
		$result=$sql->query(
			sprintf('SELECT * FROM %sshifts WHERE sid=%s;',$cfg['dbprefix'], $sid));
		$row=$result->fetch();
		$this->start=new DateTime($row['start']);
		$this->end=new DateTime($row['end']);
		$this->day=$row['day'];
		$this->user=new User($row['uid']);
		$result->closeCursor();
	}

	function delete(){
		global $cfg, $sql;
		$sid=$sql->quote(htmlspecialchars($this->sid));
		$qry=sprintf("delete from %sshifts where sid=%s;",$cfg['dbprefix'], $sid);
		return $sql->query($qry);
	}
	
}

?>
