<?php
	session_start();
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
	$con->select_db($con->temporal);
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$idasistencia="";

 // DATOS PARA LA TABLA ASISTENCIA 

	$asis[IDEXPEDIENTE]=$_POST["IDEXPEDIENTE"];
	$asis[IDFAMILIA]=$_POST["IDFAMILIA"];
	$asis[IDSERVICIO]=$_POST["IDSERVICIO"];
	$asis[ARRSTATUSASISTENCIA]=$_POST["ARRSTATUSASISTENCIA"];
	$asis[ARRCONDICIONSERVICIO]= $_POST["ARRCONDICIONSERVICIO"];
	$asis[IDETAPA]=$_POST["IDETAPA"];
	$asis[IDCUENTA]=$_POST["IDCUENTA"];
	$asis[IDPROGRAMA]=$_POST["IDPROGRAMA"];
	$asis[IDPROGRAMASERVICIO]=$_POST[IDPROGRAMASERVICIO];	
	$asis[IDLUGARDELEVENTO]=$_POST[IDLUGARDELEVENTO];
	$asis[ARRAMBITO]=$_POST["cmbambito"]; 
	$asis[FECHAREGISTRO]=date("Y-m-d H:i:s");
	
	if ($_POST[ARRCONDICIONSERVICIO]=='GAR')
		 $asis[GARANTIA_REL]=$_POST[GARANTIA_REL];
	else	
		  $asis[GARANTIA_REL]='';

	$asis[REEMBOLSO]=($_POST[REEMBOLSO]=='on')?1:0;
	if ($_POST[REEMBOLSO]=='on') $asis[REPORTADO]=$_POST[REPORTADO];
	  else $asis[REPORTADO]=0;
  

	if ($_POST["IDASISTENCIA"]=='')
	{
		$asis[IDUSUARIORESPONSABLE] = $_POST["IDUSUARIO"];
		$con->insert_reg($con->temporal.'.asistencia',$asis);
		$idasistencia = $con->reg_id();	
		
			$rowexpusu[IDEXPEDIENTE] = $_POST["IDEXPEDIENTE"];
			$rowexpusu[FECHAHORA] = $_POST["fechareg"];
			$rowexpusu[IDUSUARIO] = $_POST["IDUSUARIO"];
			$rowexpusu[ARRTIPOMOVEXP] = "TR1";

			if($idasistencia)	$con->insert_reg($con->temporal.".expediente_usuario",$rowexpusu);

			  
	}
	else {
		$asis[IDUSUARIOMOD] = $_POST["IDUSUARIO"];
		$con->update($con->temporal.'.asistencia',$asis," where IDASISTENCIA = '$_POST[IDASISTENCIA]' ");
		$idasistencia = $_POST["IDASISTENCIA"];
	}

//GRABANDO UBIGEO ASISTENCIA

	if ($_POST[IDLUGARDELEVENTO]!=''){
		$asis_ubigeo[IDASISTENCIA]=$idasistencia;
		$asis_ubigeo[IDEXPEDIENTE]=$_POST["IDEXPEDIENTE"];
		$con->update($con->temporal.'.asistencia_lugardelevento',$asis_ubigeo," where ID='$_POST[IDLUGARDELEVENTO]'");
	}
	
	$asis_just[IDASISTENCIA] = $idasistencia;
	$asis_just[CVEJUSTIFICACION] ='2';
	$asis_just[MOTIVO] = $_POST[JUSTIFICACION];
	$asis_just[IDUSUARIOMOD] =$_POST[IDUSUARIO];

	//$con->insert_reg($con->temporal.'.asistencia_justificacion',$asis_just);

	//  DATOS PARA LA TABLA ASISTENCIA_USUARIO  
	$asis_usuario[IDASISTENCIA]=$idasistencia;
	$asis_usuario[IDUSUARIO]=$_POST["IDUSUARIO"];
	$asis_usuario[IDETAPA]=$_POST["ETAPACULMINADA"];
	$asis_usuario[OBSERVACION]=$_POST["OBSERVACION"];
	$con->insert_reg($con->temporal.'.asistencia_usuario',$asis_usuario);	
	/*
	if ($_POST[IDASISTENCIA]=='')
	{
	$dis[IDASISTENCIA] = $idasistencia;
	$dis[FECHAINI] = $_POST['date'].' '.$_POST[cbhora1].':'.$_POST[cbminuto1].':00';
	$dis[FECHAFIN] = $_POST['date4'].' '.$_POST[cbhora2].':'.$_POST[cbminuto2].':00';
	$dis[IDUSUARIOMOD] =$_POST[IDUSUARIO];
	$con->insert_reg($con->temporal.'.asistencia_disponibilidad_afiliado',$dis);
	}
*/
	//crear vehiculo
	
	$pervehiculo[IDASISTENCIA]=$idasistencia;
	$pervehiculo[MARCA]=$_POST["txtmarca"];
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
	
	if($_POST["idvehiculo"]=='')
	{
		$con->insert_reg($con->temporal.'.asistencia_vehicular_datosvehiculo',$pervehiculo);
		$idvehiculo = $con->reg_id();
	}
	else {
		$con->update($con->temporal.'.asistencia_vehicular_datosvehiculo',$pervehiculo," where ID = '$_POST[idvehiculo]'  ");
		$idvehiculo = $_POST["idvehiculo"];

	}	

	//DATOS PARA LA TABLA ASISTENCIA_VEHICULAR  
	
	$vehicular[IDASISTENCIA]= $idasistencia;
	$vehicular[IDCONTACTANTE]= $_POST["IDCONTACTANTE"];
	$vehicular[IDSERVICIO]=$_POST["IDSERVICIO"];
	$vehicular[ARRUBICACIONFISICAVEH]=$_POST["TIPOINMUEBLE"]; 
	$vehicular[IDVEHICULO]=$idvehiculo;
	$vehicular[ARRSERVICIOPOR]=$_POST["cmbserviciopor"]; 
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