<?php
 
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/clase_lang.inc.php");
	
	$con= new DB_mysqli();		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
	$Sql_exi="SELECT
			  asistencia.IDCUENTA,
			  asistencia.IDPROGRAMA,
			  CONCAT(catalogo_cuenta.NOMBRE,'/',catalogo_programa.NOMBRE)
			FROM $con->temporal.asistencia
			  INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
			  INNER JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = asistencia.IDPROGRAMA
			WHERE IDEXPEDIENTE ='".$_POST["idexpediente"]."'";

	$cueplan=$con->consultation($Sql_exi);
	
	if($cueplan[0][0]!=$_POST["idplan"] and $cueplan[0][0]!="")
		echo "1,".$cueplan[0][0].",".$cueplan[0][1].",".$cueplan[0][2];	
	else 
		echo 0;
	
?>
