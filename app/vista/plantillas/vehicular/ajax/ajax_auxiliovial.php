<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	$reg[IDASISTENCIA]=$_GET["idasistencia"];
	$reg[IDUBIGEODESTINO]=$_POST["IDDESTINO"];
	$reg[ARRTIPOAUXILIO] = $_POST["cmbtipoauxilio"];
	$reg[DESCRIPCION] = strtoupper($_POST["txtadescripcion"]);
	$reg[COMENTARIO] = strtoupper($_POST["txtacomentario"]);
	$reg[IDUSUARIOMOD]=$_POST["IDUSUARIOMOD"];
	$reg[NUMERORECLAMO] = $_POST[NUMERORECLAMO];
	$reg[NOMBREAJUSTADOR] = $_POST[NOMBREAJUSTADOR];

	if($con->exist("asistencia_vehicular_auxiliovial","IDASISTENCIA"," where IDASISTENCIA = '$_GET[idasistencia]' "))
	{
		$resp=$con->update("asistencia_vehicular_auxiliovial",$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
	}
	else 
	{
		$resp=$con->insert_reg("asistencia_vehicular_auxiliovial",$reg);	
	}

	if ($_POST[IDDESTINO]!=''){
		
		$ubigeo_auxiliov_destino[IDASISTENCIA]=$_GET[idasistencia];
		$con->update($con->temporal.'.asistencia_vehicular_auxiliovial_destino',$ubigeo_auxiliov_destino," WHERE ID ='$_POST[IDDESTINO]'");	
		
	}

 
?>