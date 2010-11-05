<?php
define('DEBUG', true);

$weekdays=array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag");
$cfg=array();
$cfg['dbprefix']='';
$cfg['dbfile']='/srv/http/sofa/test.sqlite';

$sql=new PDO('sqlite:'.$cfg['dbfile']); //SQLite3($cfg['dbfile']);
//$sql=new SQLite3($cfg['dbfile']);

?>
