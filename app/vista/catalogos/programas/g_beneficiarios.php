<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();	 
 		
  	$codigob=$_POST["idpro"];
	$codigotb=$_POST["idtipob"];
	$codigoserv=$_POST["idserv"];
		
	 if(!$_POST["idserv"])
	 {
		$rowb["ACTIVO"]=0;		
		$con->update("catalogo_programa_beneficiario",$rowb,"WHERE IDPROGRAMA='$codigob' and IDTIPOBENEFICIARIO not in(".$_POST["valor"].") ");
		
		$row["ACTIVO"]=1;			
		$respuesta=$con->update("catalogo_programa_beneficiario",$row,"WHERE IDPROGRAMA='$codigob' and IDTIPOBENEFICIARIO  in(".$_POST["valor"].") ");

		if($_POST["opc"]=="all")
		 {
		
			$campo["ACTIVO"]=0;
			$respuesta=$con->update("catalogo_programa_servicio_beneficiario",$campo,"WHERE IDPROGRAMA='$codigob' ");
			
			$rowbe["ACTIVO"]=1;
			$respuesta=$con->update("catalogo_programa_servicio_beneficiario",$rowbe,"WHERE IDPROGRAMA='$codigob' and IDTIPOBENEFICIARIO in(".$_POST["valor"].") ");
			
		 }
		
		if($_POST["valor"]=="")
		 {
			$rowb["ACTIVO"]=0;		
			$con->update("catalogo_programa_beneficiario",$rowb,"WHERE IDPROGRAMA='$codigob' ");		 
		 }
	 }
	else
	 {
		
		$rowb["ACTIVO"]=0;
		$respuesta=$con->update("catalogo_programa_servicio_beneficiario",$rowb,"WHERE IDPROGRAMA='$codigob' and IDTIPOBENEFICIARIO not in(".$_POST["valor"].") and IDSERVICIO='$codigoserv' ");
		
		$row["ACTIVO"]=1;
		$respuesta=$con->update("catalogo_programa_servicio_beneficiario",$row,"WHERE IDPROGRAMA='$codigob' and IDTIPOBENEFICIARIO in(".$_POST["valor"].") and IDSERVICIO='$codigoserv' ");

		if($_POST["valor"]=="")
		 {
			$rowb["ACTIVO"]=0;		
			$con->update("catalogo_programa_servicio_beneficiario",$rowb,"WHERE IDPROGRAMA='$codigob' and IDSERVICIO='$codigoserv' ");		 
		 }
		
	 }
	  
	  
?>