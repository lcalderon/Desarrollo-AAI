function administrativamensajeria(){
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

	var sw=false;
	//alert($F('subservicio'));
	if($F('subservicio')==''){ alert('SELECCIONE UN SUBSERVICIO'); 
	}else if($F('subservicio')=="EDOC"){
	    if ($F('d_idretiro')=='') alert('INGRESE LA DIRECCION DE RETIRO');
	    else if ($F('d_personaentrega')=='') alert('INGRESE EL NOMBRE DE LA PERSONA QUE ENTREGA');
	    else sw = true;
	}else if($F('subservicio')=='COVA'){
	    if ($F('c_idretiro')=='') alert('INGRESE LA DIRECCION DEL ESTABLECIMIENTO');
	    else if ($F('c_descripcion')=='') alert('INGRESE LA DESCRIPCION DE LA COMPRA');
	    else sw = true;
	}else if($F('subservicio')=='MERE'){
	    if ($F('m_remitente')=='') alert('INGRESE EL NOMBRE DEL REMITENTE');
	    else if ($F('m_destinatario')=='') alert('INGRESE EL NOMBRE DEL DESTINATARIO');
	    else if ($F('m_iddestino')=='') alert('INGRESE LA DIRECCION DEL DESTINATARIO');
	    else if ($F('date2')=='') alert('INGRESE LA FECHA DE ENTREGA');
	    else if ($F('date2')<fechaactual) alert('LA FECHA DE ENTREGA NO PUEDE  SER MENOR A LA FECHA ACTUAL');
	    else sw = true;
	  }
	return sw;
}

function funeraria(){
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

	var sw=false;
	if ($F('date2')=='') alert('INGRESE LA FECHA DE DECESO');
	else if ($F('date2')>fechaactual) alert('LA FECHA DE DECESO NO PUEDE SER MAYOR A LA FECHA ACTUAL');
	else if ($F('motivo')=='') alert('INGRESE EL MOTIVO DEL DECESO');
	else sw = true;
	return sw;
}

