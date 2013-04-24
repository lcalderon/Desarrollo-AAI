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
 
//VALIDAR ACCESO DE USUARIOS

function accesousuario(){
	//donde se mostrará el resultado
	divResultado = document.getElementById('resultado');

	usuario=document.getElementById('ususesion').value;
	passactual=document.getElementById('txtpassactual').value;

	//instanciamos el objetoAjax
	ajax=objetoAjax();
	//usamos el medoto POST
	//archivo que realizará la operacion
	//datoscliente.php
	
	ajax.open("POST", "../../app/vista/login/verficarusuario.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa

			if(ajax.responseText==0){
				divResultado.innerHTML ="Contrasenia actual Incorrecto.";
				document.getElementById('txtpassactual').value="";
				document.getElementById('txtpassactual').focus();
			}
			else{		
				
				divResultado.innerHTML = ""
			}
		}
	}
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("usuario="+usuario+"&passactual="+passactual);
}

//MOSTRAR FORMULARIO SIAC
function showFormulario(opc,cuenta,programa,valors){
	//donde se mostrar? el formulario con los datos
	divFormulario = document.getElementById('tipogestion');

	//instanciamos el objetoAjax
	ajax=objetoAjax();
	//uso del medotod POST
	ajax.open("POST", "tipogestion.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText
			//mostrar el formulario
			divFormulario.style.display="block";
			//document.getElementById('btnaceptar').style.backgroundColor='#a7c6eb';
		
			if(valors){ 
				
				document.getElementById('btncerrar').disabled=true;
				document.getElementById('btnaceptar').disabled=true;

			}
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("opc="+opc+"&cuenta="+cuenta+"&programa="+programa);
}

function showPlantilla(opc,idcosto){
	//donde se mostrará el formulario con los datos
	divFormulario = document.getElementById('resultado');

	//instanciamos el objetoAjax
	ajax=objetoAjax();

	//uso del medotod POST
	ajax.open("POST", "plantillas.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			//alert(id);
			divFormulario.innerHTML = ajax.responseText
			//mostrar el formulario
			divFormulario.style.display="block";			
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idcosto="+idcosto+"&opc="+opc);
}

function showCostos(idfamilia,idservicio){
	//donde se mostrará el formulario con los datos
	divFormulario = document.getElementById('resultado');

	//instanciamos el objetoAjax
	ajax=objetoAjax();

	//uso del medotod POST
	ajax.open("POST", "vercosto.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			//alert(id);
			
			divFormulario.innerHTML = ajax.responseText
			//mostrar el formulario
			divFormulario.style.display="block";			
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idfamilia="+idfamilia+"&idservicio="+idservicio);
}

// combo canal de venta

function showCombo(idcanal){
	//donde se mostrará el formulario con los datos
	divFormulario = document.getElementById('resultado');

	//instanciamos el objetoAjax
	ajax=objetoAjax();
 
	//uso del medotod POST
	ajax.open("POST", "datocanal.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa

			divFormulario.innerHTML = ajax.responseText
		 
			//mostrar el formulario
			divFormulario.style.display="block";	
			//	
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idcanal="+idcanal);
}

// mostrar formulario negociado

function show_info(negociado,direccion,idcosto){
	//donde se mostrará el formulario con los datos
	divFormulario = document.getElementById('verdatonegociado');

	//instanciamos el objetoAjax
	ajax=objetoAjax();
 
	//uso del medotod POST
	ajax.open("POST",direccion);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText
			divFormulario.style.display="block";
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("negociado="+negociado+"&idcosto="+idcosto);
}

//mostar divs contenidos

function show_divs(direccion,idcodigo,nombrediv,opc1,opc2){
	
	//donde se mostrará el formulario con los datos
	divFormulario = document.getElementById(nombrediv);

	//instanciamos el objetoAjax
	ajax=objetoAjax();
	//alert(idcodigo);
	//uso del medotod POST
	ajax.open("POST",direccion);
	ajax.onreadystatechange=function() {

	//alert(ajax.readyState);
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText
			divFormulario.style.display="block";
			 
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idcodigo="+idcodigo+"&opc1="+opc1+"&opc2="+opc2);
}

 
