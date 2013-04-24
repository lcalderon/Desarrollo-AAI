#!/usr/local/bin/php -q
 <?
include('soaang_agi_func.php');
$origen = $_SERVER['argv'][5];
if($origen=='DEMO'){
	$matriz_ini = parse_ini_file("/etc/asterisk/demo_soaang_agi.conf");
	
}
else{
	$matriz_ini = parse_ini_file("/etc/asterisk/soaang_agi.conf");
}

 $host =  $matriz_ini[host];
 $db =  $matriz_ini[db];
 $user =  $matriz_ini[user];
 $pass =  $matriz_ini[pass];
 $db2 =  $matriz_ini[db2];

 $link = conectadb($host,$user,$pass,$db);
setCall2(
$link,$_SERVER['argv'][1],
$_SERVER['argv'][2],
$_SERVER['argv'][3],
$_SERVER['argv'][4],
$db2
);

 ?>

