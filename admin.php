<?php
//if(!$_SERVER['HTTPS'])
//	header(sprintf('Location: https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]));
require('config.php');
require('classes/user.php');
require('classes/shift.php');
include('functions.php');
session_start();

$alerts=array();

if(!isset($_SESSION['user'])) $_SESSION['user'] =false;
if(isset($_POST['action']) && $_POST['action'] == 'login'){
	$user = new User($_POST['name']);
	$user->passhash=sha1($_POST['password']);
	if($user->login())
		$_SESSION['user']=$user;
	else
		$alerts[]="Nutzername und/oder Passwort falsch.";
}

if($_SESSION['user']) 
	$_SESSION['user']->select();

if(isset($_POST['action'])){
	if($_POST['action'] == 'logout')				logout();
	if($_POST['action'] == 'new_shift')			new_shift();
	if($_POST['action'] == 'delete_shift')	delete_shift();
	if($_POST['action'] == 'add_user')			add_user();
	if($_POST['action'] == 'lost_passwd')		lost_passwd();
}
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
		<?php 
		  if(isset($_SESSION['user']) && $_SESSION['user']) 
			  print "<span id=\"loggedin\">Eingeloggt als ".$_SESSION['user']->name.".</span>";
		?>
		<ul>
			<li><a class="navlink" href="./">home</a></li>
			<li><a class="navlink" href="./admin.php">admin</a></li>
		</ul>
		<div style="clear:both;" />
		</nav>
		<hgroup>
		<h1>Sofa-Cafe</h1>
		<h2>Admininterface</h2>
		<?php foreach($alerts as $alert) printf('<h3>%s</h3>', $alert); ?>
		</hgroup>
	</header>
	<div class="content">
		<div class="left">
			<?php 
				if(isset($_SESSION['user']) && $_SESSION['user'])
					include('panel.inc.php');
				else {
					if(isset($_GET['action']) && $_GET['action'] == 'lost_passwd') 
						include('lost_passwd.inc.php');
					else
						include('login.inc.php');
				}
			?>
		</div>
		<div class="right">
<?php
write_table();
$sql=null;
?>
</div>
	</div>
	<footer>
		<p>fuer die sache.</p>
	</footer>
</body>
</html>
