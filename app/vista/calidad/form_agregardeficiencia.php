<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");

	Auth::required();

	$idasistencia=$_GET["asistencia"];
	$idexpediente=$_GET["expediente"];
	$idetapa = $_GET["idetapa"];
 
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<title><?=_("AgregarDeficiencia");?></title>
	<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link> 
	<link href="../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>	
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_ext.js"></script>
	
	<style type="text/css">

		body{
			background-color:#ecefcb;
		}

		form fieldset{
			background-color:#e9f9ac;
			
			border-width:2px 2px 2px 10px;
			border-style:solid;
			border-color:#cee574;
			
			font-family:Arial, Helvetica, sans-serif;
			font-size:12px;
				
			margin:20px 0px 20px 20px;
			width:350px;
			position:relative;
			display:block;
			padding: 0px 10px 10px 10px;
		}	
	</style>	
</head>
<body>
<?	
	if(!$idetapa){
// SI ES LA ETAPA 0 SE CARGAN TODAS LAS DEFICIENCIAS AGRUPADAS POR ETAPA
		$result = $con->query("SELECT * FROM $con->catalogo.catalogo_etapa WHERE IDETAPA <='".$_GET["etapa"]."' ");
		while($reg=$result->fetch_object()){
		//recorremos las etapas
			$Sql_deficxetapa="SELECT distinct
					  CD.CVEDEFICIENCIA,
					  CD.NOMBRE,
					  ED.IDETAPA
					FROM $con->catalogo.catalogo_deficiencia CD
					  INNER JOIN $con->catalogo.catalogo_deficiencia_etapa ED
						ON ED.CVEDEFICIENCIA = CD.CVEDEFICIENCIA
					WHERE ED.IDETAPA = $reg->IDETAPA";
?>					
			<fieldset><legend style='font-weight:bold;font-size:14px'><?=$reg->IDETAPA." - ".$reg->DESCRIPCION?></legend>
				<table width='100%' bgcolor="#E7EAB9" border=1 cellpadding='1' cellspacing='1' style='border-collapse:collapse;font-size:10px'>
					<tbody>			
				<?
					//cargamos las deficiencias asociadas a cada etapa
					$resultxetapa=$con->query($Sql_deficxetapa);
					while($row= $resultxetapa->fetch_object()){
			 
				?>
					<tr>
						<td><strong><?=$row->CVEDEFICIENCIA?></strong><?=' - '.$row->NOMBRE;?></td>
						<td width="87px"><input type="button" class="normal" name='btnvalida' id='btnvalida' title="<?=_("Agregar Deficiencia")?>" value="<?=_("AGREGAR")?>" class="normal" onclick="presentar_formulario('','validardeficiencia.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=$row->CVEDEFICIENCIA."-".$row->NOMBRE?>','430','210','','<?=$idexpediente?>','<?=$idasistencia?>','<?=$row->CVEDEFICIENCIA?>','<?=$row->IDETAPA?>')"></td>
					</tr>
				<?
					} 
				?>
					</tbody>
				</table>
			</fieldset>
<?
		}
	} else{
 // CASO CONTRARIO CARGAMOS LAS DEFICIENCIAS DE LA ETAPA RESPECTIVA

		$Sql_defic="SELECT
				  CD.CVEDEFICIENCIA,
				  CD.NOMBRE,
				  ED.IDETAPA				  
				FROM  $con->catalogo.catalogo_deficiencia CD
				  INNER JOIN  $con->catalogo.catalogo_deficiencia_etapa ED
					ON ED.CVEDEFICIENCIA = CD.CVEDEFICIENCIA
				WHERE ED.IDETAPA ='$idetapa'";

?>
	<div id='deficiencia1'>
		<fieldset><legend style='font-weight:bold;font-size:14px'><?=$idetapa." - ".$_GET["nombreEtapa"]?></legend>
			<table width="100%" border="1" cellpadding="1" cellspacing="1" style="border-collapse:collapse" >
				<?
					$result=$con->query($Sql_defic);
					while($reg = $result->fetch_object()){
				?>
				<tr>
					<td><?=$reg->CVEDEFICIENCIA.' - '.$reg->NOMBRE;?></td>
					<td align='center' width="87px"><input type="button" name='btnvalida' id='btnvalida' title="<?=_("Agregar Deficiencia")?>"  value="<?=_("AGREGAR")?>" class="normal" onclick="presentar_formulario('','validardeficiencia.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=$reg->CVEDEFICIENCIA."-".$reg->NOMBRE?>','430','210','','<?=$idexpediente?>','<?=$idasistencia?>','<?=$reg->CVEDEFICIENCIA?>','<?=$reg->IDETAPA?>')"></td>	   
				</tr>
				<? 
					}
				?>
			</table>
		</fieldset>	
	</div>	 
<? 	} ?>
	
</body>
</html>