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
 
//order columnas

function OrdenarPor(campo, orden,posicion){
	//especificamos en div donde se mostrar el resultado
	divListado = document.getElementById('resultado');
	ajax=objetoAjax();
	//especificamos el archivo que realizar� el listado
	//y enviamos las dos variables: campo y orden
	ajax.open("GET", "general.php?campo="+campo+"&orden="+orden+"&pag="+posicion);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divListado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null);	
}

//mostar resultado x pagina

function Pagina(nropagina){
	//donde se mostrar� los registros
	divContenido = document.getElementById('resultado');
	
	ajax=objetoAjax();
	//uso del medoto GET
	//indicamos el archivo que realizar el proceso de paginar
	//junto con un valor que representa el nro de pagina
	ajax.open("GET", "general.php?pag="+nropagina);

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divContenido.innerHTML = ajax.responseText
		}
	}
	//como hacemos uso del metodo GET
	//colocamos null ya que enviamos 
	//el valor por la url ?pag=nropagina
	ajax.send(null);
}

//mostrar tipo de formulario agregar/editar servicios

function showFormulario(idprograma,idserv,opc,opc2){
	//donde se mostrar el formulario con los datos
	divFormulario = document.getElementById('bloque');

	//instanciamos el objetoAjax
	ajax=objetoAjax();
	//alert(idprograma);
	//uso del medotod POST
	ajax.open("POST", "frmdiv_editar.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText
		
			//mostrar el formulario
			divFormulario.style.display="block";
			//if(opc)	document.frmeditar.txtmonto.focus(); else document.frmagrega.txtmonto.focus();
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idprograma="+idprograma+"&idserv="+idserv+"&opc="+opc+"&opc2="+opc2);
}

//mostrar beneficiarios

function showBeneficiario(idpro,idserv){
	//donde se mostrar� el formulario con los datos
	divFormulario = document.getElementById('beneficiario');

	//instanciamos el objetoAjax
	ajax=objetoAjax();

	//uso del medotod POST
	ajax.open("POST", "beneficiarios.php");
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
	ajax.send("idpro="+idpro+"&idserv="+idserv);
}


// AGREGAR O QUITAR BENEFICIARIOS
function activarBeneficiarios(idpro,idserv,opc){

//donde se mostrar el formulario con los datos
	divFormulario = document.getElementById('beneficiario');
	
	var checkboxes = document.getElementById("form1").chkbeneficiario; //Array que contiene los checkbox

	var cantidad = 0;
	var valor = "";
	var valorfinal = "";

	for (var x=0; x < checkboxes.length; x++) {
	   if (checkboxes[x].checked){
		
		cantidad = cantidad + 1;
		valor = valor + checkboxes[x].value+',';
		
		}
	}
	//var longstring="Most of the time Amrit is confused � OK, not most of the time";
	//var brokenstring=longstring.split(' '); 

	valorfinal=valor.substring(0,valor.length-1);
 
	//instanciamos el objetoAjax
	ajax=objetoAjax();
 
	//uso del medotod POST
	ajax.open("POST", "g_beneficiarios.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {

			alert('Se aplicaron los cambios.');
			divFormulario.style.display="none";			
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("valor="+valorfinal+"&idpro="+idpro+"&idserv="+idserv+"&opc="+opc);
}

// enviar email


function enviarEmail(programa){

	ajax=objetoAjax();

	ajax.open("POST", "enviaremail.php");

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//if(ajax.responseText==1)	alert('EMAILS ENVIADOS.');	else alert('HUBO UN PROBLEMA, NO SE ENVIARON LOS EMAILS.');			
			alert(ajax.responseText);	
		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("programa="+programa);
}


/* function MostroFrmCosto(familia,servicio){ 

	//donde se mostrar el formulario con los datos
	divFormulario = document.getElementById('resultado');
	//instanciamos el objetoAjax
	ajax=objetoAjax();
	
	//uso del medotod POST
	ajax.open("POST", "frmcosto.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText
		
			//mostrar el formulario
			if(familia)	divFormulario.style.display="block";	else	divFormulario.innerHTML="";
			//if(opc)	document.frmeditar.txtmonto.focus(); else document.frmagrega.txtmonto.focus();
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("familia="+familia+"&servicio="+servicio);
} */

//
function logConformidad(idprograma,idusuario,clave){ 

	//donde se mostrar el formulario con los datos
	divFormulario = document.getElementById('resultado');
	//instanciamos el objetoAjax
	ajax=objetoAjax();

	//uso del medotod POST
	ajax.open("POST", "/app/vista/catalogos/programas/mostrarlog.php");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			//mostrar resultados en esta capa
			divFormulario.innerHTML = ajax.responseText;
		}
	}

	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores
	ajax.send("idprograma="+idprograma+"&idusuario="+idusuario+"&clave="+clave);
}



function prueba1(){
 
 alert('111');
	ajax=objetoAjax();

	divFormulario = document.getElementById('cargando');
	
	ajax.open("POST", "prueba3.php");
	
	ajax.setRequestHeader("Content-type","application/x-download");
	ajax.setRequestHeader("Content-type","application/vnd.ms-excel");
	ajax.setRequestHeader("Content-Disposition", "attachment;filename='aaa.xls'");
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
 
			 alert('222222');
			// divFormulario.innerHTML = ajax.responseText;
 
			 
		}
	}

	//ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");




	ajax.send("oka=1");
}
