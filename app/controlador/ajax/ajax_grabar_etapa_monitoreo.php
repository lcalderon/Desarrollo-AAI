<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$asis_usuario[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$asis_usuario[IDETAPA]=$_POST[IDETAPA]-1;

$con->insert_reg("$con->temporal.asistencia_usuario",$asis_usuario);

//  actualiza la etapa en la tabla ASISTENCIA
//$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
//echo $_POST[IDETAPA];
if($_POST[IDETAPA]=='4')
{
    $asis[IDETAPA]=6;
}else{
    $asis[IDETAPA]=$_POST[IDETAPA];
}

$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$_POST[IDASISTENCIA]'");





	
	

return;
?>