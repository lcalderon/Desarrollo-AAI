<?php

	session_start();
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/clase_lang.inc.php");
	
	$con= new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
 	//actuliazar  afiliado
		
		$resp=0;

		if($_POST["idexpediente"]){
			
			$rowexp["CVEAFILIADO"]=$_POST["cveafiliado"];
			$rowexp["IDPROGRAMA"]=$_POST["cmbplan"];
			$rowexp["ASIGNARTITULAR"]=1;
			$rowexp["IDUSUARIOASIGNACION"]=$_SESSION["user"];
			
			$respuesta=$con->update("$con->temporal.expediente",$rowexp,"WHERE IDEXPEDIENTE='".$_POST['idexpediente']."' AND ASIGNARTITULAR=0 AND ARRSTATUSEXPEDIENTE='PRO'");

			if($respuesta){
				
				$rowasis["IDPROGRAMA"]=$_POST["cmbplan"];				
				$con->update("$con->temporal.asistencia",$rowasis,"WHERE IDEXPEDIENTE='".$_POST['idexpediente']."'");
				$resp=1;
			}
			
		}		
 
		echo $resp;
?>