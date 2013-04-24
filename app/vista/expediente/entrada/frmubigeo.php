<?php

	session_start();

	include_once("../../../modelo/clase_lang.inc.php");	
	include_once("../../../modelo/clase_mysqli.inc.php"); 
	include_once("../../../modelo/functions.php");
	include_once("../../../vista/login/Auth.class.php");	
	include_once("../../includes/head_prot_win.php");
	include_once("../../../modelo/clase_unidadfederativa.inc.php");
		
	$con= new DB_mysqli();		 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
 	Auth::required();	
	
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
	
	if($idafiliado >0){	 
		$condi="catalogo_afiliado.CVEAFILIADO='$cveafiliado' AND catalogo_afiliado.CVEAFILIADO!=''";
	} else if($cveexpediente){
		$condi="expediente.CVEAFILIADO='$cveexpediente' and expediente.CVEAFILIADO!=''";
	} else{
		$condi="expediente.IDEXPEDIENTE='$idexpediente' and expediente.IDEXPEDIENTE!=''";
	}
 
	$Sql_expedediente="SELECT
						  expediente_usuario.FECHAHORA,
						  expediente_ubigeo.IDEXPEDIENTE,
						  expediente_ubigeo.CVEENTIDAD1,
						  expediente_ubigeo.CVEENTIDAD2,
						  expediente_ubigeo.CVEENTIDAD3,
						  expediente_ubigeo.CVEENTIDAD4,
						  expediente_ubigeo.CVEENTIDAD5,
						  expediente_ubigeo.CVEENTIDAD6,
						  expediente_ubigeo.CVEENTIDAD7,
						  expediente_ubigeo.DIRECCION,
						  expediente_ubigeo.DESCRIPCION
						FROM $con->temporal.expediente_ubigeo
						  INNER JOIN $con->temporal.expediente
							ON expediente.IDEXPEDIENTE = expediente_ubigeo.IDEXPEDIENTE
						  INNER JOIN $con->catalogo.catalogo_programa
							ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
						  INNER JOIN $con->catalogo.catalogo_cuenta
							ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA							
						  LEFT JOIN  $con->catalogo.catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO		
						  INNER JOIN  $con->temporal.expediente_usuario
							ON expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE							
						WHERE $ver_cuentas $condi AND
							expediente_usuario.ARRTIPOMOVEXP='REG'
							GROUP BY expediente.IDEXPEDIENTE
							ORDER BY expediente.IDEXPEDIENTE DESC
						"; 
 
	$Sql_asistencia="SELECT
						  MIN(asistencia_usuario.FECHAHORA) as FECHAHORA,
						  asistencia_lugardelevento.ID,
						  asistencia_lugardelevento.IDASISTENCIA,
						  asistencia_lugardelevento.IDEXPEDIENTE,
						  asistencia_lugardelevento.CVEENTIDAD1,
						  asistencia_lugardelevento.CVEENTIDAD2,
						  asistencia_lugardelevento.CVEENTIDAD3,
						  asistencia_lugardelevento.CVEENTIDAD4,
						  asistencia_lugardelevento.CVEENTIDAD5,
						  asistencia_lugardelevento.CVEENTIDAD6,
						  asistencia_lugardelevento.CVEENTIDAD7,
						  asistencia_lugardelevento.DIRECCION,
						  asistencia_lugardelevento.DESCRIPCION,
						  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA ) AS servicio
						FROM $con->temporal.asistencia_lugardelevento
						  INNER JOIN $con->temporal.expediente
							ON expediente.IDEXPEDIENTE = asistencia_lugardelevento.IDEXPEDIENTE
						  INNER JOIN $con->catalogo.catalogo_programa
							ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
						  INNER JOIN $con->catalogo.catalogo_cuenta
							ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA														
						  LEFT JOIN  $con->catalogo.catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO							
						  INNER JOIN $con->temporal.asistencia
							ON asistencia.IDASISTENCIA = asistencia_lugardelevento.IDASISTENCIA
						  LEFT JOIN $con->catalogo.catalogo_programa_servicio
							ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
						  LEFT JOIN $con->catalogo.catalogo_servicio
							ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO	
						  LEFT JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA							
						WHERE $ver_cuentas $condi			
						    AND asistencia_lugardelevento.ID = asistencia.IDLUGARDELEVENTO
							GROUP BY expediente.IDEXPEDIENTE,asistencia_usuario.IDASISTENCIA
							ORDER BY asistencia_lugardelevento.IDASISTENCIA DESC
						";

	if(!$cveafiliado)	$cveafiliado=$_GET["cve_id"];
	
	$Sql_sac="SELECT
			  catalogo_afiliado_persona_domicilio_ubigeo.ID,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD1,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD2,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD3,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD4,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD5,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD6,
			  catalogo_afiliado_persona_domicilio_ubigeo.CVEENTIDAD7,
			  catalogo_afiliado_persona_domicilio_ubigeo.DIRECCION,
			  catalogo_afiliado_persona_domicilio_ubigeo.DESCRIPCION
			FROM $con->catalogo.catalogo_afiliado
			  INNER JOIN $con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo
				ON catalogo_afiliado_persona_domicilio_ubigeo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA				
			WHERE $ver_cuentas catalogo_afiliado.CVEAFILIADO = '$cveafiliado' 
				AND catalogo_afiliado.CVEAFILIADO != '0'
				GROUP BY catalogo_afiliado_persona_domicilio_ubigeo.ID "; 
 
	$result1=$con->query($Sql_expedediente);
	$result2=$con->query($Sql_asistencia);
	$result3=$con->query($Sql_sac);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_('DIRECCIONES')?></title>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		<!--
		.style3 {color: #FFFFFF; font-weight: bold; }
		-->
	</style>
</head>

<body>  
<div id="tablewrapper" > 	   
<div style="float:left;">
	<form id='form_listado'>	
		<strong><?=_('DIRECCION DE EXPEDIENTES ANTERIORES')?></strong>
		<table cellpadding="1" cellspacing="1" border="0" id="table" class="tinytable" style="width:100%">
			<thead>
				<tr> 
					<th><h3><?=_('FECHAREGISTRO')?></h3></th>
					<th><h3><?=_('EXPEDIENTE')?></h3></th>
					<th><h3><?=_('ENTIDAD 1')?></h3></th>
					<th><h3><?=_('ENTIDAD 3')?></h3></th>
					<th><h3><?=_('ENTIDAD 2')?></h3></th>
					<th><h3><?=_('DIRECCION')?></h3></th>
					<th><h3><?=_('REFERENCIAS')?></h3></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?	
					$unidad = new unidadfederativa();
					$linea=0;
					while($reg=$result1->fetch_object()){
						$entidad[1]=$reg->CVEENTIDAD1;
						$entidad[2]=$reg->CVEENTIDAD2;
						$entidad[3]=$reg->CVEENTIDAD3;
						$arr = $unidad->nombre_entidades_array($entidad);
						
						$colorlinea = ($linea%2)? 'par':'impar';
						
				?>
						<tr class='<?=$colorlinea?>' >
						<td align='center'><?=$reg->FECHAHORA?></td>
						<td align='center'><?=$reg->IDEXPEDIENTE?></td>
						<td><?=$arr[1]?></td>
						<td><?=$arr[2]?></td>
						<td><?=$arr[3]?></td>
						<td><?=$reg->DIRECCION?></td>
						<td><?=utf8_encode($reg->DESCRIPCION)?></td>
						<td><? if(!$_GET["info"]){?><a href='#' title="<?=_('ASIGNAR DIRECCION')?>" onclick="window.close();window.opener.recargar_ubigeo('<?=$reg->IDEXPEDIENTE?>','EXP','<?=$id_expediente?>')" >ASIGNAR</a><?}?></td>
						</tr>
				<?
						$linea++;
					}
				?>
			</tbody>
		</table>
	</form>
 </div> 
 </div>  
 <br> 
<div id="tablewrapper" > 	   
<div style="float:left;">
	<br>
	<strong><?=_('DIRECCION DE ASISTENCIAS BRINDADAS')?></strong>	
	<table cellpadding="1" cellspacing="1" border="0" id="table2" class="tinytable" style="width:100%">
		<thead>
			<tr> 
				<th style="background:#558DC6"><h3><?=_('FECHAREGISTRO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ASISTENCIA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('EXPEDIENTE')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ENTIDAD 1')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ENTIDAD 3')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ENTIDAD 2')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('DIRECCION')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('REFERENCIAS')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('SERVICIO')?></h3></th>
			    <th style="background:#558DC6"></th>
			</tr>
		</thead>
		<tbody>
		<?	
			$unidad = new unidadfederativa();
			$linea=0;
			while ($reg=$result2->fetch_object()){
				$entidad[1]=$reg->CVEENTIDAD1;
				$entidad[2]=$reg->CVEENTIDAD2;
				$entidad[3]=$reg->CVEENTIDAD3;
				$arr = $unidad->nombre_entidades_array($entidad);
				
				$colorlinea = ($linea%2)? 'par':'impar';
		?>
				<tr class='<?=$colorlinea?>' >
				<td align='center'><?=$reg->FECHAHORA?></td>
				<td align='center'><?=$reg->IDASISTENCIA?></td>
				<td align='center'><?=$reg->IDEXPEDIENTE?></td>
				<td><?=$arr[1]?></td>
				<td><?=$arr[2]?></td>
				<td><?=$arr[3]?></td>
				<td><?=utf8_encode($reg->DIRECCION)?></td>
				<td><?=$reg->DESCRIPCION?></td>
				<td><?=$reg->servicio?></td>
				<td><? if(!$_GET["info"]){?><a href='#' title="<?=_('ASIGNAR DIRECCION')?>" onclick="window.close();window.opener.recargar_ubigeo('<?=$reg->ID?>','ASI','<?=$id_expediente?>')">ASIGNAR</a><?}?></td>
				</tr>
		<?
				$linea++;
			}
		?>
		</tbody>
	</table>
 </div> 
 </div>
 <br>
 
