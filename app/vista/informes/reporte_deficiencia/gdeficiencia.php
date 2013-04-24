<?php
	
	session_start();  
	
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con= new DB_mysqli();
 
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
 
	$nombrexist=$con->consultation("select count(*) as cantidad from $con->catalogo.catalogo_modeloplantilla_informe where TIPO='DEFICIENCIA' AND IDUSUARIO='".$_SESSION["user"]."' and NOMBRE='".strtoupper(trim($_POST['txtnombre']))."' ");
 // echo $nombrexist[0][0];
  // die("");
 
	if($nombrexist[0][0]==0 and $_GET["valor"]!="btneliminar")
	 {
			$rows["IDMODELO"]="";
			$rows["NOMBRE"]=strtoupper(trim($_POST['txtnombre']));
			$rows["TIPO"]="DEFICIENCIA";
			$rows["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->catalogo.catalogo_modeloplantilla_informe",$rows);
			$valorid=$con->reg_id();

		foreach($_POST['chkexpediente'] as $indice => $nombre1){
		
			$rows1["ID"]="";
			$rows1["CLAVE"]=$nombre1;
			$rows1["IDMODELO"]=$valorid;
			$rows1["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows1);			 

		}	
 			
		foreach($_POST['chkasistencia'] as $indice => $nombre2){
		
			$rows2["ID"]="";
			$rows2["CLAVE"]=$nombre2;
			$rows2["IDMODELO"]=$valorid;
			$rows2["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows2);			 
				
		}	
 		 			
		foreach($_POST['chkdeficiencia'] as $indice => $nombre3){
		
			$rows3["ID"]="";
			$rows3["CLAVE"]=$nombre3;
			$rows3["IDMODELO"]=$valorid;
			$rows3["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows3); 				
		}	
 		 			
		foreach($_POST['chkauditoria'] as $indice => $nombre4){
		
			$rows4["ID"]="";
			$rows4["CLAVE"]=$nombre4;
			$rows4["IDMODELO"]=$valorid;
			$rows4["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows4);			 
				
		}
		
		foreach($_POST['chkencuesta'] as $indice => $nombre4){
		
			$rows4["ID"]="";
			$rows4["CLAVE"]=$nombre4;
			$rows4["IDMODELO"]=$valorid;
			$rows4["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows4);			 
				
		}	
 		 			
		foreach($_POST['chkfecha'] as $indice => $nombre5){
		
			$rows5["ID"]="";
			$rows5["CLAVE"]=$nombre5;
			$rows5["IDMODELO"]=$valorid;
			$rows5["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows5);			 
				
		}

	 }
	else if($nombrexist[0][0]==1 and  $_POST["cmbmodelo"]  and $_GET["valor"]!="btneliminar")
	 {			 
		$valorid=$_POST["cmbmodelo"];

		$respuesta=$con->update("$con->temporal.catalogo_modeloplantilla_informe",$rows,"WHERE IDMODELO='".$valorid."'");
 		$con->query("delete from $con->temporal.acceso_transferencia_deficiencia where IDMODELO='".$valorid."'");
		
		if($resultado)	
		 {
		 	$rows["IDMODELO"]="";
			$rows["NOMBRE"]=strtoupper(trim($_POST['txtnombre']));
			$rows["TIPO"]="DEFICIENCIA";
			$rows["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.catalogo_modeloplantilla_informe",$rows);
			$valorid=$con->reg_id();
		 }	
		
		foreach($_POST['chkexpediente'] as $indice => $nombre1){

			$rows1["ID"]="";
			$rows1["CLAVE"]=$nombre1;
			$rows1["IDMODELO"]=$valorid;
			$rows1["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows1);			 

		}	
			
		foreach($_POST['chkasistencia'] as $indice => $nombre2){

			$rows2["ID"]="";
			$rows2["CLAVE"]=$nombre2;
			$rows2["IDMODELO"]=$valorid;
			$rows2["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows2);			 
				
		}	
					
		foreach($_POST['chkdeficiencia'] as $indice => $nombre3){

			$rows3["ID"]="";
			$rows3["CLAVE"]=$nombre3;
			$rows3["IDMODELO"]=$valorid;
			$rows3["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows3); 				
		}	
					
		foreach($_POST['chkauditoria'] as $indice => $nombre4){

			$rows4["ID"]="";
			$rows4["CLAVE"]=$nombre4;
			$rows4["IDMODELO"]=$valorid;
			$rows4["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows4);			 
				
		}	
					
		foreach($_POST['chkencuesta'] as $indice => $nombre4){
		
			$rows4["ID"]="";
			$rows4["CLAVE"]=$nombre4;
			$rows4["IDMODELO"]=$valorid;
			$rows4["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows4);			 
				
		}						
		foreach($_POST['chkfecha'] as $indice => $nombre5){

			$rows5["ID"]="";
			$rows5["CLAVE"]=$nombre5;
			$rows5["IDMODELO"]=$valorid;
			$rows5["IDUSUARIO"]=$_SESSION['user'];	
			$con->insert_reg("$con->temporal.acceso_transferencia_deficiencia",$rows5);			 
				
		}

		
	 }
	else if($_POST["cmbmodelo"]  and $_GET["valor"]=="btneliminar")
	 {	
		$resultado=$con->query("delete from $con->catalogo.catalogo_modeloplantilla_informe where  IDUSUARIO='".$_SESSION["user"]."' and IDMODELO='".$_POST["cmbmodelo"] ."'");
		if($resultado)	$con->query("delete from $con->temporal.acceso_transferencia_deficiencia where IDMODELO='".$_POST["cmbmodelo"] ."'");
	 
	 }
	 
	echo "<script>";	
	echo "document.location.href='general.php?idmodelo=$valorid'";
    echo "</script>";	 
	  
?>
