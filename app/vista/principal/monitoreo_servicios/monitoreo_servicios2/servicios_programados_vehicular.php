<?php
 
	session_start(); 
 
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/functions.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	Auth::required();

//id proveedores gruas tabla monitoreo_servicio_programado
	$resproveedorgrua=$con->consultation("SELECT GROUP_CONCAT(monitoreo_servicio_programado.IDPROVEEDOR) FROM $con->temporal.monitoreo_servicio_programado INNER JOIN $con->catalogo.catalogo_proveedor on catalogo_proveedor.IDPROVEEDOR=monitoreo_servicio_programado.IDPROVEEDOR WHERE monitoreo_servicio_programado.TIPO='GRUA' AND catalogo_proveedor.ACTIVO=1");
	$idproveedor_grua=$resproveedorgrua[0][0];	
	
//id proveedores auxilio vial tabla monitoreo_servicio_programado
	$resproveedoravial=$con->consultation("SELECT GROUP_CONCAT(monitoreo_servicio_programado.IDPROVEEDOR) FROM $con->temporal.monitoreo_servicio_programado INNER JOIN $con->catalogo.catalogo_proveedor on catalogo_proveedor.IDPROVEEDOR=monitoreo_servicio_programado.IDPROVEEDOR  WHERE TIPO='AVIAL' AND catalogo_proveedor.ACTIVO=1");
	$idprov_auvilioVial=$resproveedoravial[0][0];
	
	$sql_gruas="(SELECT				  
				  /* ervicios_programados_vehicular */
				  (SELECT
				  COUNT( asigp.idproveedor ) 
				FROM $con->catalogo.catalogo_proveedor cp
				  LEFT JOIN $con->temporal.asistencia_asig_proveedor asigp
					ON asigp.idproveedor = cp.idproveedor
					  AND DATE(asigp.teat) = CURDATE()
					  AND asigp.statusproveedor IN ('AC','PC')
				  LEFT JOIN $con->temporal.asistencia asis
					ON asis.IDASISTENCIA = asigp.IDASISTENCIA
					  AND asis.ARRCONDICIONSERVICIO = 'COB'
				WHERE asis.IDSERVICIO=3 AND cp.IDPROVEEDOR=cp2.IDPROVEEDOR) AS cantidad,
				
				  cp2.nombrecomercial
				FROM $con->catalogo.catalogo_proveedor cp2
				  LEFT JOIN $con->temporal.asistencia_asig_proveedor asigp
					ON asigp.idproveedor = cp2.idproveedor
					  AND DATE(asigp.teat) = CURDATE()
					  AND asigp.statusproveedor IN ('AC','PC')
				  LEFT JOIN $con->temporal.asistencia asis
					ON asis.IDASISTENCIA = asigp.IDASISTENCIA
					  AND asis.ARRCONDICIONSERVICIO = 'COB'
				WHERE cp2.IDPROVEEDOR IN($idproveedor_grua)
				GROUP BY 2)
				UNION
				(SELECT
				  COUNT(*) AS cantidad,
				  'OTROS'
				FROM $con->temporal.asistencia_asig_proveedor asigp,
				  $con->temporal.asistencia asis,
				  $con->catalogo.catalogo_proveedor cp
				WHERE asigp.idproveedor = cp.idproveedor
					AND asis.idasistencia = asigp.idasistencia
					AND asis.idservicio = 3/* GRUA */ 
					AND asis.ARRCONDICIONSERVICIO='COB'
					AND asigp.IDPROVEEDOR NOT IN($idproveedor_grua)
					AND DATE(asigp.teat) = CURDATE()
					AND asigp.statusproveedor IN ('AC','PC')
				ORDER BY cp.nombrecomercial) ";

	$result=$con->query($sql_gruas);	
	$numreg=$result->num_rows*1;

	$sql_auxiliovial=" (SELECT
						  /* ervicios_programados_vehicular */  						  
						  (SELECT
						  COUNT( asigp.idproveedor ) 		 
						FROM $con->catalogo.catalogo_proveedor cp
						  LEFT JOIN $con->temporal.asistencia_asig_proveedor asigp
							ON asigp.idproveedor = cp.idproveedor
							  AND DATE(asigp.teat) = CURDATE()
							  AND asigp.statusproveedor IN ('AC','PC')
						  LEFT JOIN $con->temporal.asistencia asis
							ON asis.IDASISTENCIA = asigp.IDASISTENCIA
							  AND asis.ARRCONDICIONSERVICIO = 'COB'
						WHERE  asis.IDSERVICIO=8 AND cp.IDPROVEEDOR=cp2.IDPROVEEDOR
						 ) as cantidad,
						  cp2.NOMBRECOMERCIAL
						FROM $con->catalogo.catalogo_proveedor cp2
						  LEFT JOIN $con->temporal.asistencia_asig_proveedor asigp
							ON asigp.idproveedor = cp2.idproveedor
							  AND DATE(asigp.teat) = CURDATE()
							  AND asigp.statusproveedor IN ('AC','PC')
						  LEFT JOIN $con->temporal.asistencia asis
							ON asis.IDASISTENCIA = asigp.IDASISTENCIA
							  AND asis.ARRCONDICIONSERVICIO = 'COB'
						WHERE cp2.IDPROVEEDOR IN($idprov_auvilioVial)
						GROUP BY 2)
						UNION
						(SELECT COUNT(*) AS cantidad, 'OTROS'
							FROM $con->temporal.asistencia_asig_proveedor asigp, 
								 $con->temporal.asistencia asis,
								 $con->catalogo.catalogo_proveedor cp
							WHERE asigp.idproveedor = cp.idproveedor
							  AND asis.idasistencia = asigp.idasistencia
							  AND asis.idservicio = 8  /* AUXILIO VIAL */
							  AND asis.ARRCONDICIONSERVICIO = 'COB'
							  AND asigp.IDPROVEEDOR NOT IN ($idprov_auvilioVial)
							  AND DATE( asigp.teat ) = CURDATE()
							  AND asigp.statusproveedor IN ('AC','PC') ) ";
 	
	$rs_auxiliovial=$con->query($sql_auxiliovial);	
	$numvial=$rs_auxiliovial->num_rows*1;
		
