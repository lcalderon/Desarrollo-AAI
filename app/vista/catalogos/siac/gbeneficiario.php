<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/afiliado/GestionBeneficiario.class.php');	
	include_once('../../../../app/modelo/functions.php');

	$con= new DB_mysqli();
	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	

 	session_start();  
	
//******* gestionar beneficiario *********
 
		$objbeneficiario= new GestionBeneficiario($_POST);
		
		$id_beneficiario=$objbeneficiario->grabar_beneficiario($_POST["idafiliado"]);

		if($id_beneficiario)	$objbeneficiario->grabar_telefonos($id_beneficiario);


//data ubigeo
	
		if(!$_POST["idbeneficiario"])	$rowubi["IDBENEFICIARIO"]=$id_beneficiario;
		
		$_POST['idbeneficiario']=$id_beneficiario;
				
		$rowubi["CVEPAIS"]="PE";
		$rowubi["CVEENTIDAD1"]=$_POST['CVEENTIDAD1'];
		$rowubi["CVEENTIDAD2"]=$_POST["CVEENTIDAD2"];
		$rowubi["CVEENTIDAD3"]=$_POST["CVEENTIDAD3"];
		$rowubi["CVEENTIDAD4"]=$_POST["CVEENTIDAD4"];
		$rowubi["CVEENTIDAD5"]=$_POST["CVEENTIDAD5"];
		$rowubi["CVEENTIDAD6"]=$_POST["CVEENTIDAD6"];
		$rowubi["CVEENTIDAD7"]=$_POST["CVEENTIDAD7"];
		$rowubi["DIRECCION"]=$_POST['DIRECCION'];
		$rowubi["CODPOSTAL"]=$_POST['txtcodpostal'];
		$rowubi["IDBENEFICIARIO"]=$_POST['idbeneficiario'];
 
		$ubigeo = new ubigeo(); 
		
		if($id_beneficiario and $_POST['DIRECCION']!="")	$ubigeo->grabar_ubigeo($rowubi,"$con->catalogo.catalogo_afiliado_beneficiario_ubigeo","IDBENEFICIARIO");
 
		echo "<script>";
		if(!$id_beneficiario)
		 {
			echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
			echo "document.location.href='beneficiario.php?idafiliado=".$_POST["idafiliado"]."' ";
		 }
		else
		 {
			echo "document.location.href='beneficiario.php?idafiliado=".$_POST["idafiliado"]."' ";
		 }
		echo "</script>";
		
?>