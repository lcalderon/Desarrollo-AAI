<?php

	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
	$con->select_db($con->temporal);
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	// crear persona vehiculo
	
	$pervehiculo[IDAFILIADO]=$_POST["idafiliado"];
	$pervehiculo[SUBMARCA]=$_POST["txtsubmarca"];
	$pervehiculo[SUBMARCA]=$_POST["txtsubmarca"];
	$pervehiculo[ANIO]=$_POST["cmbanio"];
	$pervehiculo[COLOR]=$_POST["txtcolor"];
	$pervehiculo[PLACA]=$_POST["txtplaca"];
	$pervehiculo[NUMVIN]=$_POST["txtvim"];
	$pervehiculo[NUMSERIEMOTOR]=$_POST["txtmotor"];
	$pervehiculo[NUMSERIECHASIS]=$_POST["txtserie"];
	$pervehiculo[USO]=$_POST["cmbuso"];
	$pervehiculo[IDFAMILIAVEH]=$_POST["cmbfamilia"];
	$pervehiculo[ARRCOMBUSTIBLE]=$_POST["cmbcombustible"];
	$pervehiculo[ARRTRANSMISION]=$_POST["cmbtrasmision"];
	$pervehiculo[ARRPESO]=$_POST["cmbpeso"];
	$pervehiculo[IDUSUARIOMOD]=$_POST["IDUSUARIO"];
	
	if($_POST["idvehiculo"]=='' and )
	{
		$con->insert_reg($con->catalogo.'.catalogo_afiliado_persona_vehiculo',$pervehiculo);
		$idvehiculo = $con->reg_id();
	}
	else {
		$con->update($con->catalogo.'.catalogo_afiliado_persona_vehiculo',$pervehiculo," where ID = '$_POST[idvehiculo]'  ");
		$idvehiculo = $_POST["idvehiculo"];

	}	

	// DATOS PARA LA TABLA ASISTENCIA_VEHICULAR  
	
	$vehicular[IDASISTENCIA]= $idasistencia;
	$vehicular[IDCONTACTANTE]= $_POST["IDCONTACTANTE"];
	$vehicular[IDSERVICIO]=$_POST["IDSERVICIO"];
	$vehicular[LUGARDELEVENTO]=$_POST["IDLUGAR"];
	$vehicular[ARRUBICACIONFISICAVEH]=$_POST["TIPOINMUEBLE"]; 
	$vehicular[ARRAMBITO]=$_POST["cmbambito"]; 
	$vehicular[IDVEHICULO]=$idvehiculo;
	$vehicular[IDUSUARIOMOD]=$_POST["IDUSUARIO"];	

	if ($_POST["IDASISTENCIA"]=='')
	{
		$con->insert_reg($con->temporal.'.asistencia_vehicular',$vehicular);
	}
	else {
		$con->update($con->temporal.'.asistencia_vehicular',$vehicular," where IDASISTENCIA = '$_POST[IDASISTENCIA]'  ");

	}

	echo  $idasistencia.",".$idvehiculo;
 
?>