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

//cantidad de ubigeo
    $nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
 	
	Auth::required();

	$sql_hogar="SELECT
				  catalogo_proveedor.IDPROVEEDOR,
				  catalogo_proveedor.NOMBRECOMERCIAL,
				  monitoreo_servicio_programado_xusuario.IDSERVICIOS
				FROM $con->catalogo.catalogo_proveedor
				  INNER JOIN $con->temporal.monitoreo_servicio_programado_xusuario
					ON monitoreo_servicio_programado_xusuario.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				WHERE catalogo_proveedor.ACTIVO = 1
					AND monitoreo_servicio_programado_xusuario.TIPO = 'HOGAR' AND monitoreo_servicio_programado_xusuario.IDCOORDINADOR='".$_SESSION["user"]."'
				ORDER BY catalogo_proveedor.NOMBRECOMERCIAL";
 
	$result=$con->query($sql_hogar);	
	$numreg=$result->num_rows;

	$rs_ids=$con->query("SELECT DISTINCT IDSERVICIOS AS IDS,IDSERVICIOS FROM $con->temporal.monitoreo_servicio_programado_xusuario WHERE monitoreo_servicio_programado_xusuario.TIPO='HOGAR' AND IDCOORDINADOR='".$_SESSION["user"]."'");
	$row_ids = $rs_ids->fetch_object();
	
	if($row_ids->IDS){
		$rs_nombre=$con->query("SELECT REPLACE(REPLACE(GROUP_CONCAT(catalogo_servicio.DESCRIPCION),'HOGAR - ',''),',','/') AS  NOMSERVICIOS FROM $con->catalogo.catalogo_servicio WHERE catalogo_servicio.IDSERVICIO IN($row_ids->IDS)");
		$row_nomserv = $rs_nombre->fetch_object(); 		
	}
	
	$dia1 = $nombre_semana[date(N)]; 
	$dia2 = $nombre_semana[date('N', strtotime(sumaDia(date("Y-m-d"),1)))]; 
	$dia3 = $nombre_semana[date('N', strtotime(sumaDia(date("Y-m-d"),2)))]; 
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?=_('Monitoreo Hogar:Gasfiteria-Electricidad');?></title>
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
			<td width="86%"><div align="center"><h1 style="color:#000066"><?=_("MONITOR SERVICIOS PROGRAMADOS HOGAR:")."<u><font color='#3f771a'>".$row_nomserv->NOMSERVICIOS."</font></u>";?></h1></div></td>
			<td width="2%"><? if(validar_permisos("ASIGNAR_PROV_SERVPROG")){ ?><img src="../../../../imagenes/iconos/adds.gif" style="cursor:pointer" onclick="ventana_cambios()" width="9" height="11" title="PRESENTACION" alt="PRESENTACION"><? } ?></td>
	  </tr>
	</table>

	<table border="1" width="100%"   style="border:1px solid #000066;border-collapse:collapse" cellspacing="1" cellpadding="1">
		<tr bgcolor="#000066">
			<td><div align="center"><span class="style4"><?=_('TECNICO');?></span></div></td>
			<td><div align="center"><span class="style4"><?=$dia1." ".date(j);?></span></div></td>
			<td><div align="center"><span class="style4"><?=$dia2." ".(substr(sumaDia(date("Y-m-d"),1),8,2));?></span></div></td>
			<td><div align="center"><span class="style4"><?=$dia3." ".(substr(sumaDia(date("Y-m-d"),2),8,2));?></span></div></td> 
		</tr>  
	<?	   
		if($numreg >0)
		 {	
			if(!$row_ids->IDSERVICIOS)	$idservicios=0; else $idservicios=$row_ids->IDSERVICIOS;
			while($reg = $result->fetch_object())
			 {
				$ii++;
				 if($ii%2==0) $fondo='#FFFFFF'; else $fondo='#F2F2FF';
	?>	
		<tr bgcolor="<?=$fondo;?>">
			<td width="25%"><span class="style2"> <?=utf8_encode($reg->NOMBRECOMERCIAL);?> </span></td>	
 
	<?
			 for ( $i = 1 ; $i <= 3 ; $i ++){
				
				$dia=$i-1;
				$info[$i]="";
				
				$sql_hogar_info="SELECT
                              /* servicios_programados_hogar */
							  cp2.nombrefiscal,
							  asis.idasistencia,
							  asis.IDSERVICIO,
							  asigp.teat,
							  asigp.team,
							  SUBSTR(asigp.teat,12,5) as tiempo1,
							  SUBSTR(asigp.team,12,5) as tiempo2,					
							  @entidad1:=IF( asisl.cveentidad1 != 0, ent1.descripcion, '' ),
							  @entidad2:=IF( asisl.cveentidad2 != 0, ent2.descripcion, '' ),
							  @entidad3:=IF( asisl.cveentidad3 != 0, ent3.descripcion, '' ),
							  @entidad4:=IF( asisl.cveentidad4 != 0, ent4.descripcion, '' ),
							 /* $var_entidad AS ENTIDADES,*/
							  asisl.descripcion,
							  asis.idusuarioresponsable,
							  CONCAT(IF( asisl.cveentidad1 != 0, ent1.descripcion, '' ),'/', 
							  IF( asisl.cveentidad2 != 0, ent2.descripcion, '' ),'/', 
							  IF( asisl.cveentidad3 != 0, ent3.descripcion, '')) AS direccion2,
							  IF( asisl.cveentidad3 != 0, ent3.descripcion, '' ) as distrito,
							  IF( asisl.cveentidad3 != 0, ent3.descripcion, '') AS comuna,
							  IF( asisl.cveentidad2 != 0, ent2.descripcion, '') AS entidad02
							FROM ($con->temporal.asistencia_asig_proveedor asigp,
							   $con->temporal.asistencia asis,
							   $con->catalogo.catalogo_proveedor cp2)
							  LEFT JOIN $con->temporal.asistencia_lugardelevento asisl
								ON asis.idlugardelevento = asisl.id
							  LEFT JOIN $con->catalogo.catalogo_entidad ent1
								ON ent1.cveentidad1 = asisl.cveentidad1
								  AND ent1.cveentidad2 = 0
								  AND ent1.cveentidad3 = 0
								  AND ent1.cveentidad4 = 0
								  AND ent1.cveentidad5 = 0
								  AND ent1.cveentidad6 = 0
								  AND ent1.cveentidad7 = 0
							  LEFT JOIN $con->catalogo.catalogo_entidad ent2
								ON ent2.cveentidad1 = asisl.cveentidad1
								  AND ent2.cveentidad2 = asisl.cveentidad2
								  AND ent2.cveentidad3 = 0
								  AND ent2.cveentidad4 = 0
								  AND ent2.cveentidad5 = 0
								  AND ent2.cveentidad6 = 0
								  AND ent2.cveentidad7 = 0
							  LEFT JOIN $con->catalogo.catalogo_entidad ent3
								ON ent3.cveentidad1 = asisl.cveentidad1
								  AND ent3.cveentidad2 = asisl.cveentidad2
								  AND ent3.cveentidad3 = asisl.cveentidad3
								  AND ent3.cveentidad4 = 0
								  AND ent3.cveentidad5 = 0
								  AND ent3.cveentidad6 = 0
								  AND ent3.cveentidad7 = 0
							  LEFT JOIN $con->catalogo.catalogo_entidad ent4
								ON ent4.cveentidad1 = asisl.cveentidad1
								  AND ent4.cveentidad2 = asisl.cveentidad2
								  AND ent4.cveentidad3 = asisl.cveentidad3
								  AND ent4.cveentidad4 = asisl.cveentidad4
								  AND ent4.cveentidad5 = 0
								  AND ent4.cveentidad6 = 0
								  AND ent4.cveentidad7 = 0
							WHERE asigp.idasistencia = asis.idasistencia
								AND asis.IDSERVICIO IN($idservicios)						
								AND asigp.statusproveedor = 'AC'								
								AND asis.ARRSTATUSASISTENCIA = 'PRO'/*PROCESO*/
								/*AND asis.ARRPRIORIDADATENCION = 'PRO' PROGRAMADO*/
								AND DATE(asigp.teat)  = DATE_ADD(CURDATE(), INTERVAL $dia DAY)
								AND cp2.idproveedor = asigp.idproveedor
								AND asigp.idproveedor IN(SELECT DISTINCT
														   cp.idproveedor
														 FROM $con->catalogo.catalogo_proveedor cp,
														   $con->catalogo.catalogo_servicio cs,
														   $con->catalogo.catalogo_proveedor_servicio cps
														 WHERE cp.idproveedor = cps.idproveedor
															 AND cps.idservicio = cs.idservicio
															 AND cs.idfamilia = 3
														 /* HOGAR   AND cp.interno = 1 */ 
															 AND cp.activo = 1
															 AND cp.nombrefiscal NOT LIKE '%REEMBOLSO%'
															 AND cp.idproveedor =$reg->IDPROVEEDOR
														 ORDER BY 1)";

						$rs_info=$con->query($sql_hogar_info);
				 
						$numinfo=$rs_info->num_rows*1;
				 		while($row = $rs_info->fetch_object()){		

							if($nroubigeo ==3){
								$distriocomuna=$row->distrito;
							} else if($nroubigeo ==2){
								$distriocomuna=$row->entidad02;
							} else{
								$distriocomuna=$row->comuna;
							}

							if($row->IDSERVICIO){
								$rs_servici=$con->query("SELECT REPLACE(GROUP_CONCAT(catalogo_servicio.DESCRIPCION),'HOGAR - ','') AS  NOMSERVI FROM $con->catalogo.catalogo_servicio WHERE IDSERVICIO ='$row->IDSERVICIO' ");
								$reg_nombres = $rs_servici->fetch_object(); 		
							}
							
							$info[$i]=$info[$i]."<font color='#DD0000'>".$row->tiempo1."-".$row->tiempo2." / $distriocomuna</font>($reg_nombres->NOMSERVI)<br>"."<font color='#002F5E'>".$row->idusuarioresponsable." - <a href='../../plantillas/etapa1_1.php?idasistencia=".$row->idasistencia."' target='_blank' title='Ver la asistencia'>".$row->idasistencia."</a></font><br>";	
						 }
					}
				?>
					<td width="25%"><span class="style3"><?=$info[1];?></span></td>
					<td width="25%"><span class="style3"><?=$info[2];?></span></td>
					<td width="25%"><span class="style3"><?=$info[3];?></span></td>	 
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
					title: '<?=_("MODIFICAR PRESENTACION HOGAR")?>',
					width: 640,
					height: 420,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'paginar_hogar_general/'
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