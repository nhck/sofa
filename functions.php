<?php

function error($str){}

function logout(){
	$_SESSION = array();
	session_destroy();
}

function lost_passwd(){

}

//taken from http://www.gidnetwork.com/b-16.html and changed a bit.
function get_time_difference( $start, $end )
{
    $uts['start']      =    $start->format('U');
    $uts['end']        =    $end->format('U');
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}

function change_password(){
	global $sql,$alerts,$notifs;
	$act=true;
	if( $_POST['new'] != $_POST['repeat']) {
		$alerts[]="Passwoerter stimmen nicht ueberein";
		$act=false;
	}
	if( $_SESSION['user']->passhash != sha1($_POST['current'])){
		$alerts[]="Aktuelles Passwort ist falsch";
		$act=false;
	}
	if($act){
		$passhash=sha1($_POST['new']);
		$_SESSION['user']->passhash = $passhash;
		if ($_SESSION['user']->update()) $notifs[]="Passwort erfolgreich geaendert";
		else $alerts[]="Unbekannter Fehler, bitte Jan Bescheid geben!";
	}
}

function add_user(){
	global $sql,$cfg;
	$act=true;
	$name=$sql->quote(htmlspecialchars($_POST['name']));
	$fon=$sql->quote(htmlspecialchars($_POST['fon']));
	$email=$sql->quote(htmlspecialchars($_POST['email']));
	$passhash=sha1($_POST['password']);
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
		if ($user->insert()) $notifs[]="User hinzugefuegt.";
		else $alerts[]="Unbekannter Fehler, bitte Jan Bescheid geben!";
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
		if ($shift->delete());
		else $alerts[]="Unbekannter Fehler, bitte Jan Bescheid geben!";
}

function new_shift(){
	global $alerts;
	$act=true;
	$start=sprintf('%02u:%02u',(int)$_POST['starth'],(int)$_POST['startm']);
	$end=sprintf('%02u:%02u',(int)$_POST['endh'],(int)$_POST['endm']);
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
	if($act){
		if($shift->insert()) $notifs[]="Schicht hinuzgefuegt.";
		else $alerts[]="Unbekannter Fehler, bitte Jan Bescheid geben!";
	}
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
	$result->closeCursor();
	return $shifts;
}

function string_splice($string, $offset, $length=0, $replacement=''){
	$a=substr($string, 0,$offset);
	$b=substr($string, $offset+$length);
	return $a.$replacement.$b;
}

function write_table(){
	$shifts=get_shifts();

	// Laufvariablenarray fuer die Rowspan-Berechnung
	$rowspan_counter= array(0,0,0,0,0);

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
			$table.='<td class="shiftcell time">';
				$table.=sprintf('%02u:%02u',$h,$m)."</td>\n";
			for($d=0; $d<5; $d++){
				$matches=0;
					foreach($shifts as $shift) {
						if ($shift->is_now(sprintf('%u,%02u:%02u',$d, $h,$m))) {
							$matches++;
							$match=$shift;
						}
						
					}
					if($matches == 0)
						$table.='<td class="shiftcell closed d'.$d.'" >&nbsp;</td>'."\n";
					else{
						if($rowspan_counter[$d]<=1){
							$diff=get_time_difference($match->start, $match->end);
							$rows=$diff['hours']*4 + $diff['minutes']/15;
							$rowspan_counter[$d] = $rows;

							$table.=sprintf('<td title="%s" rowspan="%s" '
							.'class="shift%s shiftcell open d%u">%s</td>'."\n",
							$match->user->name, $rows, $match->sid, $d, 
								$match->user->name);
						}else{
							$rowspan_counter[$d]--;	
						}
					}

					if($matches >1) 
						error(sprintf('mehr als ein match: d:%s h:%s m:%s', $d, $h, $m));
			}
			$table.="</tr>\n";
		}
	}
	$table.="</table>\n";
	print $table;
}
?>
