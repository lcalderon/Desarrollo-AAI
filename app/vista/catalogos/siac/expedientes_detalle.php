<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
		
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 	Auth::required();
	
	$_id_pagina=601;
	 
	$var_color_ventas="#EBF6FF";
	$var_color_marcaje="#EBF6FF";

	
	$Sql="SELECT
		  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS usuario,
		  catalogo_afiliado_persona_ubigeo.DESCRIPCION,
		  catalogo_afiliado_persona.IDAFILIADO,
		  expediente.IDEXPEDIENTE,
		  expediente.FECHAREGISTRO,
		  expediente.OBSERVACIONES,
		  expediente.OCURRIO,
		  expediente.DESCRIPCIONFISICA,
		  CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,', ',catalogo_afiliado_persona.NOMBRE) AS nombres
		FROM $con->temporal.expediente
		  INNER JOIN catalogo_afiliado
			ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO
		  INNER JOIN catalogo_afiliado_persona
			ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
		  LEFT JOIN catalogo_afiliado_persona_ubigeo
			ON catalogo_afiliado_persona_ubigeo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
		  INNER JOIN $con->temporal.expediente_usuario
			ON expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE
		  INNER JOIN catalogo_usuario
			ON catalogo_usuario.IDUSUARIO = expediente_usuario.IDUSUARIO
		WHERE expediente.IDEXPEDIENTE=".$_GET["idexpediente"]." AND expediente_usuario.ARRTIPOMOVEXP = 'REG' ";
 ;
	$result=$con->query($Sql);
	$reg = $result->fetch_object();
		
	$Sql_telefono="SELECT
				  catalogo_afiliado_persona_telefono.NUMEROTELEFONO,
				  catalogo_afiliado_persona_telefono.CODIGOAREA
				FROM catalogo_afiliado_persona_telefono
				  INNER JOIN catalogo_afiliado
					ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
				WHERE catalogo_afiliado_persona_telefono.IDAFILIADO ='".$reg->IDAFILIADO."' ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
				LIMIT 4";
				
	$resultel=$con->query($Sql_telefono);
	while($row = $resultel->fetch_object())
	 {
		$ii=$ii+1;
		$telefono[$ii]=$row->NUMEROTELEFONO;
		$codigoa[$ii]=$row->CODIGOAREA;		
		
	 }
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link href='librerias/rpt_estilos.css' rel='stylesheet' type='text/css'>	
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<style type="text/css">
	<!--
	.style1 {
		color: #003366;
		font-weight: bold;
	}
	-->
	</style>
	<script type="text/javascript">

		function llamada(codigoarea,numero){
			numero = codigoarea+numero;

			new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
			{	method : 'get',
				parameters: {
					prefijo: "",
					num: numero,
					ext: '<?=$_SESSION["extension"];?>'
					} 
			}
			);
		}

	</script>	
</head>
<body leftmargin="2"  onLoad="Maximixar()">
<?php
//Recepcion de Datos
	$var_nro_expediente=$_GET["var_nro_expediente"];
	$var_expediente_valida=$_GET["var_valida"]; 
