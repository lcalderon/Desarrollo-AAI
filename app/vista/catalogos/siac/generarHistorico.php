<?php

	$rutapais = $argv[1];
	
	include_once("/var/www/html/$rutapais/app/modelo/clase_mysqli.inc.php");
	
	$con = new DB_mysqli();
		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	$Sql_generarTablaHistorico="  
				INSERT INTO $con->catalogo.catalogo_afiliado_historial (IDCUENTA,IDPLAN,ACTIVOS,CANCELADOS,FECHACREACION)
				SELECT
					catalogo_afiliado.IDCUENTA,
					catalogo_afiliado.IDPROGRAMA,
					SUM(

						IF(
							catalogo_afiliado.STATUSASISTENCIA = 'ACT',
							1,
							0
						)
					) AS activos,
					SUM(

						IF(
							catalogo_afiliado.STATUSASISTENCIA = 'CAN',
							1,
							0
						)
					) AS cancelados,
				  CURDATE()
				FROM
					$con->catalogo.catalogo_afiliado
				INNER JOIN $con->catalogo.catalogo_cuenta ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
				INNER JOIN $con->catalogo.catalogo_programa ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
				WHERE
					catalogo_afiliado.AFILIADO_SISTEMA = 'VALIDADO'
				GROUP BY
					1,
					2
				ORDER BY 1,2";
 
	$resp=$con->query($sql_generarTablaHistorico); 
	if($resp) echo "Ok"; else echo "error.";

?>
