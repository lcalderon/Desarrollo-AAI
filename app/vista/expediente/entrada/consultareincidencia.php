<?php

 	session_start();
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

	if($_GET["id_expediente"]){
		$dato="expediente.IDEXPEDIENTE ='".$_GET["id_expediente"]."'";	 
	} else if($_GET["cve_id"]){
		$dato="catalogo_afiliado.CVEAFILIADO ='".$_GET["cve_id"]."'";	 
	}	
	
	$Sql_verfica="SELECT
				  catalogo_afiliado.IDAFILIADO,
				  catalogo_afiliado.CVEAFILIADO,
				  expediente.CVEAFILIADO,
				  expediente.IDEXPEDIENTE
				FROM $con->temporal.expediente
				  LEFT JOIN  $con->catalogo.catalogo_afiliado
					ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO
				WHERE $dato ";
 
	$afiliado=$con->consultation($Sql_verfica);	
	 
 	$idafiliado=$afiliado[0][0];
 	$cveafiliado=$afiliado[0][1];
 	$cveexpediente=$afiliado[0][2];
 	$idexpediente=$afiliado[0][3];
	 
	if($cveexpediente !=""){
			$condi="expediente.CVEAFILIADO='$cveexpediente' and expediente.CVEAFILIADO!=''";
	} else{
			$condi="expediente.IDEXPEDIENTE ='$idexpediente' ";
	}
	
	if($_GET["cuenta"])	$datocuenta=$_GET["cuenta"];
	if($_GET["plan"])	$datoplan=$_GET["plan"];
	
//cuenta plan actual
	$Sql_his_mismaCuenta_plan="SELECT
		  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS ETIQUETA,
		  expediente.IDEXPEDIENTE,
		  asistencia_usuario.IDASISTENCIA,
		  asistencia.ARRCONDICIONSERVICIO,
		  asistencia.ARRPRIORIDADATENCION,
		  asistencia_usuario.IDUSUARIO,
		  MIN(asistencia_usuario.FECHAHORA) as FECHAHORA,
		  catalogo_programa.NOMBRE          as programa,
		  catalogo_cuenta.NOMBRE            as cuenta,
		  catalogo_servicio.DESCRIPCION,
		  expediente.IDAFILIADO,
		  asistencia.ARRSTATUSASISTENCIA
		FROM $con->temporal.expediente
		  INNER JOIN $con->catalogo.catalogo_programa
			ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
		  INNER JOIN $con->catalogo.catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
		  INNER JOIN $con->temporal.asistencia
			ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
		  LEFT JOIN $con->catalogo.catalogo_programa_servicio
			ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO			
		  LEFT JOIN $con->catalogo.catalogo_servicio
			ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
		  LEFT JOIN $con->temporal.asistencia_usuario
			ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
		  LEFT JOIN $con->catalogo.catalogo_afiliado
			ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO   			
		WHERE asistencia.ARRSTATUSASISTENCIA IN('CON','PRO') AND expediente.IDCUENTA ='$datocuenta' AND expediente.IDPROGRAMA ='$datoplan' AND  $condi
		GROUP BY asistencia_usuario.IDASISTENCIA
		ORDER BY asistencia_usuario.IDASISTENCIA  DESC";
 
	$result=$con->query($Sql_his_mismaCuenta_plan);	
	$numregCuentaActPlan=$result->num_rows;

//cuentan plan otros	
	$Sql_his_mismaCuentaOtroPlan="SELECT
		  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS ETIQUETA,
		  expediente.IDEXPEDIENTE,
		  asistencia_usuario.IDASISTENCIA,
		  asistencia.ARRCONDICIONSERVICIO,
		  asistencia.ARRPRIORIDADATENCION,
		  asistencia_usuario.IDUSUARIO,
		  MIN(asistencia_usuario.FECHAHORA) as FECHAHORA,
		  catalogo_programa.NOMBRE          as programa,
		  catalogo_cuenta.NOMBRE            as cuenta,
		  catalogo_servicio.DESCRIPCION,
		  asistencia.ARRSTATUSASISTENCIA
		FROM $con->temporal.expediente
		  INNER JOIN $con->catalogo.catalogo_programa
			ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
		  INNER JOIN $con->catalogo.catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
		  INNER JOIN $con->temporal.asistencia
			ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
		  LEFT JOIN $con->catalogo.catalogo_programa_servicio
			ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO			
		  LEFT JOIN $con->catalogo.catalogo_servicio
			ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
		  LEFT JOIN $con->temporal.asistencia_usuario
			ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
		  LEFT JOIN $con->catalogo.catalogo_afiliado
			ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO   			
		WHERE expediente.IDPROGRAMA ='$datoplan' AND asistencia.ARRSTATUSASISTENCIA IN('CP','CM') AND expediente.IDCUENTA ='$datocuenta' AND  $condi
		GROUP BY asistencia_usuario.IDASISTENCIA
		ORDER BY asistencia_usuario.IDASISTENCIA  DESC";
 
	$resultCueOtroPlan=$con->query($Sql_his_mismaCuentaOtroPlan);	
	$numregCuentaActOtroPlan=$resultCueOtroPlan->num_rows*1;
	
//todas las cuentas menos la principal

	$Sql_his_otrasCuenta="SELECT
		  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS ETIQUETA,
		  expediente.IDEXPEDIENTE,
		  asistencia_usuario.IDASISTENCIA,
		  asistencia.ARRCONDICIONSERVICIO,
		  asistencia.ARRPRIORIDADATENCION,
		  asistencia_usuario.IDUSUARIO,
		  MIN(asistencia_usuario.FECHAHORA) as FECHAHORA,
		  catalogo_programa.NOMBRE          as programa,
		  catalogo_cuenta.NOMBRE            as cuenta,
		  catalogo_servicio.DESCRIPCION,
		  asistencia.ARRSTATUSASISTENCIA
		FROM $con->temporal.expediente
		  INNER JOIN $con->catalogo.catalogo_programa
			ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
		  INNER JOIN $con->catalogo.catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
		  INNER JOIN $con->temporal.asistencia
			ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
		  LEFT JOIN $con->catalogo.catalogo_programa_servicio
			ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO			
		  LEFT JOIN $con->catalogo.catalogo_servicio
			ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
		  LEFT JOIN $con->temporal.asistencia_usuario
			ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
		  LEFT JOIN $con->catalogo.catalogo_afiliado
			ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO   			
		WHERE $ver_cuentas  expediente.IDPROGRAMA !='$datoplan' AND  $condi
		GROUP BY asistencia_usuario.IDASISTENCIA
		ORDER BY asistencia_usuario.IDASISTENCIA  DESC";
 
	$resultOtros=$con->query($Sql_his_otrasCuenta);	
	$numregCuentaOtros=$resultOtros->num_rows*1;	
	
	$totalReinc=$numregCuentaOtros+$numregCuentaActPlan+$numregCuentaActOtroPlan;
	$numreg=($numregCuentaActPlan >0)?"(".$numregCuentaActPlan.")":"";
?>