#!/usr/local/bin/php -q
 <?php
include('soaang_agi_func.php');
$matriz_ini = parse_ini_file("/etc/asterisk/demo_soaang_agi.conf");

 $host =  $matriz_ini[host];
 $db =  $matriz_ini[db];
 $user =  $matriz_ini[user];
 $pass =  $matriz_ini[pass];

 $link = conectadb($host,$user,$pass,$db);
setCall2($link,$_SERVER['argv'][1],$_SERVER['argv'][2],$_SERVER['argv'][3],$_SERVER['argv'][4]);

 ?>

