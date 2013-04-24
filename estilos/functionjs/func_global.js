//cargar div 
	function mostrarDiv(nombreDiv,ruta,cuenta,plan,servicio,afiliado,expediente,opc1){

		$(nombreDiv).style.display = 'block';

			new Ajax.Updater(nombreDiv, ruta,{
				parameters: { 						
						cuenta: cuenta,
						plan: plan,
						idserv: servicio,
						idafiliado: afiliado,
						idexpediente: expediente,
						verifica: opc1
					},
				evalScripts:true,
				method: 'post'
			});
	}
	
//mostrar contrato

	var validar_func = '';
	var win = null;		
		
	function presentar_formulario(refresh,ruta,mensaje,titulo,ancho,alto,sModal,expediente,asistencia,cuenta,plan,opcion,idetapa,idafiliado,tipoclass){

		if (win != null){
			
			alert(mensaje);
			
		} else{
			
			if(!tipoclass || tipoclass =='') tipoclass='alphacube';
			
			win = new Window({
				className: tipoclass,
				title: titulo,
				width: ancho,
				height: alto,
				showEffect: Element.show,
				hideEffect: Element.hide,
				showModal  : sModal,
				destroyOnClose: true,
				minimizable: false,
				maximizable: false,
				resizable: true,
				opacity: 0.95,
				url: ruta+'?expediente='+expediente+'&asistencia='+asistencia+'&cuenta='+cuenta+'&plan='+plan+'&opcion='+opcion+'&etapa='+idetapa+'&idafiliado='+idafiliado
			});

			win.showCenter(sModal);
			myObserver = {onDestroy: function(eventName, win1)
			{
				if(refresh ==1) window.location.reload();
				if (win1 == win) {
					win = null;
					Windows.removeObserver(this);
				}
			}
			}
			
			Windows.addObserver(myObserver);
			WindowCloseKey.init();
		}

			return;	 
	}
	
//marcar todos los checks	
	function marcar_todasCks(todos,idcmb,idcheck){

		var Lista=document.getElementById(idcmb); 

		var Arreglo = $A(Lista);
		if(todos =='1')	$(idcheck).checked=true;

		if ($(idcheck).checked || todos=='1')
		Arreglo.each(function(el, indice){
			
			el.selected=true;
		});
		else
		Arreglo.each(function(el, indice){
			el.selected=false;
		});
		return;
	}
			 	
//desactivar cuenta combo lita
	function desactiva_check(id){
		$(id).checked=false;
		return;
	}
	
//establecer formato	
	function coloronFocus(Obj){

		Obj.className = "FormObjOnFocus"
	}

	function colorOffFocus(Obj){

		Obj.className = "FormObjOffFocus"
	}
	
//validar cambio de password de usuario
	function validarPassword(msj){
		//donde se mostrará el resultado
		divResultado = document.getElementById('resultado');

		usuario=document.getElementById('ususesion').value;
		passactual=document.getElementById('txtpassactual').value;

		//instanciamos el objetoAjax
		ajax=objetoAjax();
		
		//usamos el medoto POST		
		ajax.open("POST", "../../app/vista/login/verficarusuario.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				//mostrar resultados en esta capa

				if(ajax.responseText==0){
					divResultado.innerHTML =msj;
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
	
	
//validar cambio de password de usuario
	function ocultarVisualizarDiv(idDiv1,idDiv2){

		if($(idDiv1).style.display=='none'){
	 
			$(idDiv1).style.display='block';
			$(idDiv2).style.display='none';
	 
		}/*  else{
	 
			$(idDiv1).style.display='none';  
			$(idDiv2).style.display='block'; 
		} */
	}

//redireccionar page
	function reDirigir(url){

		document.location.href=url;
	}

//mensaje de confirmacion	
	function confirmaRespuesta(mensaje,destino){

		if(confirm(mensaje))	document.location.href=destino;
	}
	
 	function comportamientoDiv(opc,nombrediv){

		if(opc=="+"){
			document.getElementById(nombrediv).style.display="block";
		}else{
			document.getElementById(nombrediv).style.display="none";
		}
	}

//mostrar-ocultar div	
	function mostrar_ocualtarDiv(elemento,valor){

		if(elemento.value=="+" || elemento.value=="V") {
			document.getElementById(valor).style.display='block';
			if(elemento.value=="+") elemento.value='-';
			if(elemento.value=="V") elemento.value='O';

		}else if(elemento.value=="-" || elemento.value =='O') {

			document.getElementById(valor).style.display='none';
			if(elemento.value=="-") elemento.value='+';
			if(elemento.value=='O') elemento.value='V';
		}else {
			alert(elemento+' Error...');
		}
	}	
		
//actulizar ventana padre
	function actualizaPadre(){
		window.opener.location.reload();
		self.close();
	}
	
	
	
	function deshabiltarEnter(e){

		tecla = (document.all) ? e.keyCode : e.which;
		if(tecla ==13) paginar(1);
		if(tecla==13) return false; //Tecla de retroceso (para poder borrar)
		
	}

//validar numero
function validarnumero(e) {

	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8 || tecla ==0) return true; //Tecla de retroceso (para poder borrar)
	patron = /\d/; //ver nota
	te = String.fromCharCode(tecla);
	return patron.test(te);
}	

//eliminar espacios todos

	function clear_all(obj) {
		
		$(obj).value =($(obj).value).replace(/\s/gi,"");		
	}
				 		