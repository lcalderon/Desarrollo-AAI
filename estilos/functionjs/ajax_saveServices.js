// funciones para grabar los diferentes servicios - 20090212

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

// Grabando nuevos tipos de servicios 
//plomeria

function grabarNewService(){
	
	contenedor = document.getElementById('resultadoid');
	
	idexped=document.getElementById('txtidexped').value;
	idserv=document.getElementById('txtidserv').value;
	usuario=document.getElementById('txtusuario').value;
		//alert(usuario);
	cmblocalf=document.getElementById('cmblocalf').value;
	cmbtiposervicio=document.getElementById('cmbtiposervicio').value;
	idprog=document.getElementById('txtprog').value;
	neventos=document.getElementById('neventos').value;
	
	txtareaubicacion=document.getElementById('txtareaubicacion').value;
	cmbubicadoen=document.getElementById('cmbubicadoen').value;
	txtareadescripcion=document.getElementById('txtareadescripcion').value;
	txtareadetalle=document.getElementById('txtareadetalle').value;	
	txtareasolucion=document.getElementById('txtareasolucion').value;
	condicion=document.getElementById('codexped').value;
 
		if(document.getElementById('cmbubicadoen').value ==''){
			alert('Seleccione la Ubicacion.');
			document.getElementById('cmbubicadoen').focus();
			return (false);
		}
		else if(document.getElementById('cmblocalf').value ==''){
			alert('Foraneo o Local?.');
			document.getElementById('cmblocalf').focus();
			return (false);
		}
//alert(idprog+idserv+idexped);
	ajax=objetoAjax();
	ajax.open("POST", "/soaa_ng/app/vista/catalogos/selecionServicios/g_servPlomeria_001.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){

			datos=ajax.responseText;
			
			respserv = datos.split('-');
			numero='';
 
			if(condicion=='' && respserv[1] == neventos && neventos != 0){
				alert('No fue registrado.\nYa fue asignado el maximo de servicios x eventos ['+neventos+']');
				document.getElementById('btnproveedor').disabled=true;
				document.getElementById('btnasignarc').disabled=true;	
			}
			else if(respserv[0] !='' && respserv[0]!=0 ){
				if(condicion=='')	alert('Se registro el servicio satisfactoriamente.');	else alert('Se actualizo los cambios.');
				numero=respserv[0];
			
				document.getElementById('btngrabar').value  ="Modificar Cambios";	
				document.getElementById('codexped').value=numero;	
				//document.getElementById('btngrabar').disabled=true;	
				document.getElementById('btnproveedor').disabled=false;
				document.getElementById('btnasignarc').disabled=false;				
			}
			else{
			
				alert('Hubo un error verificar.');
				
			}
			
			contenedor.innerHTML = "<b>" + numero + "</b>";
			contenedor.style.background= '#FFFFCC';
			
			document.getElementById('txt1').value=numero;							
			document.getElementById('ifr_listado').src="/soaa_ng/app/vista/catalogos/selecionServicios/servicios_listado_proveedores.php?idserv="+idserv+"&idexped="+idexped+"&idasist="+numero;
				
		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("idexped="+idexped+"&idserv="+idserv+"&cmblocalf="+cmblocalf+"&cmbtiposervicio="+cmbtiposervicio+"&idprog="+idprog+"&txtareaubicacion="+txtareaubicacion+"&cmbubicadoen="+cmbubicadoen+"&txtareadescripcion="+txtareadescripcion+"&txtareadetalle="+txtareadetalle+"&txtareasolucion="+txtareasolucion+"&usuario="+usuario+"&condicion="+condicion);
	
}


function CargaPrincipal(){
idexped=document.getElementById('txtidexped').value;
idserv=document.getElementById('txtidserv').value;
xtxt1 = document.getElementById('txt1').value;

if(xtxt1 != ''){
		document.getElementById('ifr_listado').src="/soaa_ng/app/vista/catalogos/selecionServicios/servicios_listado_proveedores.php?idserv=idserv&idexped=idexped"
	}
}
// grabando servicio remolque de grua. 

function grabarNewServiceAutomovil(){
	
	contenedor = document.getElementById('resultadoid');
	
	idserv=document.getElementById('txtidservremol').value;
	idexped=document.getElementById('txtidexped').value;
	usuarioserv=document.getElementById('txtusuario').value;	
	cmblocalf=document.getElementById('cmblocalf').value;
	cmbtiposervicio=document.getElementById('cmbtiposervicio').value;
	idprograma=document.getElementById('txtprog').value;
	cmbservsol=document.getElementById('cmbservsol').value;
	txtdescserv=document.getElementById('txtdescserv').value;
	marca=document.getElementById('txtmarca').value;
	txtmodelo=document.getElementById('txtmodelo').value;
	txtcolor=document.getElementById('txtcolor').value;
	txtpatente=document.getElementById('txtpatente').value;
	txtanio=document.getElementById('txtanio').value;
	txtserie=document.getElementById('txtserie').value;
	txtmotor=document.getElementById('txtmotor').value;
	txtalugart=document.getElementById('txtalugart').value;
	neventos=document.getElementById('neventos').value;

		if(document.getElementById('cmbservsol').value =='' ){
			alert('Seleccione alguna opcion.');
			document.getElementById('cmbservsol').focus();
			return (false);
		}
		else if(document.getElementById('cmblocalf').value =='' ){
			alert('Foraneo o Local?.');
			document.getElementById('cmblocalf').focus();
			return (false);
		}
			
	ajax=objetoAjax();
	ajax.open("POST", "/soaa_ng/app/vista/catalogos/selecionServicios/g_servAutomovil_003.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){		
			

			datos=ajax.responseText;
			
			respserv = datos.split('-');
			numero='';

			if(respserv[1] == neventos && neventos != '' ){
				alert('No fue registrado.\nYa fue asignado el maximo de servicios x eventos ['+neventos+']');
				document.getElementById('btnproveedor').disabled=true;
				document.getElementById('btnasignarc').disabled=true;	
			}
			else if(respserv[0] != ''){
				alert('Se registro el servicio satisfactoriamente.');
				numero=respserv[0];
			
				//document.getElementById('btngrabar').disabled=true;	
				document.getElementById('btnproveedor').disabled=false;
				document.getElementById('btnasignarc').disabled=false;				
			}
			else{
				alert('Hubo un error verificar.');
			}
							
			contenedor.innerHTML = "<b>" + numero + "</b>";
			contenedor.style.background= '#FFFFCC';
			
			document.getElementById('txt1').value=numero;							
			document.getElementById('ifr_listado').src="/soaa_ng/app/vista/catalogos/selecionServicios/servicios_listado_proveedores.php?idserv="+idserv+"&idexped="+idexped+"&idasist="+numero;
		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("idserv="+idserv+"&idexped="+idexped+"&marca="+marca+"&usuarioserv="+usuarioserv+"&cmblocalf="+cmblocalf+"&cmbtiposervicio="+cmbtiposervicio+"&idprograma="+idprograma+"&cmbservsol="+cmbservsol+"&txtdescserv="+txtdescserv+"&txtmodelo="+txtmodelo+"&txtcolor="+txtcolor+"&txtpatente="+txtpatente+"&txtanio="+txtanio+"&txtserie="+txtserie+"&txtmotor="+txtmotor); 
	//ajax.send("txtidserv="+idserv); 

} 

// grabando servicio remolque de grua. 

function grabarNewAuxulioVial(){
	
	contenedor = document.getElementById('resultadoid');
	
	//idserv=document.getElementById('txtidservremol').value;
	//idexped=document.getElementById('txtidexped').value;
	neventos=document.getElementById('neventos').value;

		if(document.getElementById('cmbservsol').value =='' ){
			alert('Seleccione alguna opcion.');
			document.getElementById('cmbservsol').focus();
			return (false);
		}
		else if(document.getElementById('cmblocalf').value =='' ){
			alert('Foraneo o Local?.');
			document.getElementById('cmblocalf').focus();
			return (false);
		}
			
			alert('ok');
	ajax=objetoAjax();
	ajax.open("POST", "/soaa_ng/app/vista/catalogos/selecionServicios/g_servAutomovil_003.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){		
			

			datos=ajax.responseText;
			
			respserv = datos.split('-');
			numero='';

			if(respserv[1] == neventos && neventos != '' ){
				alert('No fue registrado.\nYa fue asignado el maximo de servicios x eventos ['+neventos+']');
				document.getElementById('btnproveedor').disabled=true;
				document.getElementById('btnasignarc').disabled=true;	
			}
			else if(respserv[0] != ''){
				alert('Se registro el servicio satisfactoriamente.');
				numero=respserv[0];
			
				//document.getElementById('btnproveedor').disabled=false;
				//document.getElementById('btngrabar').disabled=true;				
			}
			else{
				alert('Hubo un error verificar.');
			}
							
			contenedor.innerHTML = "<b>" + numero + "</b>";
			contenedor.style.background= '#FFFFCC'
			
			document.getElementById('txt1').value=numero;							
			document.getElementById('ifr_listado').src="/soaa_ng/app/vista/catalogos/selecionServicios/servicios_listado_proveedores.php?idserv="+idserv+"&idexped="+idexped+"&idasist="+numero;
		}
	}
	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("idserv="+idserv+"&idexped="+idexped+"&marca="+marca+"&usuarioserv="+usuarioserv+"&cmblocalf="+cmblocalf+"&cmbtiposervicio="+cmbtiposervicio+"&idprograma="+idprograma+"&cmbservsol="+cmbservsol+"&txtdescserv="+txtdescserv+"&txtmodelo="+txtmodelo+"&txtcolor="+txtcolor+"&txtpatente="+txtpatente+"&txtanio="+txtanio+"&txtserie="+txtserie+"&txtmotor="+txtmotor); 
	//ajax.send("txtidserv="+idserv); 

} 

function OrdenaAsignacion(){
	resul = document.getElementById('resultado');
	orden = document.getElementById('cborden').value;
	exped = document.getElementById('CVEEXPED').value;
	asist = document.getElementById('NUMASIST').value;
	servicio = document.getElementById('cveservicio').value;
	lat = document.getElementById('latitud').value;
	lon = document.getElementById('longitud').value;
	ent1 = document.getElementById('cveentidad1').value;
	ent2 = document.getElementById('cveentidad2').value;
	ent3 = document.getElementById('cveentidad3').value;
	
	if(orden =='sel' ){
		xorden = ' VISUALIZAR ASC,P.INTERNO DESC, P.CDE ASC, DISTANCIA ASC';		
	}
	else if(orden =='distancia' ){	
		xorden = ' DISTANCIA ASC';		
	}
	else if(orden =='cde' ){	
		xorden = ' P.CDE ASC';		
	}		
			
			
	ajax=objetoAjax();
	ajax.open("POST", "/soaa_ng/app/vista/catalogos/selecionServicios/consulta_asignacion.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			resul.innerHTML = ajax.responseText
		}
	}
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	ajax.send("expediente="+exped+"&asistencia="+asist+"&servicio="+servicio+"&orden="+xorden+"&latitud="+lat+"&longitud="+lon+"&entidad1="+ent1+"&entidad2="+ent2+"&entidad3="+ent3);
}