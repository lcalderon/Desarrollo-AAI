function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}
  
//visualizar Asistencias

function obtnerdato(base,campo,clave){

	resul = document.getElementById('resultado_formato');
	
	//producto=document.getElementById('cmbplanes').value;

 	ajax=objetoAjax();
	ajax.open("POST", "/app/vista/catalogos/layout/operacion.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			resul.innerHTML = ajax.responseText;

		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("base="+base+"&campo="+campo+"&clave="+clave);

}

function grabardato(cuenta,base,campo){

	resul = document.getElementById('resultado_formato');
	prefijo=document.getElementById('txtprefijo').value;
	sufijo=document.getElementById('txtsufijo').value;

 	ajax=objetoAjax();
	ajax.open("POST", "/app/vista/catalogos/layout/grabar.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			resul.innerHTML = ajax.responseText;

		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("cuenta="+cuenta+"&base="+base+"&campo="+campo+"&prefijo="+prefijo+"&sufijo="+sufijo);

}
