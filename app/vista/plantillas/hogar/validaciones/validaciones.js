function varios(){
	var sw=false;
	if ($F('descripcionservicio')=='') alert('INGRESE DESCRIPCION DE LO OCURRIDO');
	//else if ($F('ubicaciondanioparcial')=='') alert('SELECCIONE LA UBICACION DEL DANIO');
	else if ($F('ubicaciondanio')=='PAR' && $F('ubicaciondanioparcial')=='') alert('SELECCIONE LA UBICACION DEL DANIO');

	else sw = true;
	return sw;
}

function seguridad(){
	var miFechaActual = new Date();
	dia = miFechaActual.getDate()
	mes = parseInt(miFechaActual.getMonth()) + 1
	ano = miFechaActual.getFullYear() 
	if(mes<=9){
	    mes="0"+mes;
	}
	if(dia<=9){
	    mes="0"+mes;
	}
	fechaactual=ano + "-" + mes + "-" + dia;
	//alert(ano + "-" + mes + "-" + dia + "  /  "+$F('date3'));
	var sw=false;
	if ($F('descripcionservicio')=='') alert('INGRESE LA DESCRIPCION DEL HECHO');
	else if ($F('date3')=='') alert('INGRESE LA FECHA INICIAL');
	else if ($F('date3')=='0000-00-00') alert('INGRESE LA FECHA INICIAL');
	else if ($F('date3')<fechaactual) alert('LA FECHA INICIAL NO PUEDE SER MENOR A LA ACTUAL');
	else if ($F('date2')!='' || $F('date2')!='0000-00-00')
	     if ($F('date2')<$F('date3')) alert('LA FECHA FINAL NO PUEDE SER MAYOR A LA FECHA INICIAL');
	      else sw = true;
	else  sw = true;
	return sw;
}





