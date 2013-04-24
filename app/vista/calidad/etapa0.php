<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	include_once("../../modelo/afiliado/asistencias.class.php");
	include_once("../../modelo/functions.php");
	include_once("../includes/arreglos.php");

 	Auth::required($_SERVER['REQUEST_URI']);

	$idasistencia=$_GET["idasistencia"];

//Validar asistencia
	validar_url($_GET["idasistencia"],$_GET["varexis"],_("ID DEL ASISTENCIA NO ESTA VALIDADO"));
	 
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	$asis = new asistencias($idasistencia);
	$infoasistencia=$asis->informacionAsistencia();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<title>:: <?=_("Consolidado de Deficiencias");?></title>
	<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../estilos/plantillas/menu.css" rel="stylesheet" type="text/css"/>
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css"></link>
	<link href="../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css"></link>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_ext.js"></script>

	<link rel="shortcut icon" type="image/x-icon" href="../../../imagenes/iconos/soaa.ico">	  

	<style type="text/css">
	<!--
	body {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 15px;
		margin:0px
	}

	.barraestado{
		position: fixed;
		top: auto!important;
		bottom: 0px!important;
		left: 0px!important;
		z-index: 1;
		border: solid 1px #000000;
		background:url(/imagenes/iconos/fondo_barraestado.gif) repeat-x;
		padding: 3px;
		width: 99%;
	}

	input{
	font-size: 10px;
	font-family: Verdana, Arial, Helvetica, sans-serif;

	}

	input[type=button]{
	border-width:2px;
	border-style:solid;
	-webkit-border-radius:3px;
	-moz-border-radius:3px;
	border-radius:3px;
	height:22px;

	}

	input[type=button].cancelar {
	border-color:red;
	}

	input[type=button].guardar {
	border-color:green;
	}

	input[type=button].normal {
	border-color:#003C74;
	}

	input[type=submit]{
	border-width:2px;
	border-style:solid;
	border-color:green;
	}

	#div-resumen {
		position:absolute;
		left:845px;
		top:50px;
		width:111px;
		height:78px;
	}
	-->
	</style>

</head>
<body  >

	<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#444444" align="center" style="font-size:12px;">
		<tr>
			<td height="21px" width="125px" style="color:#FFFFFF;font-weight:bold"><?=_("NRO EXPEDIENTE")?></td>
			<td width="65px" bgcolor="#ADC6DE"><?=$infoasistencia["IDEXPEDIENTE"]?></td>
			<td width="122px" style="color:#FFFFFF;font-weight:bold"><?=_("NRO ASISTENCIA")?></td>
			<td width="70px" bgcolor="#ADC6DE"><?=$infoasistencia["IDASISTENCIA"]?></td>
			<td width="70px" style="color:#FFFFFF;font-weight:bold"><?=_("SERVICIO")?></td>
			<td width="405px"bgcolor="#ADC6DE"><strong><?=$infoasistencia["SERVICIO"]?></strong></td>
			<td style="color:#FFFFFF;font-weight:bold" align="right"><?=_("STATUS EVALUACION")?>&nbsp;</td>
			<td bgcolor="#ADC6DE" width="86px">
			<?
				$con->cmb_array("cmbevaluacion",$evalexped,($infoasistencia["STATUSCALIDAD"] !="CERRADO")?"EVALUADO":"CERRADO","onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'",($asis->statuscalidad=="CERRADO")?"2":"1",_("TODOS"),"","SEVALUAR");
			?>
			</td>
		</tr>
	</table>	
	<br/>
 
	<?
        //incluir el mensaje de asistencia abandonado
		$idetapa_asis=$infoasistencia["IDETAPA"];

		$msj=_("ASISTENCIA ABANDONADO");
		include_once("../asistencia/principal/menuItems_calidad.js.php");
	?>
	<div id='listado_de_servicios'><? include_once("form_consolidado.php");?></div>
	<br/>
	<div id='div-resumen' style="width:41%" align="right"><? include_once("resumen_tiempos.php");?></div>
	<div id='barra' class="barraestado">
		<form name="frmbarra" id="frmbarra" method="post">
			<table align="right">
				<tbody>
					<tr>
						<input type="hidden" name="hid_cantideficiencia" id="hid_cantideficiencia" value="<?=$cantidadDefActivas;?>"/>
						<input type="hidden" name="hid_asistencia" value="<?=$idasistencia?>"/>
						<input type="hidden" name="cantidadDef" id="cantidadDef" value="<?=$res_servicio[0][1]?>"/>
						<td><input type="button" id="btn_delegar" value="<?=_("DELEGACIONES")?>" title="Ver delegaciones" onClick="presentar_formulario('','../principal/delegar/historial_delegar.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("HISTORIAL DE DELEGACION")?>','740','300','','','<?=$idasistencia?>')" class="normal" ></td>
						<td><input type="button" id="btn_bitacora" value="<?=_("BITACORA")?>" title="Ver bitacora" onClick="presentar_formulario('','../../vista/bitacora/bitacora.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("BITACORA ASISTENCIA")?>','780','300','','','<?=$idasistencia?>')" class="normal" ></td>
						<td><input type='button' id='btn_agregar' value='<?=_("AGREGAR DEFICIENCIAS")?>' title="Agregar deficiencias manuales" class='normal' <?=$desactivado?> onClick="$('btn_confirma').disabled=true;presentar_formulario('1','form_agregardeficiencia.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("AGREGAR DEFICIENCIAS MANUALES")?>' ,'700','500','','<?=$infoasistencia["IDEXPEDIENTE"]?>','<?=$idasistencia?>','','','','<?=$idetapa_asis?>')"></td>
						<td><input type='button' id='btn_confirma' value='<?=_("CONFIRMAR EVALUACION DE ASISTENCIA")?>' title="Confirmar Evaluaci&oacute;n" class='guardar' onClick="ConfirmaEvaluacion()"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>

</body>
<script type="text/javascript" >

	function ConfirmaEvaluacion(){

		var idstatuseval= document.getElementById("cmbevaluacion").value;
		if($F('hid_cantideficiencia') >0){

			alert('<?=_("FALTAN VALIDAR / RETIRAR DEFICIENCIA(S)")?>.');

		} else{

			if(confirm('<?=_("ESTA SEGURO QUE DESEA CONCLUIR CON LA EVALUACION DE LA ASISTENCIA?")?>')){
				document.getElementById("frmbarra").action='gconfirmaevaluacion.php?cmbevaluacion='+idstatuseval;
				document.getElementById("frmbarra").submit()
			}
		}
	}

</script>
</html>