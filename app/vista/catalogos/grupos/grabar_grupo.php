<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	session_start();	
	
	$piezas = explode(" ",strtoupper($_POST["txtnombre"]));
	  
	if(count($piezas) ==1)	$codigo=substr($piezas[0],0,4);	else $codigo=substr($piezas[0],0,3).substr($piezas[1],0,1);
			
	$rows["IDGRUPO"]=$codigo;
	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["FECHAMOD"]="";

	//Inserta los datos

	$respuesta=$con->insert_reg("$con->catalogo.catalogo_grupo",$rows);	 
  
	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='edit_catalogo.php?codigo=".$codigo."'";
    echo "</script>";	
?>