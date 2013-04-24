<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	$reg[IDASISTENCIA]=$_GET["idasistencia"];
	$reg[IDUBIGEODESTINO]=$_POST["IDDESTINO"];
	$reg[POSICIONPUERTA] = $_POST["txtaposicionp"];
	$reg[DESCRIPCION] = strtoupper($_POST["txtadescripcion"]);
	$reg[IDUSUARIOMOD]=$_POST["IDUSUARIOMOD"];

	if($con->exist("asistencia_vehicular_cerrajeriavial","IDASISTENCIA"," where IDASISTENCIA = '$_GET[idasistencia]' "))
	{
		$resp=$con->update("asistencia_vehicular_cerrajeriavial",$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
	}
	else 
	{
		$resp=$con->insert_reg("asistencia_vehicular_cerrajeriavial",$reg);	
	}
	 
	if ($_POST[IDDESTINO]!=''){
		
		$ubigeo_cerrajeriav_destino[IDASISTENCIA]=$_GET[idasistencia];
		$con->update($con->temporal.'.asistencia_vehicular_cerrajeriavial_destino',$ubigeo_cerrajeriav_destino," WHERE ID ='$_POST[IDDESTINO]'");	
		
	}
	
?>