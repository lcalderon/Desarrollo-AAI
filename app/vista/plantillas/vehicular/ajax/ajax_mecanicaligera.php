<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	$reg[IDASISTENCIA]=$_GET["idasistencia"];
	$reg[IDUBIGEODESTINO]=$_POST["IDDESTINO"];
	$reg[ARRTIPOAUXILIO] = $_POST["cmbparentesco"];
	$reg[DESCRIPCION] = strtoupper($_POST["txtadescripcion"]);
	$reg[COMENTARIO] = strtoupper($_POST["txtacomentario"]);
	$reg[IDUSUARIOMOD]=$_POST["IDUSUARIOMOD"];

	if($con->exist("asistencia_vehicular_mecanicaligera","IDASISTENCIA"," where IDASISTENCIA = '$_GET[idasistencia]' "))
	{
		$resp=$con->update("asistencia_vehicular_mecanicaligera",$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
	}
	else 
	{
		$resp=$con->insert_reg("asistencia_vehicular_mecanicaligera",$reg);	
	}

	 
 
?>