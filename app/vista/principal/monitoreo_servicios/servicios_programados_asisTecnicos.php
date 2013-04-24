<?php
 
	session_start(); 
 
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/functions.php');
	include_once('../../../modelo/clase_unidadfederativa.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	Auth::required();
 				
	$sql_varios="SELECT
                  /* SERVICIOS_PROGRAMADOS_ASISTECNICOS  */ 
				  catalogo_proveedor.IDPROVEEDOR,
				  catalogo_proveedor.NOMBRECOMERCIAL,
				  (SELECT
					  COUNT(*)
					FROM $con->temporal.asistencia
					  INNER JOIN $con->temporal.asistencia_asig_proveedor
						ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
					WHERE DATE(asistencia_asig_proveedor.TEAT) = CURDATE()
						AND asistencia.IDFAMILIA = 1
						AND asistencia.IDSERVICIO = '8'
						AND asistencia_asig_proveedor.STATUSPROVEEDOR IN('AC','PC')
						AND asistencia_asig_proveedor.IDPROVEEDOR=catalogo_proveedor.IDPROVEEDOR
					GROUP BY asistencia_asig_proveedor.IDPROVEEDOR
					) AS CANTIDADVIAL,
					(SELECT
					  COUNT(*)
					FROM $con->temporal.asistencia
					  INNER JOIN $con->temporal.asistencia_asig_proveedor
						ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
					WHERE DATE(asistencia_asig_proveedor.TEAT) = CURDATE()
						AND asistencia.IDFAMILIA = 3
						AND asistencia.IDSERVICIO IN(1,13,14,15,16,17)
						AND asistencia_asig_proveedor.STATUSPROVEEDOR IN('AC','PC')
						AND asistencia_asig_proveedor.IDPROVEEDOR=catalogo_proveedor.IDPROVEEDOR
					GROUP BY asistencia_asig_proveedor.IDPROVEEDOR
					) AS CANTIDADHOGAR
				FROM $con->catalogo.catalogo_proveedor
				  INNER JOIN $con->temporal.monitoreo_servicio_programado
					ON monitoreo_servicio_programado.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				WHERE catalogo_proveedor.ACTIVO = 1
					AND monitoreo_servicio_programado.TIPO = 'VIALHOGAR'
				ORDER BY catalogo_proveedor.NOMBRECOMERCIAL";

	$result=$con->query($sql_varios);	
	$numreg=$result->num_rows*1;
		
	 
	$dia1 = $nombre_semana[date(N)]; 
	$dia2 = $nombre_semana[date('N', strtotime(sumaDia(date("Y-m-d"),1)))]; 
	$dia3 = $nombre_semana[date('N', strtotime(sumaDia(date("Y-m-d"),2)))]; 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?=_('Monitoreo Tecnicos');?></title>
		<? if($_GET["rotacion"]){ ?><meta http-equiv="Refresh" content="10; url=servicios_programados_vehicular.php?rotacion=1"> <? }?>		
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
		<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />	
		
		<!-- se usa para la ventana del prototype -->
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
		<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
		<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link> 
		<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css"></link>

		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"></script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
		<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
		<style type="text/css">
		<!--
		.style1 {
			color: #FFFFFF;
			font-weight: bold;
			
		}
		.style2 {
			color: #000066;
			font-weight: bold;
			font-size: 20px;
			font-family: Verdana, Arial, Helvetica, sans-serif;
		}

		.style3 {
			font-weight: bold;
			font-size: 12px;
			font-family: Verdana, Arial, Helvetica, sans-serif;
		}

		.style4 {
			color: #FFFFFF;
			font-weight: bold;
			font-size: 19px;
			font-family: Verdana, Arial, Helvetica, sans-serif;
		}
		-->
		</style>
	</head>
<body>
	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
		<tr>
			<td width="12%"><div align="center"><img src="../../../../imagenes/logos/<?=$con->logo;?>" width="60%"  alt="<?=_('LOGO_AAPE');?>" /></div></td>
			<td width="86%"><div align="center"><h1 style="color:#000066"><?=_('MONITOR SERVICIOS PROGRAMADOS - TECNICOS');?></h1></div></td>
			<td width="2%"><? if(validar_permisos("ASIGNAR_PROV_SERVPROG")){ ?><img src="../../../../imagenes/iconos/adds.gif" style="cursor:pointer" onclick="ventana_cambios()" width="9" height="11" title="PRESENTACION" alt="PRESENTACION"><? } ?></td>
	  </tr>
	</table>

	<table border="1" width="100%"   style="border:1px solid #000066;border-collapse:collapse" cellspacing="1" cellpadding="1">
		<tr bgcolor="#000066">
			<td colspan="4" bgcolor="#000066"><div align="center"><h2 style="color:#FFFFFF"><?=_('ASISTENCIA VIAL - HOGAR');?></h2></div></td>
		</tr>
		<tr bgcolor="#000066">
			<td></td>
			<td><div align="center"><span class="style4"><?=_('TECNICO');?></span></div></td>
			<td><div align="center"><span class="style4"><?=_('NRO ASIS.VIAL');?></span></div></td>
			<td><div align="center"><span class="style4"><?=_('NRO ASIS.HOGAR');?></span></div></td>
		</tr>  
	<?	   
		if($numreg >0)
		 {	
			
			while($reg = $result->fetch_object())
			 {
				$ii++;
				 if($ii%2==0) $fondo='#FFFFFF'; else $fondo='#DDEFFF';
	?>	
		<tr bgcolor="<?=$fondo;?>">
			<td width="2%" align="center"><span class="style3"><?=$ii;?></span></td>
			<td><span class="style2"> <?=utf8_encode($reg->NOMBRECOMERCIAL);?> </span></td>	 
			<td align="center"><span class="style2"> <?=$reg->CANTIDADVIAL;?> </span></td>	 
			<td align="center"><span class="style2"> <?=$reg->CANTIDADHOGAR;?> </span></td> 
		</tr>		
		<?
			}
		 }
		?> 
	</table>  
</body>
</html>

	<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_cambios(){
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("MODIFICAR PRESENTACION - VIAL y HOGAR")?>',
					width: 640,
					height: 420,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'paginar_vehicular_vialhogar/'
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					window.location.reload();
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			return;
		}
			
	</script>