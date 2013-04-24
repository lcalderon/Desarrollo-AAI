<?php
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../includes/arreglos.php");
		
	$con= new DB_mysqli("replica");
	$con->select_db($con->catalogo);
			
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
		
	session_start();
 
	$nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
	
	$entidad1=str_replace(" ", "_",_("ENTIDAD 1"));
	$entidad2=str_replace(" ", "_",_("ENTIDAD 2"));
	$entidad3=str_replace(" ", "_",_("ENTIDAD 3"));
	$entidad4=str_replace(" ", "_",_("ENTIDAD 4"));
	$entidad5=str_replace(" ", "_",_("ENTIDAD 5"));
	$entidad6=str_replace(" ", "_",_("ENTIDAD 6"));
	
	foreach($_POST['cmbcuenta'] as $idcuenta){			
		$cuentas[] ="'$idcuenta'";
		$idcuentas =implode(',',$cuentas);
	 }
 	
	if($nroubigeo ==3){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,";
	} else if($nroubigeo ==4){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5='0' ) AS $entidad4,
				";
	} else if($nroubigeo ==5){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5='0') AS $entidad4,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5=expediente_ubigeo.CVEENTIDAD5 AND CVEENTIDAD6='0' ) AS $entidad5,
				";
	}
	
	if($idcuentas and $_POST["ckbtodocuenta"] ==0) $ver_cuentas="AND catalogo_cuenta.IDCUENTA IN($idcuentas)"; //else $ver_cuentas="IDCUENTA IN('')";
 	
	if($_POST["radio"]==1)
	 {
		$fecha=$_POST["cmbanio"]."-".$_POST["cmbmes"];
		$Sql_fecha=" HAVING (MIN(asistencia_usuario.FECHAHORA) LIKE '$fecha%')  ";
	 }
	else
	 {
		$Sql_fecha=" HAVING (LEFT(MIN(asistencia_usuario.FECHAHORA),10) BETWEEN '".$_POST["fechaini"]."' AND '".$_POST["fechafin"]."')  ";	 
	 }
	 
	$Sql="SELECT
              /* REPORTE DE DEFICIENCIA*/ 
			  expediente.IDEXPEDIENTE,
			  asistencia.IDASISTENCIA,
			  catalogo_cuenta.IDCUENTA,
			  catalogo_cuenta.NOMBRE              AS CUENTA,
			  catalogo_programa.IDPROGRAMA AS IDPLAN,
			  catalogo_programa.NOMBRE            AS PLAN,
			  (SELECT LEFT(expediente_usuario.FECHAHORA,10) FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS FECHA_APERTURA_EXP,  
			  (SELECT RIGHT(expediente_usuario.FECHAHORA,8) FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS HORA_APERTURA_EXP,  
			  (SELECT LEFT(MIN(asistencia_usuario.FECHAHORA),10) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS FECHA_REGISTRO_ASIS,
			  (SELECT RIGHT(MIN(asistencia_usuario.FECHAHORA),8) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS HORA_REGISTRO_ASIS,

			  (SELECT
				 CONCAT(APPATERNO,' ',APMATERNO,' ,',NOMBRE)
			   FROM $con->temporal.expediente_persona
			   WHERE expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
				   AND ARRTIPOPERSONA = 'CONTACTO') AS NOMBRECONTACTO,  
			   (SELECT
				 CONCAT(APPATERNO,' ',APMATERNO,' ,',NOMBRE)
			   FROM $con->temporal.expediente_persona
			   WHERE expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
				  AND ARRTIPOPERSONA = 'TITULAR' GROUP BY expediente_persona.ARRTIPOPERSONA) AS NOMBRETITULAR,   
			  asistencia.ARRSTATUSASISTENCIA as STATUSASISTENCIA,			  
			  CONCAT(' ',expediente.CVEAFILIADO) as CVEAFILIADO,
			  			  asistencia.IDSERVICIO,
			  catalogo_servicio.DESCRIPCION AS SERVICIO,
			  catalogo_programa_servicio.ETIQUETA AS NOMBRECOMERCIALSERV,
			  (SELECT  DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2='0' AND CVEENTIDAD3='0') AS DEPARTAMENTO,
			  (SELECT  DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3='0')  AS PROVINCIA,
			   $Sql_ubigeo
			  catalogo_proveedor.IDPROVEEDOR,
			  
			 (SELECT catalogo_proveedor.NOMBRECOMERCIAL  FROM $con->temporal.asistencia_asig_proveedor
			 INNER JOIN $con->catalogo.catalogo_proveedor
			  ON catalogo_proveedor.IDPROVEEDOR =asistencia_asig_proveedor.IDPROVEEDOR 
			  WHERE asistencia_asig_proveedor.IDASISTENCIA=asistencia.IDASISTENCIA  
			 ORDER BY asistencia_asig_proveedor.FECHAASIGNACION DESC LIMIT 1) AS NOMBREPROVEEDOR,
				 
			  (SELECT asistencia_asig_proveedor.STATUSPROVEEDOR FROM $con->temporal.asistencia_asig_proveedor WHERE asistencia_asig_proveedor.IDASISTENCIA=asistencia.IDASISTENCIA ORDER BY asistencia_asig_proveedor.FECHAHORA DESC LIMIT 1) AS STATUSPROVEEDOR,
			  asistencia.ARRPRIORIDADATENCION AS PRIORIDAD_ATENCION,
			  asistencia.GARANTIA_REL AS EXP_GARANTIA,
			  asistencia.ARRCONDICIONSERVICIO AS CONDICIONSERVICIO,
			  if(asistencia.REEMBOLSO=1,'SI','NO') as REEMBOLSO,
			 CONCAT(catalogo_usuario.APELLIDOS,' ,',catalogo_usuario.NOMBRES) AS COORDINADOR   ,
			 asistencia.ARRAMBITO AS AMBITO,
			 asistencia.EVALAUDITORIA DESC_CALIFICACION,
			 expediente.NOMAUTORIZACION AS NOMBREAUTORIZACION, 
			 expediente.NUMAUTORIZACION AS NUMEROAUTORIZACION,   
			 expediente.OBSERVACIONES,
			 asistencia_lugardelevento.DIRECCION AS DIRECCIONASIST,
  
			  expediente_deficiencia.CVEDEFICIENCIA,
			  catalogo_deficiencia.NOMBRE AS NOMBREDEFICIENCIA,
			  expediente_deficiencia.IDETAPA AS ETAPA,
			  catalogo_deficiencia.ORIGEN AS ORIGENDEF,
			  catalogo_deficiencia.IMPORTANCIA AS IMPORTANCIADEF,
			  expediente_deficiencia.ORIGEN   as MEDIOASIGNACION,

				/* Deficiencia */
				(SELECT expediente_deficiencia_bitacora.COMENTARIO  FROM $con->temporal.expediente_deficiencia_bitacora WHERE expediente_deficiencia_bitacora.IDDEFICIENCIA_CORRELATIVO = expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO ORDER BY expediente_deficiencia_bitacora.FECHAMOD DESC LIMIT 1 ) AS OBSASIGDEF,
				(SELECT expediente_deficiencia_bitacora.IDUSUARIOMOD  FROM $con->temporal.expediente_deficiencia_bitacora WHERE expediente_deficiencia_bitacora.IDDEFICIENCIA_CORRELATIVO = expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO ORDER BY expediente_deficiencia_bitacora.FECHAMOD  DESC LIMIT 1 ) AS SUPERVISORDEF,  
				(SELECT expediente_deficiencia_bitacora.FECHAMOD FROM $con->temporal.expediente_deficiencia_bitacora WHERE expediente_deficiencia_bitacora.IDDEFICIENCIA_CORRELATIVO = expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO ORDER BY expediente_deficiencia_bitacora.FECHAMOD  DESC LIMIT 1 ) AS FECHADEF,
				IF(expediente_deficiencia.MOVIMIENTO='NUEVO','S/D',expediente_deficiencia.MOVIMIENTO) AS STATUSDEF,
 				IF(catalogo_deficiencia.ORIGEN='EXTERNO',(SELECT catalogo_proveedor.NOMBRECOMERCIAL  FROM $con->catalogo.catalogo_proveedor  WHERE catalogo_proveedor.IDPROVEEDOR=expediente_deficiencia.IDPROVEEDOR GROUP BY expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO),expediente_deficiencia.IDCOORDINADOR) AS USUARIODEF

				/* encuesta 
				 
				asistencia_encuesta_calidad.EVALENCUESTA AS CALIFENCUESTA,
				asistencia_encuesta_calidad.EVALENCUESTA AS DECSCALIFICACION, 			 
				(SELECT asistencia_usuario_calidad.IDUSUARIO FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA = asistencia.IDASISTENCIA AND asistencia_usuario_calidad.PROCESO = 'ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA DESC LIMIT 1) AS SUPERVISORENCUESTA, 
				(SELECT asistencia_usuario_calidad.FECHAHORA FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA = asistencia.IDASISTENCIA AND asistencia_usuario_calidad.PROCESO = 'ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA DESC LIMIT 1) AS FECHAENCUESTA, 
				*/ 
				/* auditar
				asistencia_auditoria_calidad.IDAUDITOR AS SUPERVAUDITORIA, 
				asistencia_auditoria_calidad.EVALUACIONAUDITOR,
				(SELECT asistencia_usuario_calidad.IDUSUARIO FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA = asistencia.IDASISTENCIA AND asistencia_usuario_calidad.PROCESO = 'AUDITO' ORDER BY asistencia_usuario_calidad.FECHAHORA DESC LIMIT 1) AS USUARIO_AUDITA,
				(SELECT asistencia_usuario_calidad.FECHAHORA FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA = asistencia.IDASISTENCIA AND asistencia_usuario_calidad.PROCESO = 'AUDITO' ORDER BY asistencia_usuario_calidad.FECHAHORA DESC LIMIT 1) AS FECHA_AUDITA
				 */
			FROM $con->temporal.expediente

			  INNER JOIN $con->temporal.expediente_deficiencia  
				ON expediente_deficiencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			  INNER JOIN $con->temporal.asistencia
				ON asistencia.IDASISTENCIA = expediente_deficiencia.IDASISTENCIA
			   LEFT JOIN $con->temporal.asistencia_usuario
				ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA				
			/*   LEFT JOIN $con->temporal.asistencia_encuesta_calidad
			   ON asistencia_encuesta_calidad.IDASISTENCIA = asistencia.IDASISTENCIA 	 */			
			  INNER JOIN $con->catalogo.catalogo_deficiencia
				ON catalogo_deficiencia.CVEDEFICIENCIA = expediente_deficiencia.CVEDEFICIENCIA
			  LEFT JOIN $con->catalogo.catalogo_deficiencia_etapa
				ON catalogo_deficiencia_etapa.CVEDEFICIENCIA = catalogo_deficiencia.CVEDEFICIENCIA
			  LEFT JOIN $con->temporal.expediente_ubigeo
				ON expediente_ubigeo.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			  LEFT JOIN $con->temporal.asistencia_lugardelevento
				ON asistencia_lugardelevento.IDASISTENCIA = asistencia.IDASISTENCIA
			/*   LEFT JOIN $con->temporal.asistencia_auditoria_calidad
				ON asistencia_auditoria_calidad.IDASISTENCIA = asistencia.IDASISTENCIA	 */			
			  LEFT JOIN $con->catalogo.catalogo_usuario
				ON catalogo_usuario.IDUSUARIO = asistencia.IDUSUARIORESPONSABLE
			  LEFT JOIN $con->temporal.asistencia_asig_proveedor
				ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
			  LEFT JOIN $con->catalogo.catalogo_proveedor
				ON catalogo_proveedor.IDPROVEEDOR = asistencia_asig_proveedor.IDPROVEEDOR
			  LEFT JOIN $con->catalogo.catalogo_servicio
				ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
			  LEFT JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
			  LEFT JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_programa_servicio
				ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO  
			  WHERE expediente.ARRSTATUSEXPEDIENTE='CER' AND asistencia_usuario.IDETAPA=1 $ver_cuentas 
				GROUP BY expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO  $Sql_fecha  					
			";

		$resultexp=$con->query($Sql);
		echo $Sql;		
		die();
		if($resultexp->num_rows*1 == 0)
		 {		 
			echo "<script>";
			echo "alert('NO EXISTE REGISTROS PARA LA CONSULTA');";
			echo "document.location.href='general.php'";
			echo "</script>";	 
		 }
		 
 	$aleatorio= rand(10000,99999);
	$fecha= date("Ymd"); 
		
 	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=\"REPORTE_DEFICIENCIA$fecha(".$_POST["txtnombre"].")_".$aleatorio.".xls\"");
	header('Pragma: no-cache');
	header('Expires: 0"');
	session_start();
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>American Assist</title>
    <style type="text/css">
	<!--
	.style3 {color: #FFFFFF; font-weight: bold; }
	-->
    </style>

</head>
<body>
	<table width="200" border="1" cellpadding="1" cellspacing="1" style="border:1px solid #333333" id="Exportar_a_Excel">  
		<tr bgcolor="#FFFFFF">
			<td bgcolor="#000000"><div align="center"><span class="style3">#</span></div></td>
			<td bgcolor="#000000"><div align="center"><span class="style3"><?=_("#EXPEDIENTE") ;?></span></div></td>
			<td bgcolor="#000000"><div align="center"><span class="style3"><?=_("#ASISTENCIA") ;?></span></div></td>
			<?	
				//creando el nombre del la cabecera
				
				foreach($_POST['chkfecha'] as $indice => $nombre){
			?> 
					<td bgcolor="#000000"><span class="style3"><?=$nombre;?></span></td>			
			<?
				}
				
			 	foreach($_POST['chkexpediente'] as $indice => $nombrefec){ 			
			?> 
					<td bgcolor="#000000"><span class="style3"><?=$nombrefec;?></span></td>			
			<?
				}
				
			 	foreach($_POST['chkasistencia'] as $indice => $nombrefec){ 			
			?> 
				<td bgcolor="#000000"><span class="style3"><?=$nombrefec;?></span></td>			
			<?
				}			
			
			 	foreach($_POST['chkdeficiencia'] as $indice => $nombrefec){ 			
			?> 
				<td bgcolor="#888888"><span class="style3"><?=$nombrefec;?></span></td>			
			<?
				}			
			
			 	foreach($_POST['chkencuesta'] as $indice => $nombrefec){ 			
			?> 
				<td bgcolor="#8f8f8f"><span class="style3"><?=$nombrefec;?></span></td>			
			<?
				}		
							
			 	foreach($_POST['chkauditoria'] as $indice => $nombrefec){ 			
			?> 
				<td bgcolor="#999999"><span class="style3"><?=$nombrefec;?></span></td>			
			<?
				}		
				
 
	//creando la data de la consulta segun la cabecera

	while($fila=$resultexp->fetch_object())
	 {
		$incre=$incre+1;
		
	?> 
		<tr bgcolor="#FFFFFF">
		    <td><?=$incre;?></td>
		    <td><?=$fila->IDEXPEDIENTE;?></td>
			<td><?=$fila->IDASISTENCIA;?></td>	
		<? 
			foreach($_POST['chkfecha'] as $indice => $fecha){			
		?> 					
			<td><?=$fila->$fecha;?></td>			
		<?
			}	
			
			foreach($_POST['chkexpediente'] as $indice => $expediente){			
		?> 					
			<td><?=$fila->$expediente;?></td>			
		<?
			}	
			
			foreach($_POST['chkasistencia'] as $indice => $asistencia){			
		?> 					
			<td><?
			
					switch($asistencia) 
					 {
						case "STATUSASISTENCIA":
							echo $desc_status_asistencia[$fila->$asistencia];
						break;	
						case "PRIORIDAD_ATENCION":
							echo $desc_prioridadAtencion[$fila->$asistencia];
						break;		
						case "CONDICIONSERVICIO":
							echo $desc_cobertura_servicio[$fila->$asistencia];
						break;		
						case "AMBITO":
							echo $desc_ambito[$fila->$asistencia];
						break;						
						default:
						echo $fila->$asistencia;
					}
			?></td>			
		<?
			}
			
			foreach($_POST['chkdeficiencia'] as $indice => $deficiencia){			
		?> 					
			<td><?=$fila->$deficiencia;?></td>			
		<?
			}	
			
			foreach($_POST['chkencuesta'] as $indice => $encuesta){			
		?> 					
			<td><?
			
					switch($encuesta) 
					 {
						case "DECSCALIFICACION":
							echo $desc_calificacion[$fila->$encuesta];
						break;			
						default:
						echo $fila->$encuesta;
					}
			?></td>				
		<?
			}
			
			foreach($_POST['chkauditoria'] as $indice => $auditoria){			
		?> 					
			<td><?=$fila->$auditoria;?></td>			
		<?
			}
		 }
		?>  			
			 
						
		 
</table> 


</body>
</html>