<div id="tablewrapper" >
<div style="float:left;">
	<br>
	<strong><?=_('DIRECCION REGISTRADAS EN SAC')?></strong>	
	<table cellpadding="1" cellspacing="1" border="0" id="table3" class="tinytable" style="width:100%">
		<thead>
			<tr> 
				<th style="background:#336699"><h3><?=_('ENTIDAD 1')?></h3></th>
				<th style="background:#336699"><h3><?=_('ENTIDAD 3')?></h3></th>
				<th style="background:#336699"><h3><?=_('ENTIDAD 2')?></h3></th>
				<th style="background:#336699"><h3><?=_('DIRECCION')?></h3></th>
				<th style="background:#336699"><h3><?=_('REFERENCIAS')?></h3></th>
			    <th style="background:#336699"></th>
			</tr>
		</thead>
		<tbody>
		<?	
			$unidad = new unidadfederativa();
			$linea=0;
			while ($reg=$result3->fetch_object()){
				$entidad[1]=$reg->CVEENTIDAD1;
				$entidad[2]=$reg->CVEENTIDAD2;
				$entidad[3]=$reg->CVEENTIDAD3;
				$arr = $unidad->nombre_entidades_array($entidad);
				
				$colorlinea = ($linea%2)? 'par':'impar';
		?>
				<tr class='<?=$colorlinea?>' >
				<td><?=$arr[1]?></td>
				<td><?=$arr[2]?></td>
				<td><?=$arr[3]?></td>
				<td><?=utf8_encode($reg->DIRECCION)?></td>
				<td><?=$reg->DESCRIPCION?></td>
				<td><? if(!$_GET["info"]){?><a href='#' title="<?=_('ASIGNAR DIRECCION')?>" onclick="window.close();window.opener.recargar_ubigeo('<?=$reg->ID?>','SAC','<?=$id_expediente?>')">ASIGNAR</a><?}?></td>
				</tr>
		<?
				$linea++;
			}	
		
			$Sql_afiliadoubi="SELECT
					  catalogo_afiliado_persona_ubigeo.IDAFILIADO,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD1,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD2,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD3,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD4,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD5,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD6,
					  catalogo_afiliado_persona_ubigeo.CVEENTIDAD7,
					  catalogo_afiliado_persona_ubigeo.DIRECCION,
					  catalogo_afiliado_persona_ubigeo.DESCRIPCION
					FROM $con->catalogo.catalogo_afiliado_persona_ubigeo
					  INNER JOIN $con->catalogo.catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_ubigeo.IDAFILIADO
					WHERE catalogo_afiliado.CVEAFILIADO = '$cveafiliado' AND catalogo_afiliado.CVEAFILIADO !=''";
			
			$result4=$con->query($Sql_afiliadoubi);
		
			$linea=0;
			while ($reg=$result4->fetch_object()){
				$entidad[1]=$reg->CVEENTIDAD1;
				$entidad[2]=$reg->CVEENTIDAD2;
				$entidad[3]=$reg->CVEENTIDAD3;
				$arr = $unidad->nombre_entidades_array($entidad);
				
				$colorlinea = ($linea%2)? 'par':'impar';
		?>
				<tr class='<?=$colorlinea?>'>
				<td><?=$arr[1]?></td>
				<td><?=$arr[2]?></td>
				<td><?=$arr[3]?></td>
				<td><?=utf8_encode($reg->DIRECCION)?></td>
				<td><?=$reg->DESCRIPCION?></td>
				<td><a href='#' title="<?=_('ASIGNAR DIRECCION')?>" onclick="window.close();window.opener.recargar_ubigeo('<?=$reg->IDAFILIADO?>','AFI')">ASIGNAR</a></td>
				</tr>
		<?
				$linea++;
			}		
		?>
		</tbody>
	</table>
</div> 
</div>
	<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script> 
</body>
</html>