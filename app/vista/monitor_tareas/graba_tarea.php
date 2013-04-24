<?php 
session_start();

if (!isset($_SESSION['user'])) echo json_encode(array("status"=>false));
else {
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
$idasistencia = $_POST['IDASISTENCIA'];
$idexpediente = $_POST['IDEXPEDIENTE'];
$idtarea =$_POST['IDTAREA'];
$fechatarea = $_POST['FECHATAREA'];
$statustarea = 'PENDIENTE';
$idusuario = $_SESSION['user']; 
$con = new DB_mysqli();


$sql="insert into $con->temporal.monitor_tarea set 
		IDASISTENCIA = '$idasistencia',
		IDEXPEDIENTE = '$idexpediente', 
		IDTAREA ='$idtarea', 
		FECHATAREA ='$fechatarea' ,
		STATUSTAREA ='$statustarea',
		IDUSUARIO ='$idusuario'
		";
$con->query($sql);

if ($con->errno==0) echo json_encode(array("status"=>true));
else echo json_encode(array("status"=>false));
}
?>