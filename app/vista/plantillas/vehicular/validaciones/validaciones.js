function remolque(){
	var sw=false;
	if($F('txtadescripcion')==''){
		alert('INGRESE LA DESCRIPCION DE LO OCURRIDO.');		
		$('txtadescripcion').focus();
		
	} else if($F('iddestino')==''){
		alert('INGRESE EL LUGAR DEL DESTINO.');		
		$('iddestino').focus();
	}
	else{	
		sw = true;
	}
	return sw;
}


function auxiliovial(){
	var sw=false;
	if($F('txtadescripcion')==''){
		alert('INGRESE LA DESCRIPCION DE LO OCURRIDO.');		
		$('txtadescripcion').focus();
	}	
	else if($F('cmbtipoauxilio')==''){
		alert('SELECCIONE EL TIPO DE AUXILIO VIAL.');		
		$('cmbtipoauxilio').focus();
	}
	else{	
		sw = true;
	}
	return sw;
}


function cerrajeriavial(){
	var sw=false;
	if($F('txtaposicionp')==''){
		alert('INGRESE LA DESCRIPCION DE LO OCURRIDO.');
		$('txtaposicionp').focus();
	}
	else{	
		sw = true;
	}
	return sw;
}

function choferremplazo(){	
	var sw=false;
	if($F('txtadescripcion')==''){
		alert('INGRESE EL MOTIVO DEL SERVICIO.');		
		$('txtadescripcion').focus(); 
    } else if($F('iddestino')==''){
        alert('INGRESE EL LUGAR DEL DESTINO.');        
        $('iddestino').focus();
    } else{	
		sw = true;
	}
	return sw;
}

function mecanicaligera(){	
	// var sw=false;
	// if ($f('descripcionservicio')=='') alert('ingrese descripcion de la falla');
	// else if ($f('iddestino')=='') alert('ingrese la direccion de destino');
	// else sw = true;
	return true;
}

function asesorialegal(){
	var sw=false;
	if ($F('descripciondelhecho')=='') alert("INGRESE DESCRIPCION DEL HECHO");
	else sw = true;
	return sw;
}



