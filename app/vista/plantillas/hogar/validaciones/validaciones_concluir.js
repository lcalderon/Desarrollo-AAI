<script type="text/javascript">


function varios(){
	var sw=false;
	if ('<?=$asis->asistencia_servicio->DIAGNOSTICO?>'=='') alert("<?=_('INGRESE EL DIAGNOSTICO DE LA ASISTENCIA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->REPARACION?>'=='') alert("<?=_('INGRESE LA SOLUCION DE LA FALLA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->RECOMENDACION?>'=='') alert("<?=_('INGRESE LA RECOMENDACION EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->PIEZADANIO?>'=='') alert("<?=_('INGRESE EL DETALLE DE LA PIEZA DANIADA')?>");
	else if ('<?=$asis->asistencia_servicio->MATERIAL?>'=='') alert("<?=_('INGRESE LOS MATERIALES UTILIZADOS')?>");
	else sw = true;
	return sw;
}


function seguridad(){
	var sw=false;
	if ('<?=$asis->asistencia_servicio->FECHAFIN?>'=='') alert(<?=_('INGRESE LA FECHA FINAL EN QUE SE BRINDARA LA SEGURIDAD')?>);
	else sw = true;
	return sw;
}




</script>