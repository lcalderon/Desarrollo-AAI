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
	Auth::required($_POST["txturl"]);	
	 
	//variables POST
 
 	$rows["DESCRIPCION"]=strtoupper($_POST['txtnombre']);
	$rows["COSTONEGOCIADO"]=$_POST['chkcostonegocio'];
	$rows["ARRCARGOACUENTA"]=$_POST['cmbcargoacuenta'];
	$rows["APLICAFORANEO"]=$_POST['ckbforaneo'];
	$rows["APLICANOCTURNO"]=$_POST['ckbnocturno'];
	$rows["APLICAFESTIVO"]=$_POST['ckbfestivo'];	
	$rows["ACTIVO"]=$_POST['chkstatus'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];

	//actualiza los datos
 
	$resultado= $con->update("catalogo_costo",$rows,"WHERE IDCOSTO='".$_POST['idcosto']."' ");	
		
	if($_POST['idcosto'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_costo_log SELECT * FROM $con->catalogo.catalogo_costo WHERE IDCOSTO='".$_POST['idcosto']."'");	
 		
	echo "<script>";
	if(!$resultado)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');"; 
	echo "document.location.href='general.php'";
    echo "</script>";
	
?>