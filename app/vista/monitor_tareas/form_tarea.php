<?php

include_once '../../modelo/clase_lang.inc.php';
$idasistencia=$_GET['idasistencia'];
$idexpediente=$_GET['idexpediente'];
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
</head>
<body>
<form>

	<table>
		<tr>
			<td><?=_('Monitoreo al') ?>: </td>
			<td>
			<select id='idtarea' name='IDTAREA'>
				<option value=''><?=_('Seleccione') ?></option>
				<option value='MON_AFIL'><?=_('MONITOREO AL AFILIADO')?></option>
				<option value='MON_PROV'><?=_('MONITOREO AL PROVEEDOR')?></option>
				<option value='CONT_AFIL'><?=_('CONTACTO AL AFILIADO')?></option>
				<option value='LLAM_CON'><?=_('LLAMADA DE CONFORMIDAD')?></option>
			
			</select>
			</td>
		</tr>
		<tr>
			<td><?=_('Fecha hora')?></td>
			<td><input type="text" id="fecha" size='10' value='' readonly>
			<select name='hora' id='hora'>
				<? for($t=0;$t<=23;$t++):?>
					<? if($t<=9) $t='0'.$t; ?> 
					<option <? if($horacita==$t){ ?> selected <?  } ?>><?=$t?></option>
				<? endfor; ?>
			</select>
			<select name='minutos' id='minutos' >
				<? for($t=0;$t<60;$t++):?>
					<? if($t<=9) $t='0'.$t; ?>
		 		<option <? if($minutos==$t){ ?> selected <? } ?> ><?=$t?></option>
				<? endfor; ?>
			</select>
			</td>
		</tr>	
		<tr>	
			<td colspan='2' id='resultado'> </td>
			
		</td>
		<tr>
			<td colspan=2>
			<input type='button' id='grabar' value="<?=_('Grabar')?>">
			<input type='button' id='salir' value="<?=_('Salir')?>"> 
			</td>
		</tr>
	</table>
</form>
</body>
</html>

<script type='text/javascript'>
$(function(){
	$( "#fecha" ).datepicker({ dateFormat: "yy-mm-dd"});
});

$(document).ready(function(){

	$('#grabar').click(function(){
		$(this).attr('disabled','disabled');
		$('#resultado').html('');
		if ($('#idtarea').val()=='')  alert("<?=_('Seleccione el tipo de monitoreo') ?>");
		else if ($('#fecha').val()=='') alert("<?=_('Ingrese la fecha del monitoreo')?>");
		else {
			
			$.post('graba_tarea.php',{
					IDASISTENCIA: '<?=$idasistencia?>',
					IDEXPEDIENTE: '<?=$idexpediente?>',
					IDTAREA : $('#idtarea').val(),
					FECHATAREA : $('#fecha').val()+' '+$('#hora').val()+':'+$('#minutos').val()+':00' 
				},function(msg){
				if (msg.status) { 
					$('#resultado').html("<font color='Green'><?=_('Tarea grabada')?></font>");
				
					}
				else{
					$('#resultado').html("<font color='red'><?=_('Error al grabar tarea')?></font>");
					
					}
				
				},'json');
		
			}
		
		}
	);

	$('#salir').click(function(){
		parent.win.close();
		});
	
});


</script>
