// ** 	funciones javascript personalizado - 20090120	** //

function validarnumero(e) {

	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8 || tecla ==0) return true; //Tecla de retroceso (para poder borrar)
	patron = /\d/; //ver nota
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

function validarnumtelefono(e) {
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8 || tecla ==0 || tecla ==35 || tecla ==42) return true; //Tecla de retroceso (para poder borrar)
	patron = /\d/; //ver nota
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

function validarletra(e) {

	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla ==8 || tecla ==0) { return true; }

	patron =/[A-Za-z\s]/; //ver nota
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

function ocultardiv(nombrediv){

	//document.getElementById(nombrediv).style.display="none";
}

function comportamientoDiv(opc,nombrediv){

	if(opc=="+"){
		document.getElementById(nombrediv).style.display="block";
	}else{
		document.getElementById(nombrediv).style.display="none";
	}
}

/*    function validarnumero(e){

tecla = (document.all) ? e.keyCode : e.which;
if (tecla==13 || tecla==8) return true; //Tecla de retroceso (para poder borrar)
patron = /\d/; //ver nota
te = String.fromCharCode(tecla);
return patron.test(te);
} */

function coloronFocus(Obj){

	Obj.className = "FormObjOnFocus"
}

function colorOffFocus(Obj){

	Obj.className = "FormObjOffFocus"
}

// redirigir pagina

function reDirigir(url){

	document.location.href=url ;
}

//confirmar respuesta

function confirmaRespuesta(mensaje,destino){

	if(confirm(mensaje))	document.location.href=destino;
}

function coloronFocustxta(Obj){

	Obj.className = "FormObjOnFocustxta"
}

function colorOffFocustxta(Obj){

	Obj.className = "FormObjOffFocustxta"
}

function coloronFocusimg(Obj){

	Obj.className = "fondoimg"
}

// mostrar - ocultar divs

function mostrar_ocualtarDiv(elemento,valor) {

	if(elemento.value=="+") {
		document.getElementById(valor).style.display='block';
		elemento.value="-";

	}else if(elemento.value=="-") {

		document.getElementById(valor).style.display='none';
		elemento.value="+";
	}else {
		alert(elemento+' Error...');
	}
}

function enabledEnterZ(e) {
	var tecla='';
	var patron='';
	//var te='';
	
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla ==13) { return true; }

	patron =/[A-Za-z\s]\d/; //letras y nunmeros
	return patron.test(te);
}

function enabledEnter(e) //nombre de la func. y la variable que se utiliza para arrojar el result.
{
	tecla = (document.all)?e.keyCode:e.which;
	
    if (tecla==8 || tecla==32 || tecla==45 || tecla==0) { return true };

    patron = /\w/;
    te = String.fromCharCode(tecla);
	
    return patron.test(te); 

}

//numero con punto decimal

function numeroDecimal(e) {

	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8 || tecla ==0) return true; //Tecla de retroceso para poder borrar
	if (tecla ==13) { return false; }
	patron = /[\d.]/;// Solo acepta n�meros y el punto

	te = String.fromCharCode(tecla);
	return patron.test(te);
}

//validar email

function isEmail(theElement){

	var s = theElement.value;
 
	//var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
	var filter=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	//if (s.length == 0 ) alert('INGRESE UNA DIRECCION DE EMAIL VALIDO');
	if (filter.test(s) || s.length == 0){
		return true;
	}
	else{
		alert("INGRESE UNA DIRECCION DE EMAIL VALIDO.");
		theElement.value='';
		theElement.focus();
		return false;
	}
}


function Email(theElement){

	var s = theElement.value;
	var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
	//if (s.length == 0 ) alert('INGRESE UNA DIRECCION DE EMAIL VALIDO');
	if (filter.test(s) || s.length == 0){
		return true;
	}
	else{
		return false;
	}
}