?>
<html>
	<head>
		<title><?=_('Monitoreo Vehicular');?></title>
		<? if($_GET["rotacion"]){ ?><meta http-equiv="Refresh" content="10; url=servicios_programados_hogar.php?rotacion=1"> <? }?>
		<!-- se usa para la ventana del prototype -->
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
		<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
		<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
		<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>	
		<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">		
		<style type="text/css">
		<!--
		   td {
			font-size: 26px;
			font-family: arial, verdana;
			color: #000066;
			}
			
		   u {
			font-size: 13px;
			font-family: arial, verdana;
			color: #0E6DB4;
			}

		   h1 {
			color: #000066;
			font-family: sans-serif,Verdana,Arial, Helvetica;
			font-size: 23px;
			font-weight: bold;
			margin-bottom: 0px;
			margin-top: 0px;
			}
		-->
		 </style>
	</head>
<body topmargin="0">
	<table border="0" width="100%" bordercolor="#0c298f" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<td align="center" width="15%"><img width="60%" src='../../../../imagenes/logos/<?=$con->logo;?>'  alt="<?=_('LOGO_AAPE');?>" ></td>
			<td align="left"><h1 align="center"><?=_('MONITOR DE SERVICIOS DE GRUAS Y AUXILIO VIAL');?></h1></td>
		</tr>
	</table>
	<table width="99%" align="center">
		<tr>
			<td width="50%" align="left" valign="top">
				<table border="1" width="100%" bordercolor="#000066" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
					<tr bgcolor="#000066">
					<td width="5%" align="center"></td>
					<td width="75%"><b><font color="white"><?=_('GRUAS');?></font></b></td>
					<td width="20%" align="center"><b><font color="white"><?=_('NRO');?></font></b><img src="../../../../imagenes/iconos/adds.gif" style="cursor:pointer" onclick="ventana_cambios(1)" width="9" height="11" title="<?=_('PRESENTACION');?>" alt="<?=_('PRESENTACION');?>"></td>
				</tr>
						<?
							if($numreg >0)
							 {							
								while($reg = $result->fetch_object())
								 {
									$ii++;
									$conta++;
									if($ii%2==0) $fondo='#FFFFFF'; else $fondo='#F2F2FF';
									$total=$total+$reg->cantidad;

						?>			 
					<tr bgcolor='<?=$fondo;?>'>
						<td align="center"><?=$conta;?></td><td><?=utf8_encode($reg->nombrecomercial);?> </td>
						<td align="center" title=""><?=$reg->cantidad;?> </td>
					</tr>
						<?
								 }
							 }
						?> 
					<tr>
						<td align="center" colspan="2"><div align="right"><?=_('TOTAL');?></div></td>
						<td align="center" style="color:#FF0000"><strong><?=$total;?></strong></td>
					</tr>
				</table>
			</td>
			<td align="right" width="50%" valign="top">
				<table border="1" width="100%" bordercolor="#000066" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
					<tr bgcolor="#000066">
						<td width="5%" align="center"></td>
						<td width="75%"><b><font color="white"><?=_('AUX. VIALES');?></font></b></td>
						<td width="20%" align="center"><b><font color="white"><?=_('NRO');?></font></b><img src="../../../../imagenes/iconos/adds.gif" style="cursor:pointer" onclick="ventana_cambios(2)" width="9" height="11" title="<?=_('PRESENTACION');?>" alt="<?=_('PRESENTACION');?>"></td>
					</tr>
				<?
						if($numvial >0)
						 {							
							while($row = $rs_auxiliovial->fetch_object())
							 {
								$ii++;
								$cont++;
								if($ii%2==0) $fondo='#FFFFFF'; else $fondo='#DDEFFF';
								//DDEFFF
								$totalv=$totalv+$row->cantidad;

				?>

					<tr bgcolor='<?=$fondo;?>'>
						<td align="center"><?=$cont;?></td><td><?=utf8_encode($row->NOMBRECOMERCIAL);?> </td>
						<td align="center" title=""><?=$row->cantidad;?> </td>
					</tr>
				<?
							 }
						 }
				?>				 
					<tr>
						<td align="center" colspan="2"><div align="right"><?=_('TOTAL');?></div></td>
						<td align="center" style="color:#FF0000"><strong><?=$totalv;?></strong></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table border="1" width="70%" bordercolor="#0c298f" style="border-style:solid;border-collapse:collapse" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<td width="50%" align="center"><b><?=_('INDICE GR/AV');?></b></td>
			<td width="50%" align="center"><b><?=($totalv >0)?number_format(($total/$totalv),2):"0.0";?></b></td>
		</tr>
	</table>
</body>
</html>

	<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_cambios(opc){
		
			if(opc  ==1){			
				ventanatipo='paginar_vehicular_grua'; 
				tituloventana='<?=_("MODIFICAR PRESENTACION - GRUA")?>';
				
			} else{
				ventanatipo='paginar_vehicular_auxiliovial';
				tituloventana='<?=_("MODIFICAR PRESENTACION - AUXILIO VIAL")?>';		
			}
			
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: tituloventana,
					width: 640,
					height: 420,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: ventanatipo
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					window.location.reload();
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}}
				
				Windows.addObserver(myObserver);
			}
			return;
		}
			
	</script>