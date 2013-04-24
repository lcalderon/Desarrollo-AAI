<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	session_start();	
 
	$con->query("DELETE FROM $con->temporal.grupo_usuario WHERE IDGRUPO='".$_POST['codgrupo']."'");
	$con->query("DELETE FROM $con->temporal.grupo_emailexterno WHERE IDGRUPO='".$_POST['codgrupo']."'");
	
		if($_POST["txtopc"])	
		 {
			$piezas = explode(" ",strtoupper($_POST["txtnomnuevo"]));
	  
			if(count($piezas) ==1)	$codigo=substr($piezas[0],0,4);	else $codigo=substr($piezas[0],0,3).substr($piezas[1],0,1);
	  
			$rows1["IDGRUPO"]=$codigo;
			$rows1["NOMBRE"]=strtoupper($_POST["txtnomnuevo"]);
			$rows1["ACTIVO"]=1;
			$rows1["IDUSUARIOMOD"]=$_SESSION["user"];
			
			$valorid=$con->insert_reg("$con->catalogo.catalogo_grupo",$rows1);
			
			$_POST["codgrupo"]=$codigo;		 
		 }
		
		foreach($_POST['txtemail'] as $indice => $email){		

				$emails[$indice]=$email;				
		} 

		foreach($_POST['txtusuario'] as $indiceusu => $usuario){

			$rows2["IDUSUARIO"]=$usuario;
			$rows2["IDGRUPO"]=$_POST["codgrupo"];
			$rows2["EMAIL"]=$emails[$indiceusu];
			$rows2["IDUSUARIOMOD"]=$_SESSION["user"];
			
			if($usuario!="")	$con->insert_reg("$con->temporal.grupo_usuario",$rows2);
		}

//grabar emails externos
		
		foreach($_POST['txtmasemail'] as $indice => $emailext){		

			$rowext["IDGRUPO"]=$_POST["codgrupo"];
			$rowext["EMAIL"]=$emailext;
			
			if($emailext!="")	$con->insert_reg("$con->temporal.grupo_emailexterno",$rowext);
				
		} 		
 		
		if($_POST["txtopc"])
		 {
			echo "<script>";
			echo "document.location.href='gestioncuentas.php'";
			echo "</script>";
		 }	
?>
