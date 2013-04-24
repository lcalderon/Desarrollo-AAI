<?
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php'); 
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");	
	include_once('../../includes/head_prot_win.php');
		
	$con= new DB_mysqli();	
		 
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
 	session_start();  
 	Auth::required();	
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

	$Sql_verfica="SELECT
				  catalogo_afiliado.IDAFILIADO,
				  catalogo_afiliado.CVEAFILIADO,
				  expediente.CVEAFILIADO,
				  expediente.IDEXPEDIENTE
				FROM $con->temporal.expediente
				  LEFT JOIN  $con->catalogo.catalogo_afiliado
					ON catalogo_afiliado.IDAFILIADO = expediente.IDAFILIADO
				WHERE catalogo_afiliado.CVEAFILIADO ='".$_GET["cve_id"]."' ";

	$afiliado=$con->consultation($Sql_verfica);	
	
 	$idafiliado=$afiliado[0][0];
 	$cveafiliado=$afiliado[0][1];
 	$cveexpediente=($afiliado[0][2])?$afiliado[0][2]:$_GET["cve_id"];
	 
	$Sql_asistencia="SELECT
					 asistencia_vehicular_datosvehiculo.ID,
					 MIN(asistencia_usuario.FECHAHORA)      AS FECHAHORA,
					  asistencia.IDASISTENCIA,
					  expediente.IDEXPEDIENTE,
					  asistencia_vehicular_datosvehiculo.PLACA,
					  asistencia_vehicular_datosvehiculo.MARCA,
					  asistencia_vehicular_datosvehiculo.SUBMARCA,
					  asistencia_vehicular_datosvehiculo.ANIO,
					  asistencia_vehicular_datosvehiculo.COLOR,
					  asistencia_vehicular_datosvehiculo.NUMVIN,
					  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA ) AS servicio
					FROM $con->temporal.asistencia_vehicular_datosvehiculo
					  INNER JOIN $con->temporal.asistencia
						ON asistencia.IDASISTENCIA = asistencia_vehicular_datosvehiculo.IDASISTENCIA
					  INNER JOIN $con->temporal.expediente
						ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
					  LEFT JOIN $con->temporal.asistencia_usuario
						ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
					  LEFT JOIN $con->catalogo.catalogo_programa
						ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
					  LEFT JOIN $con->catalogo.catalogo_programa_servicio
						ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
					  LEFT JOIN $con->catalogo.catalogo_servicio
						ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO						
					  LEFT JOIN $con->catalogo.catalogo_cuenta
						ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA							
					WHERE $ver_cuentas 
					expediente.CVEAFILIADO != '' AND expediente.CVEAFILIADO = '$cveexpediente'
					GROUP BY expediente.IDEXPEDIENTE,asistencia_usuario.IDASISTENCIA
					ORDER BY asistencia.IDASISTENCIA DESC";

	$Sql_sac="SELECT
			  catalogo_afiliado_persona_vehiculo.ID,
			  MIN(catalogo_afiliado_persona_vehiculo_log.FECHAMOD) AS fecha,			  
			  catalogo_afiliado_persona_vehiculo.PLACA,
			  catalogo_afiliado_persona_vehiculo.MARCA,
			  catalogo_afiliado_persona_vehiculo.SUBMARCA,
			  catalogo_afiliado_persona_vehiculo.ACTIVO,
			  catalogo_afiliado_persona_vehiculo.ANIO,
			  catalogo_afiliado_persona_vehiculo.COLOR,
			  catalogo_afiliado_persona_vehiculo.NUMVIN
			FROM $con->catalogo.catalogo_afiliado
			  INNER JOIN $con->catalogo.catalogo_afiliado_persona_vehiculo
				ON catalogo_afiliado_persona_vehiculo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_vehiculo_log
				ON catalogo_afiliado_persona_vehiculo_log.IDAFILIADO = catalogo_afiliado.IDAFILIADO				
			WHERE $ver_cuentas 	
				catalogo_afiliado.CVEAFILIADO = '$cveafiliado' 
				AND catalogo_afiliado.CVEAFILIADO != '0'
				AND catalogo_afiliado_persona_vehiculo.ACTIVO=1
				GROUP BY catalogo_afiliado_persona_vehiculo.ID
				ORDER BY catalogo_afiliado_persona_vehiculo.ACTIVO DESC "; 
 
	$result1=$con->query($Sql_asistencia);
	$result2=$con->query($Sql_sac);

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_('American Assist')?></title>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FFFFFF; font-weight: bold; }
-->
</style>
</head>

