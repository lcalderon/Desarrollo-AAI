function docenteadomicilio(){
 
	return true;
}


function trasladoescolar(){
	var sw=false;

	if ($F('dateesc1') < $F('dateactual') ||  $F('dateesc2') < $F('dateactual') ){
		alert('LA FECHA NO DEBE SER MENOR A LA ACTUAL.');
		
		if ($F('dateesc1') < $F('dateactual'))	$('dateesc1').focus();
		if ($F('dateesc2') < $F('dateactual'))	$('dateesc2').focus();
	
	}
	else if ($F('dateesc2') < $F('dateesc1')){
		alert('LA FECHA DE REGRESO DEBE SER IGUAL O MAYOR A LA FECHA DE IDA.');
		$('dateesc2').focus();
	}
	else if ( ($F('dateesc2') == $F('dateesc1')) && ($F('dcbhora1') >= $F('dcbhora2')) ){
			alert('LA HORA DE REGRESO DEBE SER MAYOR A LA HORA DE IDA.');
			$('dcbhora2').focus();		
	}
	else if ($F('direcciondestino') ==''){
		alert('INGRESE EL LUGAR DE DESTINO.');
		$('direcciondestino').focus();
	}	
	else
	{
		sw = true;
	}
	
	return sw;
}

 