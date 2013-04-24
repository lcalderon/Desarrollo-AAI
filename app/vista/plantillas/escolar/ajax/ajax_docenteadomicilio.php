<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$horario=$_POST["dateesc1"]." ".$_POST["dcbhora1"].":".$_POST["dcbminuto1"];

$reg[IDASISTENCIA]=$_GET["idasistencia"];
$reg[MATERIAS] = $_POST["txtmaterias"];
$reg[HORARIO] = $horario;
$reg[OTROS] = $_POST["txtaotros"];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_escolar_docenteadomicilio','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_escolar_docenteadomicilio',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_escolar_docenteadomicilio',$reg);	
}

// if ($_POST[IDDESTINO]!=''){
	
	// $asis_pc_visitatecnica_destino[IDASISTENCIA]=$_GET[idasistencia];
	// $con->update($con->temporal.'.asistencia_escolar_docenteadomicilio_destino',$asis_pc_visitatecnica_destino," WHERE ID ='$_POST[IDDESTINO]'");	
	
// }


?>