<?php
require('classes/user.php');
require('classes/shift.php');
require('config.php');
include('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="style.css" />
	<!-- Atom feed
	<link rel="alternate"
		type="application/atom+xml"
		title="My Weblog feed"
		href="/feed/" />
	-->
</head>
<body>
	<header>
		<nav>
		<ul>
			<li><a class="navlink" href="./">home</a></li>
			<li><a class="navlink" href="admin.php">admin</a></li>
		</ul>
		</nav>
		<hgroup>
		<h1>Sofa-Cafe</h1>
		<!-- <h2>A lof of effort went into making this effortless.</h2> -->
		</hgroup>
	</header>
	<div class="content">
		<div class="left">
			<div class="adminsection">
				<p>
				Das Sofa Café liegt im Norden des TUHH Campus in der sogenannten "Baracke". Ihr findet hier nicht nur gemütliche Sofas, sondern auch nette Menschen die gegen Spende Kaffee, Getränke und Schokoriegel verteilen. 
				Kommt doch gern vorbei.</p>
				<p>
				PS: Wer gern helfen möchte meldet sich am besten im Café.</p>
			</div>
			<div class="adminsection">
			<h2>Lage</h2>
			<img id="lagebild" src="http://www.tu-harburg.de/sofa/campusplan_color.png"/>	
			</div>
			<div class="adminsection">
			<h2>Kontakt</h2>
			<p>
			Die Sofa AG ist ein offzielle AG des AStA der Technischen Universität Hamburg-Harburg. (asta.tu-harburg.de)</p>
<p>
			Anschrift:</p>
			<p class="adresse">
			Sofa AG<br>
			c/o Allgemeiner Studierendenausschuss der<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Technischen Universität Hamburg-Harburg<br>
			Schwarzenbergstraße 95<br>
			21073 Hamburg<br>
			Telefonisch: 040 42478-2916</p>

			</div>
		</div>
		<div class="right">
<?php


//make_tables();

write_table();

?>
</div>
	</div>
	<footer>
		<p>fuer die sache.</p>
	</footer>
</body>
</html>
