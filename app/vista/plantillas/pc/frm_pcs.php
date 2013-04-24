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

	$afiliado=$con->consultation($Sql_verfica);	
	 
	$Sql_pcs="SELECT
			  MIN(asistencia_usuario.FECHAHORA) AS FECHAHORA,
			  expediente.IDEXPEDIENTE,
			  asistencia_pc.ID,
			  asistencia_pc.IDASISTENCIA,
			  asistencia_pc.MARCA,
			  asistencia_pc.MODELO,
			  asistencia_pc.NUMEROSERIE,
			  asistencia_pc.FECHADECOMPRA,
			  asistencia_pc.SISTEMAOPERATIVO,
			  IF(catalogo_programa_servicio.ETIQUETA IS NULL,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA ) AS servicio
			FROM $con->temporal.asistencia_pc
			  INNER JOIN $con->temporal.asistencia
				ON asistencia.IDASISTENCIA = asistencia_pc.IDASISTENCIA	 			
			  LEFT JOIN $con->temporal.asistencia_usuario
				ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA				
			  INNER JOIN $con->temporal.expediente
				ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
			  LEFT JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
			  LEFT JOIN $con->catalogo.catalogo_programa_servicio
				ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
			  LEFT JOIN $con->catalogo.catalogo_servicio
				ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO						
			  LEFT JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
			WHERE $ver_cuentas
			expediente.CVEAFILIADO = '".$_GET["cve_id"]."' AND expediente.CVEAFILIADO != ''
			GROUP BY expediente.IDEXPEDIENTE,asistencia_usuario.IDASISTENCIA
			ORDER BY asistencia.IDASISTENCIA DESC ";
		//echo $Sql_pcs;
	 
	$result=$con->query($Sql_pcs);

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FFFFFF; font-weight: bold; }
-->
</style>
</head>

<body>   
    	
	<strong><?=_('REINCIDENCIAS DE REGISTROS PCS')?></strong>	
	<table cellpadding="1" cellspacing="1" border="0" id="table2" class="tinytable" style="width:100%">
		<thead>
			<tr> 
				<th style="color:#FFFFFF;background:#558DC6"><h3><?=_('FECHAREGISTRO')?></h3></th>
				<th style="color:#FFFFFF;background:#558DC6"><h3><?=_('ASISTENCIA')?></h3></th>
				<th style="color:#FFFFFF;background:#558DC6"><h3><?=_('EXPEDIENTE')?></h3></th>
				<th><h3><?=_('SERVICIO')?></h3></th>
				<th><h3><?=_('MARCA')?></h3></th>
				<th><h3><?=_('MODELO')?></h3></th>
				<th><h3><?=_('NRO SERIE')?></h3></th>
				<th><h3><?=_('FECHACOMPRA')?></h3></th>
				<th><h3><?=_('SISTEMA OPERATIVO')?></h3></th>
			    <th></th>
			</tr>
		</thead>
		<tbody>
		<?	
		$linea=0;
		while ($reg=$result->fetch_object())
		{
			$colorlinea = ($linea%2)? 'par':'impar';
			echo "<tr class='$colorlinea' >";
			echo "<td align='center'>$reg->FECHAHORA</td>";
			echo "<td align='center'>$reg->IDASISTENCIA</td>";
			echo "<td align='center'>$reg->IDEXPEDIENTE</td>";
			echo "<td align='center'>$reg->servicio</td>";
			echo "<td align='center'>$reg->MARCA</td>";
			echo "<td>$reg->MODELO</td>";
			echo "<td>$reg->NUMEROSERIE</td>";
			echo "<td>$reg->FECHADECOMPRA</td>";
			echo "<td>".utf8_encode($reg->SISTEMAOPERATIVO)."</td>";
			echo "<td align='center'><a href='#' title='"._('SELECCIONAR PC')."' onclick=\"window.close();window.opener.cargar_pcs('$reg->ID')\" >SELECCIONAR</a></td>";
			echo "</tr>";
			$linea++;
		}
		?>
		</tbody>
	</table> 
	<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script> 
</body>
</html>
 