<body>  
    	
	<strong><?=_('REGISTROS DE VEHICLOS DE ASISTENCIAS BRINDADAS')?></strong>	
	<table cellpadding="1" cellspacing="1" border="0" id="table2" class="tinytable" style="width:100%">
		<thead>
			<tr> 
				<th style="color:#FFFFFF;background:#558DC6"><h3><?=_('FECHAREGISTRO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ASISTENCIA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('EXPEDIENTE')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('SERVICIO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('PLACA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('MARCA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('SUBMARCA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ANIO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('COLOR')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('VIN')?></h3></th>
			    <th style="background:#558DC6"></th>
			</tr>
		</thead>
		<tbody>
		<?	
			$linea=0;
			while ($reg=$result1->fetch_object()){
				
				$colorlinea = ($linea%2)? 'par':'impar';
				echo "<tr class='$colorlinea' >";
				echo "<td align='center'>$reg->FECHAHORA</td>";
				echo "<td align='center'>$reg->IDASISTENCIA</td>";
				echo "<td align='center'>$reg->IDEXPEDIENTE</td>";
				echo "<td align='center'>$reg->servicio</td>";
				echo "<td>$reg->PLACA</td>";
				echo "<td>$reg->MARCA</td>";
				echo "<td>$reg->SUBMARCA</td>";
				echo "<td>".utf8_encode($reg->ANIO)."</td>";
				echo "<td>$reg->COLOR</td>";
				echo "<td>$reg->NUMVIN</td>";
				echo "<td align='center'><a href='#' title='"._('SELECCIONAR VEHICULO')."' onclick=\"window.close();window.opener.cargar_vehiculo('$reg->ID','ASI')\" >SELECCIONAR</a></td>";
				echo "</tr>";
				$linea++;
			}
		?>
		</tbody>
	</table> 
 
 <br>
 
 <div id="tablewrappers" > 	   
<div style="float:left;">
	<br>
	<strong><?=_('VEHICULOS REGISTRADAS EN SAC')?></strong>	
	<table cellpadding="1" cellspacing="1" border="0" id="table2" class="tinytable" style="width:100%">
		<thead>
			<tr> 
				<th style="background:#558DC6"><h3><?=_('FECHAREGISTRO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('PLACA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('MARCA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('SUBMARCA')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('ANIO')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('COLOR')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('VIN')?></h3></th>
				<th style="background:#558DC6"><h3><?=_('STATUS')?></h3></th>
			    <th style="background:#558DC6" colspan="2"></th>
			</tr>
		</thead>
		<tbody>
		<?	

		$linea=0;
		while ($reg=$result2->fetch_object())
		{
			$status=($reg->ACTIVO==1)?_('ACTIVO'):_('INACTIVO');
			$colorlinea = ($linea%2)? 'par':'impar';
			echo "<tr class='$colorlinea' >";
			echo "<td align='center'>$reg->fecha</td>";
			echo "<td>$reg->PLACA</td>";
			echo "<td>$reg->MARCA</td>";
			echo "<td>$reg->SUBMARCA</td>";
			echo "<td>".utf8_encode($reg->ANIO)."</td>";
			echo "<td>$reg->COLOR</td>";
			echo "<td>$reg->NUMVIN</td>";
			echo "<td>$status</td>";
			echo "<td><img src='../../../../imagenes/iconos/historia_s.gif'  title='"._("HISTORIAL")."'  style='cursor:pointer' onClick=\"ventana_historialvehiculo('$reg->ID')\" ></td>";
			echo "<td><a href='#' title='"._('SELECCIONAR VEHICULO')."' onclick=\"window.close();window.opener.cargar_vehiculo('$reg->ID','SAC')\" >SELECCIONAR</a></td>";
			echo "</tr>";
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

<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_historialvehiculo(id){
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("HISTORIAL -> STATUS VEHICULOS")?>',
					width: 350,
					height: 170,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: '../../catalogos/siac/historial_vehiculos.php?id='+id
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
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