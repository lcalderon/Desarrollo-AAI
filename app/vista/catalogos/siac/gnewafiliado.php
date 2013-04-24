<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/afiliado/GestionAfiliado.class.php');
	include_once("../../../vista/login/Auth.class.php");	
	include_once('../../../modelo/functions.php');

	$con= new DB_mysqli();	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	session_start();
	Auth::required($_POST["txturlacces"]);	

// verificando si existe un afiliado con la misma cuenta, plan y clave 
	
	$afilaidoexist=$con->consultation("SELECT COUNT(*) FROM $con->catalogo.catalogo_afiliado WHERE IDPROGRAMA='".$_POST["cmbprograma"]."' AND IDCUENTA='".$_POST["cmbcuenta"]."' AND CVEAFILIADO='".$_POST["txtidentificador"]."'");

//******* gestionar afiliado *********
 
	if($afilaidoexist[0][0]==0 or $_POST['ckbvicio']){
		
		$objafiliado = new GestionAfiliado($_POST);
		
		$id_afiliado=$objafiliado->grabar_afiliado();	
		if($id_afiliado)	$objafiliado->grabar_telefonos($id_afiliado);

//data ubigeo
	
		$rowubi["IDAFILIADO"]=$id_afiliado;
		//$rowubi["CVEPAIS"]="PE";
		$rowubi["CVEENTIDAD1"]=$_POST['CVEENTIDAD1'];
		$rowubi["CVEENTIDAD2"]=$_POST["CVEENTIDAD2"];
		$rowubi["CVEENTIDAD3"]=$_POST["CVEENTIDAD3"];
		$rowubi["CVEENTIDAD4"]=$_POST["CVEENTIDAD4"];
		$rowubi["CVEENTIDAD5"]=$_POST["CVEENTIDAD5"];
		$rowubi["CVEENTIDAD6"]=$_POST["CVEENTIDAD6"];
		$rowubi["CVEENTIDAD7"]=$_POST["CVEENTIDAD7"];
		$rowubi["DIRECCION"]=$_POST['DIRECCION'];
		$rowubi["CODPOSTAL"]=$_POST['txtcodpostal'];
		
		if($id_afiliado)	$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_ubigeo",$rowubi);		
		
	}
	
	if($id_afiliado) echo 1; else echo 0;

/*   		echo "<script>";
		if(!$id_afiliado)
		 {
			if($afilaidoexist[0][0]==0)	echo "alert('"._("HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.")."');";	else echo "alert('"._("NO ES POSIBLE REGISTRAR AL AFILIADO CON LA MISMA CUENTA,PLAN Y CODIGO DE IDENTIFICACION, NO SE COMPLETO LA OPERACION.")."');";	
			echo "document.location.href='newafiliado.php'";
		 }
		else
		 {
			echo "document.location.href='buscarafiliado.php' ";
		 }
		echo "</script>";   */
		
		
?>