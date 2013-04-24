<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	$reg[IDASISTENCIA]=$_GET["idasistencia"];
	$reg[NOMBRECONDUCTOR] = $_POST["txtconductor"];
	$reg[IDPARENTESCO] = $_POST["cmbparentesco"];
	$reg[IDUBIGEODESTINO]=$_POST["IDDESTINO"];
	$reg[DESCRIPCION] = strtoupper($_POST["txtadescripcion"]);
	$reg[COMENTARIO] = strtoupper($_POST["txtacomentario"]);
	$reg[IDUSUARIOMOD]=$_POST["IDUSUARIOMOD"];

	if($con->exist("asistencia_vehicular_choferremplazo","IDASISTENCIA"," where IDASISTENCIA = '$_GET[idasistencia]' "))
	{
		$resp=$con->update("asistencia_vehicular_choferremplazo",$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
	}
	else 
	{
		$resp=$con->insert_reg("asistencia_vehicular_choferremplazo",$reg);	

	}
		
	if ($_POST[IDDESTINO]!=''){
		
		$ubigeo_choferemp_destino[IDASISTENCIA]=$_GET[idasistencia];
		$con->update($con->temporal.'.asistencia_vehicular_choferemplazo_destino',$ubigeo_choferemp_destino," WHERE ID ='$_POST[IDDESTINO]'");	
		
	}

?>