<?php
 	session_start(); 
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
	Auth::required($_POST["txturl"]);
	
	$rows["DESCRIPCION"]=strtoupper($_POST['txtnombre']);
	$rows["COSTONEGOCIADO"]=$_POST['chkcostonegocio'];
	$rows["ARRCARGOACUENTA"]=$_POST['cmbcargoacuenta'];
	$rows["APLICAFORANEO"]=$_POST['ckbforaneo'];
	$rows["APLICANOCTURNO"]=$_POST['ckbnocturno'];
	$rows["APLICAFESTIVO"]=$_POST['ckbfestivo'];	
	$rows["ACTIVO"]=$_POST['chkstatus'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];

	//Inserta los datos

	$respuesta=$con->insert_reg("$con->catalogo.catalogo_costo",$rows);		
	$idcost=$con->reg_id();

	if($respuesta and $_POST['txtnombre'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_costo_log SELECT * FROM $con->catalogo.catalogo_costo WHERE IDCOSTO='".$idcost."'");
  
	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>