<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	$reg[IDASISTENCIA]=$_GET["idasistencia"];
	$reg[IDUBIGEODESTINO]=$_POST["IDDESTINO"];
	$reg[IDTALLER] = $_POST["cmbtaller"];
	$reg[DESCRIPCION] = strtoupper($_POST["txtadescripcion"]);
	$reg[COMENTARIO] = strtoupper($_POST["txtacomentario"]);
	$reg[IDUSUARIOMOD]=$_POST["IDUSUARIOMOD"];
	//$reg[IDTIPOGRUA]= $_POST["IDTIPOGRUA"];
	if($con->exist("asistencia_vehicular_remolque","IDASISTENCIA"," where IDASISTENCIA = '$_GET[idasistencia]' "))
	{
		$resp=$con->update("asistencia_vehicular_remolque",$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
	}
	else 
	{
		$resp=$con->insert_reg("asistencia_vehicular_remolque",$reg);	
	}	 	

if ($_POST[IDDESTINO]!=''){
	
	$ubigeo_remolque_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_vehicular_remolque_destino',$ubigeo_remolque_destino," WHERE ID ='$_POST[IDDESTINO]'");	

}

		
?>