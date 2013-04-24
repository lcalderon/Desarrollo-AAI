<script type="text/javascript">


function asistentetareaweb(){
	var sw=false;
	if ('<?=$asis->asistencia_servicio->DIAGNOSTICO?>'=='') alert("<?=_('INGRESE EL DIAGNOSTICO DE LA ASISTENCIA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->SOLUCIONFALLA?>'=='') alert("<?=_('INGRESE LA SOLUCION DE LA FALLA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->RECOMENDACION?>'=='') alert("<?=_('INGRESE LA RECOMENDACION EN LA APERTURA')?>");
	else sw = true;
	return sw;
}


function consultatelefonica(){
	var sw=false;
	if ('<?=$asis->asistencia_servicio->DIAGNOSTICO?>'=='') alert(<?=_('INGRESE ')?>);
	else if ('<?=$asis->asistencia_servicio->SOLUCIONFALLA?>'=='') alert("<?=_('INGRESE LA SOLUCION DE LA FALLA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->RECOMENDACION?>'=='') alert("<?=_('INGRESE LA RECOMENDACION EN LA APERTURA')?>");
	else sw = true;
	return sw;
}


function visitatecnica(){	
	var sw=false;
	if ('<?=$asis->asistencia_servicio->DIAGNOSTICO?>'=='') alert(<?=_('INGRESE')?>);
	else if ('<?=$asis->asistencia_servicio->SOLUCIONFALLA?>'=='') alert("<?=_('INGRESE LA SOLUCION DE LA FALLA EN LA APERTURA')?>");
	else if ('<?=$asis->asistencia_servicio->RECOMENDACION?>'=='') alert("<?=_('INGRESE LA RECOMENDACION EN LA APERTURA')?>");
	else sw = true;
	return sw;
}

</script>