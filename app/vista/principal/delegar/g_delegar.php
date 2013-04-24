<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();

	$rowlog["ID"]="";
	$rowlog["IDUSUARIOMOD"]=$_SESSION["user"];
	
// RECHAZO DE ASIGNACION
	if($_SESSION["user"]){
		
		if($_GET["opcion"] ==1)
		 {
			$row["DELEGAR"]=0;
			$row["USUARIODELEGAHACIA"]="";
			$row["USUARIODELAGADOPOR"]="";
				
			if($_GET["idasistencia"])	$respuesta=$con->update("$con->temporal.asistencia",$row,"WHERE ARRSTATUSASISTENCIA='PRO' AND IDASISTENCIA='".$_GET["idasistencia"]."'");
			
			$rowlog["IDASISTENCIA"]=$_GET["idasistencia"];
			$rowlog["ARRMOTIVO"]="RECHA";
			$rowlog["USUARIORESPONSABLE"]=$_GET["responsable"];
			
			if($respuesta)	$con->insert_reg("$con->temporal.delegar_asistencia_log",$rowlog);	
		 
		 }	
	//RECHAZO ASIGNACION RESPONSABLE
		 else if($_GET["opcion"] ==2)
		 {
			
			$row["USUARIODELEGAHACIA"]="";
			$row["USUARIODELAGADOPOR"]="";
		  
			$row["DELEGAR"]=0;
			
			if($_GET["idasistencia"])	$respuesta=$con->update("$con->temporal.asistencia",$row,"WHERE ARRSTATUSASISTENCIA='PRO' AND IDASISTENCIA='".$_GET["idasistencia"]."'");
			
			$rowlog["IDASISTENCIA"]=$_GET["idasistencia"];
			$rowlog["ARRMOTIVO"]="RECHA";
			$rowlog["USUARIORESPONSABLE"]=$_GET["responsable"];  
			
			if($respuesta)	$con->insert_reg("$con->temporal.delegar_asistencia_log",$rowlog);
			
		 }
	//ACEPTAR RESPONSABILIDAD
		else if($_GET["opcion"] ==3)
		 {	
			$row["DELEGAR"]=0;
				 
			if($_GET["idasistencia"])	$respuesta=$con->query("UPDATE $con->temporal.asistencia SET IDUSUARIORESPONSABLE=USUARIODELEGAHACIA,DELEGAR=0,USUARIODELEGAHACIA='' WHERE USUARIODELEGAHACIA!='' AND ARRSTATUSASISTENCIA='PRO' AND IDASISTENCIA='".$_GET["idasistencia"]."'");
			
			$rowlog["IDASISTENCIA"]=$_GET["idasistencia"];
			$rowlog["USUARIORESPONSABLE"]=$_SESSION["user"];
			$rowlog["ARRMOTIVO"]="ACEP";
			
			if($respuesta)	$con->insert_reg("$con->temporal.delegar_asistencia_log",$rowlog);	

		 }
	//GRABAR ASIGNAR RESPONSABILIDAD     
		else
		 {
			foreach($_POST["ckbmarcar"] as $idasist){
				if($idasist!=""){
				
					$row["USUARIODELEGAHACIA"]=$_POST["cmbusuario"];
					$row["USUARIODELAGADOPOR"]=$_SESSION["user"];
					$row["DELEGAR"]=1;

					$respuesta=$con->update("$con->temporal.asistencia",$row,"WHERE ARRSTATUSASISTENCIA='PRO' AND IDASISTENCIA='$idasist'");	
					$usuarioresp=$con->consultation("select IDUSUARIORESPONSABLE from $con->temporal.asistencia WHERE IDASISTENCIA='$idasist'");	

					$rowlog["IDASISTENCIA"]=$idasist;
					$rowlog["USUARIODELEGADOR"]=$_SESSION["user"];
					$rowlog["USUARIORESPONSABLE"]=$usuarioresp[0][0];
					$rowlog["USUARIODELEGADOHACIA"]=$_POST["cmbusuario"];
					$rowlog["ARRMOTIVO"]="ASIG";
					
					if($respuesta)	$respuesta_acep=$con->insert_reg("$con->temporal.delegar_asistencia_log",$rowlog);
					$usuarioresp[0][0]="";
			
				}
			}			
		 }
	}
	echo "<script>";
	//if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='gestion_delegar.php'";
    echo "</script>";	
?>