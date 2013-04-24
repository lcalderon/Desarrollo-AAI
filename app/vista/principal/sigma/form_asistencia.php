<?php 
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');

$idasistencia = $_GET['idasistencia'];

$con = new DB_mysqli();

$sql="
SELECT
a.`IDASISTENCIA`,
e.`IDCUENTA`,
e.`IDPROGRAMA`, 
cs.`DESCRIPCION`,
CONCAT(ep.`NOMBRE`,' ',ep.`APPATERNO`,' ',ep.APMATERNO) AFILIADO,
DATE(amc.`FECHACITA`) FECHACITA,
HOUR(amc.`FECHACITA`) HORA,
MINUTE(amc.`FECHACITA`) MINUTOS

FROM
$con->temporal.`asistencia` a,
$con->temporal.`expediente` e,
$con->temporal.`expediente_persona` ep,
$con->catalogo.`catalogo_servicio` cs,
$con->temporal.`asistencia_medica_controlcitas` amc
WHERE
a.`IDEXPEDIENTE`= e.`IDEXPEDIENTE`
AND e.`IDEXPEDIENTE`= ep.`IDEXPEDIENTE`
AND a.`IDSERVICIO` =cs.`IDSERVICIO`
AND a.`IDASISTENCIA` = amc.`IDASISTENCIA`
AND a.`IDASISTENCIA`='$idasistencia'
AND ep.`ARRTIPOPERSONA`='TITULAR'

";

$result = $con->query($sql);
while ($reg = $result->fetch_object()){
	$servicio = $reg->DESCRIPCION;
	$afiliado = $reg->AFILIADO;
	$fechacita =	$reg->FECHACITA;
	$horacita = $reg->HORA;
	$minutos = $reg->MINUTOS;
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="/librerias/jquery-ui-1.8.23/development-bundle/themes/base/jquery.ui.all.css">
	<script src="/librerias/jquery-ui-1.8.23/js/jquery-1.8.0.min.js"></script>
	<script src="/librerias/jquery-ui-1.8.23/development-bundle/ui/jquery.ui.core.js"></script>
	<script src="/librerias/jquery-ui-1.8.23/development-bundle/ui/jquery.ui.widget.js"></script>
	<script src="/librerias/jquery-ui-1.8.23/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script src="/librerias/jquery-ui-1.8.23/development-bundle/ui/i18n/jquery.ui.datepicker-es.js"></script>
	<link rel="stylesheet" href="../demos.css">
</head>
<body>
<form>
<center><H3>DATOS DE LA ASISTENCIA</H3></center>
<table>
<tr>
	<td>Asistencia </td>
	<td><input type="text" id='idasistencia' value="<?=$idasistencia?>" size='11' disabled></td>
</tr>
<tr>
	<td>Servicio </td>
	<td><input type="text" id='servicio' value="<?=$servicio?>" size='40' disabled></td>
</tr>
<tr>
	<td>Afiliado</td>
	<td><input type="text" id='afiliado' value="<?=$afiliado?>" size='50' disabled></td>
</tr>
<tr>
	<td ></td>
	<td>	
	<input type='radio' name='MOVIMIENTO' id='citas' checked value="Ingresar Cita">Ingresar Cita</input> 
	<input type='radio' name='MOVIMIENTO' id='monitoreo'  value="Ingresar Monitoreo">Ingresar Monitoreo</input>
	</td>
</tr>
<tr>
<td colspan="2"><div id="ingresoCita">

<table>

<tr id='zona_lugar'>
	<td>Lugar</td>
	<td><textarea name='LUGAR' id='lugar' cols='40' style="text-transform:uppercase;"></textarea></td>
</tr>
<tr id='zona_clinica'>
	<td>Clinica</td>
	<td><textarea name='CLINICA' id='clinica' cols='40' style="text-transform:uppercase;"></textarea></td>
</tr>
</table>

</div>
</td>
</tr>



<tr>
<td colspan="2"><div id="ingresoMonitoreo" style="display: none;">

<table>
<tr>
	<td>Fecha de la cita</td>
	<td><input type="text" id="fecha" value='<?=$fechacita?>' readonly> Hora
	<select name='hora' id='hora'>
		<? for($t=0;$t<=24;$t++):?>
			<? if($t<=9) $t='0'.$t; ?> 
			<option <? if($horacita==$t){ ?> selected <?  } ?>><?=$t?></option>
		<? endfor; ?>
	</select>
	<select name='minutos' id='minutos' >
		<? for($t=0;$t<60;$t=$t+10):?>
			<? if($t<=9) $t='0'.$t; ?>
		 <option <? if($minutos==$t){ ?> selected <? } ?> ><?=$t?></option>
		<? endfor; ?>
	</select>
	</td>
</tr>

</table>
</div>
</td>
</tr>

<tr>
	<td>Comentario: </td>
	<td><textarea name='COMENTARIO' id='comentario' cols='40' style="text-transform:uppercase;"></textarea></td>
</tr>

<tr>
	<td colspan='2' id ='mensajes'></td>

</tr> 
 <tr>
 	<td colspan='2'><center>
 	<input type='button' id='grabar' value='Grabar'></input>
 	<input type='button' id='cerrar' value='Cerrar'></input>
 	</center>
 	</td>
</tr>
</table>
</form>
</body>
</html> 
<script type='text/javascript'>

	$(document).ready(function(){
		$("#citas").click(function(evento){
			if ($("#citas").attr("checked")){
				$("#ingresoCita").css("display", "block");
				$("#ingresoMonitoreo").css("display", "none");
			}
		});
	});

	$(document).ready(function(){
		$("#monitoreo").click(function(evento){
			if ($("#monitoreo").attr("checked")){
				$("#ingresoCita").css("display", "none");
				$("#ingresoMonitoreo").css("display", "block");
			}
		});
	});
 


	$(function() {
		$( "#fecha" ).datepicker({ dateFormat: "yy-mm-dd"});
	});

$(document).ready(function(){

	// Ajax para grabar los datos
	$('#grabar').click(function(){
		$('#mensajes').html('');
		
		var fechahora = "Se cambio fecha de la cita a: " + $('#fecha').val()+' '+$('#hora').val()+':'+$('#minutos').val();
	
		$.post('grabadatos.php',{
								idasistencia: $('#idasistencia').val(),
								citas: $("#citas").attr("checked"),
								monitoreo: $("#monitoreo").attr("checked"),
								lugar: $('#lugar').val(),
								clinica: $('#clinica').val(),
								comentario: $('#comentario').val(),
								fecha: fechahora
								},function(data){
									if (data.status)  $('#mensajes').html("<font color='green'>Los datos se grabaron correctamente</font>"); 
									else
										$('#mensajes').html("<font color='red'>Error al grabar los datos </font>");
								},"json");  //final de ajax
		}); // fin de la funcion  grabar

	// Ajax para cerrar	
	$('#cerrar').click(function(){
		parent.location.reload();
		})

	//


	
	
}); // fin de la funcion principal
	
</script>