?>
	<h2 class="Box"><?=_("DETALLE DEL EXPEDIENTE") ;?></h2><br/>
    <table border="1" width="100%" style="border-collapse:collapse" cellspacing="1" cellpadding="1" align="center">
		<tr>
			<td colspan="4" bgcolor="#6f6f6f" align="center" style="color:#FFFFFF"><b><?=_("DATOS DEL EXPEDIENTE") ;?></b></td>
		</tr>	
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("NUMERO DE EXPEDIENTE") ;?></b></td>
			<td><b><?=$reg->IDEXPEDIENTE;?></b></td>
			<td bgcolor="#a7a7a7"><b><?=_("FECHA DE REGISTRO") ;?></b></td>
			<td align="left"><?=$reg->FECHAREGISTRO;?></td>
		</tr>	
		<tr>
			<td width="18%" bgcolor="#a7a7a7"><b><?=_("NOMBRE DEL AFILIADO") ;?></b></td>
			<td align="left" width="32%"><?=$reg->nombres;?></td>
			<td width="16%" bgcolor="#a7a7a7"><b><?=_("NOMBRE DEL CONTACTO") ;?></b></td>
			<td align="left" width="34%"><?=$reg->IDCUENTA;?></td>
		</tr>	
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("TELEFONO1") ;?></b></td>
			<td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="8<?=$telefono[1];?>" /><?
					$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
					//$con->cmbselectdata($sql,"cmbtelefono0",$tipotelefono[1]," onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
				?><? if($telefono[1]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[1];?>','<?=$telefono[1];?>')" title="Llamar" /><? }?></td>
			<td bgcolor="#a7a7a7"><span class="style5"><b><?=_("TELEFONO2") ;?></b></span></td>
			<td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[2];?>"/><?
					$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
					//$con->cmbselectdata($sql,"cmbtelefono1",$tipotelefono[2]," onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
			?><? if($telefono[2]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[2];?>','<?=$telefono[2];?>')" title="Llamar" /><? }?></td>
		</tr>
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("TELEFONO3") ;?></b></td>
			<td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[3];?>" /><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				//$con->cmbselectdata($sql,"cmbtelefono2",$tipotelefono[3]," onchange=\"verificardiv('+','telefono3',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
			?><? if($telefono[3]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[3];?>','<?=$telefono[3];?>')" title="Llamar" /><? }?></td>
			<td bgcolor="#a7a7a7"><b><?=_("TELEFONO4") ;?></b></td>
			<td><input name="txttelefono[]" type="text" readonly class="classtexto" id="txttelefono[]" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[4];?>"/><?
					$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
					//$con->cmbselectdata($sql,"cmbtelefono3",$tipotelefono[4]," onchange=\"verificardiv('+','telefono4',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","1");
			?><? if($telefono[4]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[4];?>','<?=$telefono[4];?>')" title="Llamar" /><? }?></td>
		</tr>	  
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("UBICACION") ;?></b></td>
			<td align="left" colspan="3"><?=$reg->DESCRIPCION;?></td>
		</tr>	
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("OCURRIO") ;?></b></td>
			<td align="left" colspan="3"><?=$reg->OCURRIO;?></td>
		</tr>	
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("DESCRIPCION FISICA") ;?></b></td>
			<td align="left" colspan="3"><?=$reg->DESCRIPCIONFISICA;?></td>
		</tr>	
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("OBSERVACIONES") ;?></b></td>
			<td align="left" colspan="3"><?=$reg->OBSERVACIONES;?></td>
		</tr>
		<tr>
			<td bgcolor="#a7a7a7"><b><?=_("COORDINADOR") ;?></b></td>
			<td align="left" colspan="3"><?=$reg->usuario;?></td>
		</tr>
	</table>
    <? 
		$Sql_asistencias="SELECT
						  asistencia.ARRSTATUSASISTENCIA,
						  asistencia.ARRPRIORIDADATENCION,
						  asistencia.IDASISTENCIA,
						  catalogo_servicio.DESCRIPCION,
						  (SELECT
							 catalogo_proveedor.NOMBRECOMERCIAL
						   FROM $con->temporal.asistencia_asig_proveedor
							 INNER JOIN $con->catalogo.catalogo_proveedor
							   ON catalogo_proveedor.IDPROVEEDOR = asistencia_asig_proveedor.IDPROVEEDOR
						   WHERE asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
						   GROUP BY asistencia_asig_proveedor.IDASISTENCIA) AS proveedor						  
						FROM $con->temporal.asistencia
						  INNER JOIN catalogo_servicio
							ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
						WHERE asistencia.IDEXPEDIENTE='".$_GET["idexpediente"]."' ";
 
		$rs_asistencia=$con->query($Sql_asistencias);		
		 
		while($row = $rs_asistencia->fetch_object())
		 {
			$cont=$cont+1;	 
			
			$Sql_plantilla="SELECT
							  REPLACE(catalogo_plantilla.VISTA,'.php','')
							FROM $con->temporal.asistencia
							  INNER JOIN $con->catalogo.catalogo_servicio
								ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
							  INNER JOIN $con->catalogo.catalogo_plantilla
								ON catalogo_plantilla.IDPLANTILLA = catalogo_servicio.IDPLANTILLA
							WHERE asistencia.IDASISTENCIA = '".$row->IDASISTENCIA."'";
							
			$retornplan=$con->consultation($Sql_plantilla);
			$nombreplan=$retornplan[0][0];
			
			$ocurrido=$con->consultation("SELECT DESCRIPCIONSERVICIO,SOLUCIONFALLA FROM $con->temporal.asistencia_pc_$nombreplan WHERE IDASISTENCIA='".$row->IDASISTENCIA."' ");
				
			$Sql_asignacion="SELECT
							  MIN(asistencia_usuario.FECHAHORA)
							FROM $con->temporal.asistencia_usuario
							  INNER JOIN $con->temporal.asistencia_asig_proveedor
								ON asistencia_asig_proveedor.IDASISTENCIA = asistencia_usuario.IDASISTENCIA
							WHERE asistencia_usuario.IDETAPA = 2  AND asistencia_usuario.IDASISTENCIA = '".$row->IDASISTENCIA."'";
			
			$Sql_arribo="SELECT
						  COALESCE(arr.FECHAARRIBO, '0000-00-00 00:00:00' )
						FROM $con->temporal.asistencia_asig_proveedor arr
						WHERE arr.idasistencia = '".$row->IDASISTENCIA."' ";
							
			$Sql_contacto="SELECT
							  COALESCE(arr2.FECHACONTACTO, '0000-00-00 00:00:00' )
							FROM $con->temporal.asistencia_asig_proveedor arr2
							WHERE arr2.idasistencia = '".$row->IDASISTENCIA."' ";
			
			$Sql_termino="SELECT
							  FECHAHORA
							FROM $con->temporal.asistencia_asig_proveedor
							WHERE statusproveedor = 'PC'
								AND idasistencia = '".$row->IDASISTENCIA."' ";	

			$sql_conformidad="SELECT
							  COMENTARIO
							FROM $con->temporal.asistencia_bitacora_etapa8
							WHERE IDASISTENCIA = '".$row->IDASISTENCIA."'
								AND COMENTARIO LIKE '%CONFO%'
							LIMIT 1 ";
										
			
			$fechaasigna=$con->consultation($Sql_asignacion);
			$fechaarribo=$con->consultation($Sql_arribo);
			$fechacontacto=$con->consultation($Sql_contacto);
			$fechatermino=$con->consultation($Sql_termino);
			$conformidad=$con->consultation($sql_conformidad);
			 
	?>
		<br>
		
		<table border="1" width="100%" bordercolor="#0c298f" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
			<tr bgcolor="#92add3">
				<td colspan="4" align="center"><span class="style1"><?=_("ASISTENCIA BRINDADA #$cont") ;?></span></td>
			</tr>		
			<tr>
				<td align="center" bgcolor="#cfdbec" colspan="2"><b class="style1"><?=_("SERVICIO") ;?></b></td>
				<td align="center" bgcolor="#cfdbec" colspan="2"><b class="style1"><?=_("TIPO DE SERVICIO") ;?></b></td>
			</tr>		
			<tr>
				<td align="center" colspan="2"><?=$row->DESCRIPCION;?></td>
				<td align="center" colspan="2"><?=$desc_prioridadAtencion[$row->ARRPRIORIDADATENCION];?></td>
			</tr>
			<tr>
				<td width="25%" align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Nro Asistencia") ;?></b></td>
				<td width="23%" align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Proveedor") ;?></b></td>
				<td width="32%" align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Fecha Asignacion") ;?></b></td>
				<td width="20%" align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Fecha Arribo") ;?></b></td>
			</tr>		
			<tr>
				<td align="center"><?=$row->IDASISTENCIA;?></td>
				<td><?=$row->proveedor;?></td>
				<td align="center"><?=$fechaasigna[0][0];?>&nbsp;</td>
				<td align="center"><?=$fechaarribo[0][0];?></td>
			</tr>		
			<tr>
				<td align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Fecha Contacto") ;?></b></td>
				<td align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Fecha Termino") ;?></b></td>
				<td align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Estado Asistencia") ;?></b></td>
				<td align="center" bgcolor="#cfdbec" class="style1"><b><?=_("Conformidad") ;?></b></td>
			</tr>		
			<tr>
				<td align="center"><?=$fechacontacto[0][0];?>&nbsp;</td>
				<td align="center"><?=$fechatermino[0][0];?></td>
				<td align="center"><?=$desc_status_asistencia[$row->ARRSTATUSASISTENCIA];?></td>
				<td><?=($conformidad[0][0])?$conformidad[0][0]:_("NO ENCONTRADO");?></td>
			</tr>
			<tr>
				<td bgcolor="#cfdbec" class="style1"><div align="center"><b><?=_("OCURRIO") ;?></b></div></td>
				<td colspan="3" bgcolor="#cfdbec" class="style1">&nbsp;</td>
		    </tr>
			<tr>
				<td colspan="4"><?=$ocurrido[0][0];?>&nbsp;</td>
			</tr>	
			<tr>
				<td bgcolor="#cfdbec" class="style1"><div align="center"><b><?=_("SOLUCION DE LA FALLA") ;?></b></div></td>
				<td colspan="3" bgcolor="#cfdbec" class="style1">&nbsp;</td>
		    </tr>
			<tr>
				<td colspan="4"><?=utf8_encode($ocurrido[0][1]);?>&nbsp;</td>
			</tr>		
		</table>
	<? 
		$nombrefiscal="";
		unset($fecha); 
	 }
	?>

</body>
</html>