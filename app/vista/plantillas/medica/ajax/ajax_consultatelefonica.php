<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONFALLA] = $_POST[DESCRIPCIONFALLA];
$reg[DIAGNOSTICO] = $_POST[DIAGNOSTICO];
$reg[SOLUCIONFALLA] = $_POST[SOLUCIONFALLA];
$reg[RECOMENDACIONES] = $_POST[RECOMENDACIONES];
$reg[OTROS] = $_POST[OTROS];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_pc_consultatelefonica','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_pc_consultatelefonica',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_pc_consultatelefonica',$reg);	
}

//print_r($_POST);

?>