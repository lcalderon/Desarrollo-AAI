<?php
	header("Content-Type: text/html;charset=utf-8");
	include_once("../../vista/login/Auth.class.php");
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/functions.php');
	include_once('../../modelo/afiliado/GestionExpediente.class.php');
	include_once('../../modelo/clase_ubigeo.inc.php');

	$con= new DB_mysqli();
	
	$con->select_db($con->catalogo);
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();	
	Auth::required($_POST["txturlacces"]);
	
// grabando expediente

		$exist_asistencia=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDEXPEDIENTE='".$_POST["idexpediente"]."' AND IDEXPEDIENTE >0 AND IDEXPEDIENTE != '' ");	
 	 
		if($exist_asistencia[0][0] >0 and $_POST["txtclavetitular"]!=$_POST["cvetitular"]) $_POST["txtclavetitular"]=$_POST["cvetitular"];
	
		$objexpediente = new GestionExpediente($_POST);
		$idexpediente=$objexpediente->grabar_expediente();	
		
//grabando persona titular 
	
		if($idexpediente)	$titular=$objexpediente->grabar_persona("titular",$idexpediente,$_POST["titular"]);
		if($titular)		$objexpediente->grabar_telefonos($titular,"titular");	//grabando telefonos

//grabando persona contacto

		if($idexpediente and $_POST["txtpaternocontacto"]!="")	$personacontacto=$objexpediente->grabar_persona("contacto",$idexpediente,$_POST["contacto"]);	
		if($personacontacto)	$objexpediente->grabar_telefonos($personacontacto,"contacto");	//grabando telefonos
		
//grabar ubigeo
	
		$_POST['idexpediente']=$idexpediente;
		
		$rowubi["CVEPAIS"]=$con->lee_parametro("IDPAIS");
		$rowubi["CVEENTIDAD1"]=$_POST["CVEENTIDAD1"];
		$rowubi["CVEENTIDAD2"]=$_POST["CVEENTIDAD2"];
		$rowubi["CVEENTIDAD3"]=$_POST["CVEENTIDAD3"];
		$rowubi["CVEENTIDAD4"]=$_POST["CVEENTIDAD4"];
		$rowubi["CVEENTIDAD5"]=$_POST["CVEENTIDAD5"];
		$rowubi["CVEENTIDAD6"]=$_POST["CVEENTIDAD6"];
		$rowubi["CVEENTIDAD7"]=$_POST["CVEENTIDAD7"];
		$rowubi["LATITUD"]=$_POST["LATITUD"];
		$rowubi["LONGITUD"]=$_POST["LONGITUD"];
		$rowubi["DIRECCION"]=$_POST['DIRECCION']; 
 	
		//$quitar= array("\\");
		$txtnombre=trim(str_replace("\\", "",$_POST['txtareferencia']));	
		$rowubi["DESCRIPCION"]=$txtnombre;
		$rowubi["IDEXPEDIENTE"]=$_POST['idexpediente'];
			
		$ubigeo = new ubigeo();
		
		if($idexpediente and $_POST["DIRECCION"]!="")	$ubigeo->grabar_ubigeo($rowubi,"$con->temporal.expediente_ubigeo","IDEXPEDIENTE");
			
        $varexis=crypt($idexpediente,"666"); 

		echo $idexpediente."&varexis=".$varexis;
		
?>