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

function recorre(){
	//donde se mostrar� los registros
	divContenido = document.getElementById('opcion');
	
	document.getElementById('hidvar').value=1;
	
	//var1=varid+1;
	
	
	ajax=objetoAjax();
		
	//uso del metodo GET
	//indicamos el archivo que realizar� el proceso de paginar
	//junto con un valor que representa el nro de pagina
	ajax.open("POST", "/app/vista/asistencia/asignacion/proveedor_orden_asignacion.php",true);
	//divContenido.innerHTML= '<img src="anim.gif">';
	
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			
			divContenido.innerHTML = ajax.responseText
			
		}
	}
	//como hacemos uso del metodo GET
	//colocamos null ya que enviamos 
	//el valor por la url ?pag=nropagina
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("hidvar="+varid);
}