function trim(s){
	s = s.replace(/[%$,#&@]/gi,'');
	s = s.replace(/^\s+|\s+$/gi,''); //sacar espacios blanco principio y final

	return s;
}

//actulizar ventana padre
function actualizaPadre(){
	window.opener.document.location.reload(true);
	self.close();
}

//quitar caracteres no deseados
function quitarcaracter(theElement){
	//cadena = reg.Replace(cadena, "([^0-9]|[^a-zA-Z]|-)", "")
	var s = theElement.value;
	validar = s.replace(/\W/g,'')
	if (validar.length == 0){
		theElement.value='';
		return false;
	}
	else{

		if(validar==s)	return false;
		alert('EL CODIDO IDENTIFICADOR SERIA: '+validar.toUpperCase());
		theElement.value=validar.toUpperCase();
	}

}


function isset(variable_name) {
    try {
         if (typeof(eval(variable_name)) != 'undefined')
         if (eval(variable_name) != null)
         return true;
     } catch(e) { }
    return false;
   }


function oNumero(numero)
{
	//Propiedades
	this.valor = numero || 0
	this.dec = -1;
	//M�todos
	this.formato = numFormat;
	this.ponValor = ponValor;
	//Definici�n de los m�todos
	function ponValor(cad)
	{
		if (cad =='-' || cad=='+') return
		if (cad.length ==0) return
		if (cad.indexOf('.') >=0)
		this.valor = parseFloat(cad);
		else
		this.valor = parseInt(cad);
	}
	function numFormat(dec, miles)
	{
		var num = this.valor, signo=3, expr;
		var cad = ""+this.valor;
		var ceros = "", pos, pdec, i;
		for (i=0; i < dec; i++)
		ceros += '0';
		pos = cad.indexOf('.')
		if (pos < 0)
		cad = cad+"."+ceros;
		else
		{
			pdec = cad.length - pos -1;
			if (pdec <= dec)
			{
				for (i=0; i< (dec-pdec); i++)
				cad += '0';
			}
			else
			{
				num = num*Math.pow(10, dec);
				num = Math.round(num);
				num = num/Math.pow(10, dec);
				cad = new String(num);
			}
		}
		pos = cad.indexOf('.')
		if (pos < 0) pos = cad.lentgh
		if (cad.substr(0,1)=='-' || cad.substr(0,1) == '+')
		signo = 4;
		if (miles && pos > signo)
		do{
			expr = /([+-]?\d)(\d{3}[\.\,]\d*)/
			cad.match(expr)
			cad=cad.replace(expr, RegExp.$1+','+RegExp.$2)
		}
		while (cad.indexOf(',') > signo)
		if (dec<0) cad = cad.replace(/\./,'')
		return cad;
	}
}//Fin del objeto oNumero:



/* INICIALIZADOR DEL CALENDARVIEW */
function setupCalendars() {
 
	Calendar.setup(
          {
            dateField: 'date4',
            triggerElement: 'calendarButton4'
          }
        ); 

        Calendar.setup(
          {
            dateField: 'date',
            triggerElement: 'calendarButton'
	    
          }
	  );

	  Calendar.setup(
          {
            dateField: 'date2',
            triggerElement: 'calendarButton2'
          }

        );   

	 Calendar.setup(
          {
            dateField: 'date3',
            triggerElement: 'calendarButton3'
          }
	);

        Calendar.setup(
          {
            dateField: 'datedom',
            triggerElement: 'calendarButtondom'
	    
          }
	  );	
	  
}

function setupCalendars_asist() {

	Calendar.setup(
          {
            dateField: 'date2',
            triggerElement: 'calendarButton2'
          }
        ); 

        Calendar.setup(
          {
            dateField: 'date3',
            triggerElement: 'calendarButton3'
	    
          }
	  );
	  
}

function setupCalendars_admin() {

	Calendar.setup(
          {
            dateField: 'date2',
            triggerElement: 'calendarButton2'
          }
        ); 

        
	  
}

function setupCalendars_seguridad() {

	Calendar.setup(
          {
            dateField: 'date2',
            triggerElement: 'calendarButton2'
          }
        ); 

        Calendar.setup(
          {
            dateField: 'date3',
            triggerElement: 'calendarButton3'
	    
          }
	  );
	  
}

function setupCalendars2() {

	Calendar.setup(
          {
            dateField: 'date5',
            triggerElement: 'calendarButton5'
          }
        ); 

        Calendar.setup(
          {
            dateField: 'date6',
            triggerElement: 'calendarButton6'
	    
          }
	  );
	  
}



function validarTxt(theElement){

	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla ==13) { return false; }

	patron =/^[\w\s\-]+$/i; 
	return patron.test(te);
}


function llamada_asistencia(numero,extension,etapa,asistencia){
//	alert(numero);
	new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',
	{
		method : 'get',
		parameters: {
			num: numero,
			ext: extension,
			idetapa: etapa,
			idasistencia: asistencia
			
		},
		onSuccess: function(t){
//			alert(t.responseText);
		}
	}
	);
	return;
}


