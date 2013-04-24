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
	
//variables POST	 

	$rows["IDCUENTA"]=strtoupper($_POST['txtcodigo']);
	$rows["NOMBRE"]=strtoupper($_POST['txtnombre']);
	$rows["AFILIADOS"]=$_POST['chkafiliado'];
	$rows["PILOTO"]=$_POST['txtpiloto'];
	$rows["ACTIVO"]=$_POST['chkstatus'];
	$rows["CUENTAVIP"]=$_POST['chkvip'];
	$rows["VALIDACIONEXTERNA"]=$_POST['ckbvalidacion'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	
//Inserta los datos

	$respuesta=$con->insert_reg("catalogo_cuenta",$rows);

	if($respuesta and $_POST['txtcodigo'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_cuenta_log SELECT * FROM $con->catalogo.catalogo_cuenta WHERE IDCUENTA='".strtoupper($_POST['txtcodigo'])."'");	
	
	echo "<script>";
	if(!$respuesta)	
	 {
		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
		echo "document.location.href='general.php' ";
	 }
	else
	 {
		echo "document.location.href='edit_catalogo.php?codigo=".strtoupper($_POST['txtcodigo'])."' ";
	 }	 
    echo "</script>"; 

?>