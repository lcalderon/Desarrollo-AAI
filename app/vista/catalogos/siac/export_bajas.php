<?php

	$pais=$_REQUEST["idpais"];
	$fecha=$_REQUEST["fecha"];
	$cuenta=$_REQUEST["idcuenta"];
	$plan=$_REQUEST["idplan"];
	
	if($cuenta =="" and $plan !="")	$plan="";
	
	if($pais !="pe")	$dirpais=$pais."_soaang_produccion"; else $dirpais="soaang_pruebas";

	include_once("/var/www/".$dirpais."/app/modelo/clase_mysqli.inc.php");
	
	$nombrearchivo="baja_afiliados-".date("Y-m-d").".txt";
	
	$con = new DB_mysqli();		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
  
	$Sql="SELECT
			CONCAT(
				IDAFILIADO,
				'|',
				CVEAFILIADO,
				'|',
				AFILIADO_SISTEMA,
				'|',
				DATE_FORMAT(FECHACANCELACION, '%Y%m%d'),
				'|'
			) AS RESULTADO
		FROM
			$con->catalogo.catalogo_afiliado
		WHERE
			DATE_FORMAT(FECHACANCELACION, '%Y%m%d')= '$fecha'
		AND IDCUENTA LIKE '$cuenta%'
		AND IDPROGRAMA LIKE '$plan%'";

	$result=$con->query($Sql); 

	if($result){
	 
		while($row = $result->fetch_object()){
			
			$archivo = fopen ("file_generado/".$nombrearchivo."txt", "a+");
			fwrite($archivo, $row->RESULTADO."\r\n");
			fclose($archivo);
			
		}
	} else{
	
		echo "ERRO DE PROCESO";
		
	}
?>
