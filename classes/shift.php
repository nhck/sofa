<?php

class Shift{
	var $start;
	var $end;
	var $user;
	var $sid;
	var $day;

	function Shift($start, $end, $day, $user=NULL, $sid=NULL){
		$this->start = $start;
		$this->end = $end;
		$this->day = $day;
		$this->user = $user;
		$this->sid = $sid;
	}

	function is_now($time){
		$day        = (int)$time[0];
		$now_hour   = (int)substr($time, 1,2);
		$now_min    = (int)substr($time, 3,2);
		$start_hour = (int)substr($this->start, 0,2);
		$start_min  = (int)substr($this->start, 2,2);
		$end_hour   = (int)substr($this->end, 0,2);
		$end_min    = (int)substr($this->end, 2,2);

		if ( $day != (int) $this->day) return false;

		if ($start_hour < $now_hour){
			if($now_hour < $end_hour){
				return true;
			}elseif($now_hour == $end_hour){
				if( $now_min < $end_min ){
					return true;
				}else{ 
					return false;	
				}
			}else {
				return false;	
			}
		}elseif ($start_hour == $now_hour){
			if($start_min <= $now_min){
				if($now_hour < $end_hour){
					return true;
				}elseif($now_hour==$end_hour){
					if($now_min < $end_min){
						return true;
					}else{
						return false;
					}
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function collides(){
		global $cfg, $sql;
		$result = $sql->query(sprintf('SELECT start,end FROM %sshifts WHERE day=%s', $cfg['dbprefix'], $this->day));
		foreach($result as $shift){
			$shift = new Shift($shift['start'], $shift['end'], $this->day);
			if( $this->is_now($this->day.$shift->start) 
			 or $this->is_now($this->day.$shift->end) 
			 or $shift->is_now($this->day.$this->start) 
			 or $shift->is_now($this->day.$this->end)){
				return true;
			}else{
				printf("neu: %s-%s, alt: %s-%s\n", $this->start, $this->end, $shift->start, $shift->end);
			}
		}
		return false;
	}
	function update(){
		global $qry, $cfg;
		$start=$sql->quote(htmlspecialchars($this->start));
		$end=$sql->quote(htmlspecialchars($this->end));
		$day=$sql->quote(htmlspecialchars($this->day));
		$uid=$sql->quote(htmlspecialchars($this->user->uid));
		$sid=$sql->quote(htmlspecialchars($this->sid));
		$sql->query(sprintf("update %sshifts set start=%s, end=%s, day=%s, uid=%s where sid=%s;", $cfg['dbprefix'], $start, $end, $day, $uid, $sid));
	}
	function insert(){
		global $sql, $cfg;
		$start=$sql->quote(htmlspecialchars($this->start));
		$end=$sql->quote(htmlspecialchars($this->end));
		$day=$sql->quote(htmlspecialchars($this->day));
		$uid=$sql->quote(htmlspecialchars($this->user->uid));
		$sql->query(sprintf("insert into %sshifts (start, end, day, uid) values (%s, %s, %s, %s);", $cfg['dbprefix'], $start, $end, $day, $uid));
	}
	function select(){
		global $cfg, $sql;
		print $sid;
		$result=$sql->query(sprintf('SELECT * FROM %sshifts WHERE sid=%s;',$cfg['dbprefix'], $sid));
		$row=$result->fetch();
		$this->start=$row['start'];
		$this->end=$row['end'];
		$this->day=$row['day'];
		$this->user=new User($row['uid']);
	}
	function delete(){
		global $cfg, $sql;
		$sid=$sql->quote(htmlspecialchars($this->sid));
		$qry=sprintf("delete from %sshifts where sid=%s;",$cfg['dbprefix'], $sid);
		$sql->query($qry);
	}



}
?>
