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

	$sql_coordinadors="SELECT DISTINCT
                      /* servicios_programados_coordinadors */
					  catalogo_usuario.IDUSUARIO,
					  catalogo_usuario.ACTIVO,
					  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS USUARIOS,
						(SELECT
						 COUNT(DISTINCT asistencia.IDASISTENCIA)
						FROM $con->temporal.asistencia
						  INNER JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE DATE(asistencia_usuario.FECHAHORA) = CURDATE()
						    AND NOT EXISTS(SELECT asistencia_asig_proveedor.IDASISTENCIA FROM $con->temporal.asistencia_asig_proveedor 
							WHERE asistencia_asig_proveedor.IDASISTENCIA= asistencia.IDASISTENCIA)
							AND asistencia.ARRSTATUSASISTENCIA ='PRO'
							AND asistencia.IDUSUARIORESPONSABLE =catalogo_usuario.IDUSUARIO
						GROUP BY asistencia.IDUSUARIORESPONSABLE) AS CANENPROCESO1,
						
					  (SELECT
						  COUNT(*)
						FROM $con->temporal.asistencia
						  LEFT JOIN $con->temporal.asistencia_asig_proveedor
							ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE DATE(asistencia_asig_proveedor.TEAT) = CURDATE()
							AND asistencia.ARRSTATUSASISTENCIA ='PRO'
							AND asistencia.IDUSUARIORESPONSABLE =catalogo_usuario.IDUSUARIO
						GROUP BY asistencia.IDUSUARIORESPONSABLE) AS CANENPROCESO2,
						(SELECT
						 COUNT(DISTINCT asistencia.IDASISTENCIA)
						FROM $con->temporal.asistencia
						  INNER JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE DATE(asistencia_usuario.FECHAHORA) = CURDATE()
							AND asistencia.ARRSTATUSASISTENCIA ='CM'
							AND asistencia.IDUSUARIORESPONSABLE =catalogo_usuario.IDUSUARIO
						GROUP BY asistencia.IDUSUARIORESPONSABLE) AS CANCANCELADOMOM,
						(SELECT
						 COUNT(DISTINCT asistencia.IDASISTENCIA)
						FROM $con->temporal.asistencia
						  INNER JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE DATE(asistencia_usuario.FECHAHORA) = CURDATE()
							AND asistencia.ARRSTATUSASISTENCIA ='CP'
							AND asistencia.IDUSUARIORESPONSABLE =catalogo_usuario.IDUSUARIO
						GROUP BY asistencia.IDUSUARIORESPONSABLE) AS CANCANCELADOPOST,
						(SELECT
						 COUNT(DISTINCT asistencia.IDASISTENCIA)
						FROM $con->temporal.asistencia
						  INNER JOIN $con->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
						WHERE DATE(asistencia_usuario.FECHAHORA) = CURDATE()
							AND asistencia.ARRSTATUSASISTENCIA ='CON'
							AND asistencia.IDUSUARIORESPONSABLE =catalogo_usuario.IDUSUARIO
						GROUP BY asistencia.IDUSUARIORESPONSABLE) AS CANCONCLUIDO
					FROM $con->temporal.grupo_usuario
					  INNER JOIN $con->catalogo.catalogo_usuario
						ON catalogo_usuario.IDUSUARIO = grupo_usuario.IDUSUARIO
					  INNER JOIN $con->temporal.monitoreo_servicio_programado
						ON monitoreo_servicio_programado.IDCOORDINADOR = catalogo_usuario.IDUSUARIO						
					  WHERE grupo_usuario.IDGRUPO IN('CORD','SUCA') 
						AND catalogo_usuario.ACTIVO=1 			
					ORDER BY catalogo_usuario.APELLIDOS";					

	$result=$con->query($sql_coordinadors);	
	$numreg=$result->num_rows*1;
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?=_('Monitoreo Coordinador');?></title>
		<? if($_GET["rotacion"]){ ?><meta http-equiv="Refresh" content="10; url=servicios_programados_vehicular.php?rotacion=1"> <? }?>		
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
		<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />	
		
		<!-- se usa para la ventana del prototype -->
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
		<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
		<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
		<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>
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
			<td width="86%"><div align="center"><h1 style="color:#000066"><?=_('MONITOR SERVICIOS PROGRAMADOS - COORDINADORES');?></h1></div></td>
			<td width="2%"><img src="../../../../imagenes/iconos/adds.gif" style="cursor:pointer" onclick="ventana_cambios()" width="9" height="11" title="PRESENTACION" alt="PRESENTACION"></td>
	  </tr>
	</table>

	<table border="1" width="100%"   style="border:1px solid #000066;border-collapse:collapse" cellspacing="1" cellpadding="1">
		<tr bgcolor="#000066">
			<td colspan="7" bgcolor="#000066"><div align="center"><h2 style="color:#FFFFFF"><?=_('ASISTENCIA DEL DIA');?></h2></div></td>
		</tr>
		<tr bgcolor="#000066">
			<td></td>
			<td><div align="center"><span class="style4"><?=_("COORDINADOR");?></span></div></td>
			<td><div align="center"><span class="style4"><?=_("EN PROC.");?></span></div></td>
			<td><div align="center"><span class="style4"><?=_("CAN.MOMEN.");?></span></div></td>
			<td><div align="center"><span class="style4"><?=_("CAN.POSTER.");?></span></div></td>
			<td><div align="center"><span class="style4"><?=_("CONCL.");?></span></div></td>
			<td><div align="center"><span class="style4"><?=_("SUBT.");?></span></div></td>
		</tr>  
	<?	   
		if($numreg >0)
		 {	
			
			while($reg = $result->fetch_object())
			 {
				$ii++;
				 if($ii%2==0) $fondo='#FFFFFF'; else $fondo='#DDEFFF';
				$subtotal=$reg->CANENPROCESO1+$reg->CANENPROCESO2+$reg->CANCANCELADOMOM+$reg->CANCANCELADOPOST+$reg->CANCONCLUIDO;
				
				$total=$total+$subtotal;
	?>	
		<tr bgcolor="<?=$fondo;?>">
			<td width="2%" align="center"><span class="style3"><?=$ii;?></span></td>
			<td><span class="style2"> <?=utf8_encode($reg->USUARIOS);?> </span></td>	 
			<td align="center"><span class="style2"> <?=$reg->CANENPROCESO1+$reg->CANENPROCESO2;?> </span></td>	 
			<td align="center"><span class="style2"> <?=$reg->CANCANCELADOMOM;?> </span></td> 
			<td align="center"><span class="style2"> <?=$reg->CANCANCELADOPOST;?> </span></td> 
			<td align="center"><span class="style2"> <?=$reg->CANCONCLUIDO;?> </span></td> 
			<td align="center"><span class="style2"> <?=$subtotal;?> </span></td> 
		</tr>		
		<?
				$subtotal="";
			}
		 }
		?>
		<tr bgcolor="#000066">
			<td colspan="6" align="right"><span class="style4"><?=_("TOTAL");?><span class="style4"></td>
			<td><div align="center"><span class="style4"><?=$total?></span></div></td>	
		</tr> 		
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
					title: '<?=_("MODIFICAR PRESENTACION - COORDINADORES")?>',
					width: 640,
					height: 420,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'paginar_coordinadores/'
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