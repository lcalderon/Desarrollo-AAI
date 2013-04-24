function ambulancia(){
	var sw=false;
	if ($F('sintomatologia')=='') alert("INGRESE SINTOMATOLOGIA");
	else sw = true;
	return sw;
}

function prestacionmedica(){
	var sw=false;
	if ($F('sintomatologia')=='') alert("INGRESE SINTOMATOLOGIA");
	else sw = true;
	return sw;
}

function deliverymedicamentos(){
	var sw=false;
	if ($F('nombremedicamento')=='') alert("INGRESE NOMBRE DEL MEDICAMENTO");
	else if ($F('idlugarentrega')=='') alert('INGRESE LUGAR DE LA ENTREGA');
	else if ($F('nombredestinatario')=='') alert('INGRESE NOMBRE DEL DESTINATARIO');
	else sw = true;
	return sw;
}

function controlmedicamentos(){
	var sw=false;
	if ($F('prescripcionmedica')=='') alert("INGRESE PRESCRIPCION MEDICA");
	else if ($F('horaprimeratoma')=='') alert('INGRESE HORA DE LA PRIMERA TOMA');
	else sw = true;
	return sw;
}


function controlcitas(){
	var sw=false;
	if ($F('idlugarcita')=='') alert("INGRESE LUGAR DE LA CITA");
	else if ($F('date2')=='') alert('INGRESE LA FECHA DE LA CITA');
	else if ($F('sintomatologia')=='') alert("INGRESE SINTOMATOLOGIA");
	else sw = true;
	return sw;
}

function descuentomedicamento(){
	var sw=true;
	return sw;
}
