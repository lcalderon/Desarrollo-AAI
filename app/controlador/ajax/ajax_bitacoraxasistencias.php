<?
include_once('/app/modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$db = $con->temporal;
$con->select_db($db);


$reg[NUMASISTENCIA]=$_POST[NUMASISTENCIA];
$reg[TEXTO]=$_POST[TEXTO];
$reg[CVEUSUARIO]=$_POST[CVEUSUARIO];

$con->insert_reg('bitacoraxasistencias',$reg);



echo 'El dato se grabo correctamente';



?>