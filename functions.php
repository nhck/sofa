<?php

function error($str){}

function logout(){
	$_SESSION = array();
	session_destroy();
}

function lost_passwd(){

}

function add_user(){
		$act=true;
		$name=$sql->quote(htmlspecialchars($_POST['name']));
		$fon=$sql->quote(htmlspecialchars($_POST['fon']));
		$email=$sql->quote(htmlspecialchars($_POST['email']));
		$password=$sql->quote(htmlspecialchars($_POST['password']));
		$passhash=sha1($password);
		$passhash=$sql->quote($passhash);
		
		$user=new User($name);
		$user->fon=$fon;
		$user->email=$email;
		$user->passhash=$passhash;

		$qry=sprintf("select uid from %susers where name=%s;",
			$cfg['dbprefix'], 
			$name );
		if($sql->query($qry)->fetch()){
			$alerts[]="Dieser Name ist schon Belegt.";
			$act=false;
		}
		if($act){
			$user->insert();
		}
}

function delete_shift(){
	$act=true;
	$shift='';
	foreach($_SESSION['user']->shifts as $_shift){
		if($_shift->sid ==
			 $_POST['sid']){
			$shift=$_shift;
		}
	}
	if($shift==''){
		$alerts[]="Diese Schicht gehoert dir nicht.";
		$act=false;
	}
	if($act)
		$shift->delete();
}

function new_shift(){
	$act=true;
	$start=sprintf('%02u%02u',(int)$_POST['starth'],(int)$_POST['startm']);
	$end=sprintf('%02u%02u',(int)$_POST['endh'],(int)$_POST['endm']);
	$shift=new Shift($start,$end, $_POST['day'], $_SESSION['user']);
	if($shift->collides()){
		$alerts[]="Die Schicht ueberschneidet sich.";
		$act=false;
	}
	if($start==$end){
		$alerts[]="Die Schicht ist 0 Minuten lang.";
		$act=false;
	}
	if((int) $_POST['starth'] > $_POST['endh']){
		$alerts[]="Die Schicht endet, bevor sie angefangen hat.";
		$act=false;
	}
	if((int) $_POST['starth'] == (int)$_POST['endh'] &&
		 (int) $_POST['startm'] > (int)$_POST['endm'] ){
				$alerts[]="Die Schicht endet, bevor sie angefangen hat.";
				$act=false;
	}
	if($act)
		$shift->insert();
}

function make_tables(){
	global $cfg, $sql;
	$sql->query('create table '.$cfg['dbprefix'].'users (uid INTEGER PRIMARY KEY,name TEXT, email TEXT, fon TEXT, passhash TEXT);');
	$sql->query('create table '.$cfg['dbprefix'].'shifts (sid INTEGER PRIMARY KEY, start TEXT, end TEXT, day INTEGER, uid INTEGER);');


}
function get_shifts(){
	global $cfg, $sql;
	$shifts=array();
	$result = $sql->query('SELECT start, end, day, uid, sid FROM shifts ORDER BY day,start');
	foreach($result as $row){
		$shifts[] = new Shift($row['start'], $row['end'], $row['day'], new User('_'.$row['uid']), $row['sid']);
	}
	return $shifts;
}

function string_splice($string, $offset, $length=0, $replacement=''){
	$a=substr($string, 0,$offset);
	$b=substr($string, $offset+$length);
	return $a.$replacement.$b;
}

function write_table(){
	$shifts=get_shifts();

	$table='<table id="shifttable" border="0" cellspacing="0px" >'."\n";
	$table.='<tr class="shiftrow days">'."\n";
	$table.="<td class=\"shiftcell day\"></td>\n";
	$table.="<td class=\"shiftcell day\">Montag</td>\n";
	$table.="<td class=\"shiftcell day\">Dienstag</td>\n";
	$table.="<td class=\"shiftcell day\">Mittwoch</td>\n";
	$table.="<td class=\"shiftcell day\">Donnerstag</td>\n";
	$table.="<td class=\"shiftcell day\">Freitag</td>\n";
	$table.="</tr>\n";
	for($h=8; $h<22; $h++){
		for($m=0; $m<60; $m+=15){
			$table.='<tr class="shiftrow h'.$h.' m'.$m.'">'."\n";
			$table.='<td class="shiftcell time">'.sprintf('%02u:%02u',$h,$m)."</td>\n";
			for($d=0; $d<5; $d++){
				$matches=0;
				foreach($shifts as $shift) {
					if ($shift->is_now(sprintf('%u%02u%02u',$d, $h,$m))) {
						$matches++;
						$match=$shift;
					}
					
				}
				if($matches == 0)
					$table.='<td class="shiftcell closed d'.$d.'" >&nbsp;</td>'."\n";
				else
					$table.=sprintf("<td title=\"%s\" class=\"shiftcell open d%u\">%s</td>\n",$match->user->name, $d, ($m==0?$match->user->name:""));

				if($matches >1) error(sprintf('mehr als ein match: d:%s h:%s m:%s', $d, $h, $m));
			}
			$table.="</tr>\n";
		}
	}
	$table.="</table>\n";
	print $table;
}

function get_user($uid){
	#global $sql:
	#$result = $sql->query('SELECT * FROM '.$cfg['dbprefix'].'users WHERE uid='.$uid.';');
	
	$user=new User($uid);
	return $user;
}
?>
