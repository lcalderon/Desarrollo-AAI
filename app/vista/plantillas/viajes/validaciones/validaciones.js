function transporte(){
	var sw=false;
	sw = true;
	return sw;
}


function hotel(){
	var sw=false;
	if ($F('nombrehotel')=='') alert('INGRESE NOMBRE DEL HOTEL');
	else if ($F('date2')=='') alert('INGRESE FECHA DE INICIO DE HOSPEDAJE');
	else if ($F('date3')=='') alert('INGRESE FECHA DE FIN DE HOSPEDAJE');
	else if ($F('ndias')=='') alert('INGRESE EL NUMERO DE DIAS DE HOSPEDAJE');
	else sw = true;
	return sw;
}

function variosviajes(){
	var sw=false;
	sw = true;
	return sw;
}

function rentaauto(){
	var sw=false;
	sw = true;
	return sw;
}

function nacionalinternacional(){
	var sw=false;
	sw = true;
	return sw;
}
