<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];

$reg[DESCRIPCIONSERVICIO]=$_POST[DESCRIPCIONSERVICIO];
$reg[SUBSERVICIO]=$_POST[SUBSERVICIO];
$fecha1 = $_POST[FECHAINI];
$fecha2 = $_POST[FECHAFIN];
$hora1 = $_POST[cbhoraini];
$hora2 = $_POST[cbhorafin];
$minuto1 = $_POST[cbminutoini];
$minuto2 = $_POST[cbminutofin];

$reg[FECHAINICIO] = $fecha1.' '.$hora1.':'.$minuto1.':00';
$reg[FECHAFIN] = $fecha2.' '.$hora2.':'.$minuto2.':00';

$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_hogar_seguridad','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_hogar_seguridad',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_hogar_seguridad',$reg);	
}

//print_r($_POST);

?>


