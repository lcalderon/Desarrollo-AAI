<?
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once('../../../modelo/functions.php');
 
	$con= new DB_mysqli();
	 
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	Auth::required($_SERVER['REQUEST_URI']);

	//validar_permisos("MENU_TRANSFER",1);
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

	$anio=mostrar_Anio(10);		
	
 	if($_POST["btnexportar"])	include_once("excel_reporte_audito.php");

	$datomodelo=$con->consultation("select IDMODELO,NOMBRE from $con->catalogo.catalogo_modeloplantilla_informe where IDMODELO='".$_GET["idmodelo"]."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_("REPORTE DE CALIDAD")?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">
	@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");
	</style> 
	
	<!-- libreria prototype -->
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>
	

	
	 
	<script type="text/javascript">
		
		function verificardiv(nombrediv,valors,nombre){
	 
			if(valors=='V'){
				comportamientoDiv('+',nombrediv);
				document.getElementById(nombre).value='O';
			}else{
				comportamientoDiv('-',nombrediv);
				document.getElementById(nombre).value='V';	
			}
		 }

		function seleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox")	
					document.form1.elements[i].checked=1
		}
		
		function deseleccionar_todo(){
			for (i=0;i<document.form1.elements.length;i++)
				if(document.form1.elements[i].type == "checkbox"  && document.form1.elements[i].disabled==false)	
					document.form1.elements[i].checked=0
		}
		
		function mostrardata(idcodigo){
				
				$('btnmostrar').disabled=false;
				$('btnexportar').disabled=false;

				new Ajax.Updater('div-informe', 'vista_transferencia.php', {
				  parameters: { cmbmodelo: idcodigo },
				  method: 'post',
				  onSuccess: function(t)
					{ 
					 
						document.form1.txtnombre.value=document.form1.cmbmodelo.options[document.form1.cmbmodelo.selectedIndex].text;
						$('btngrabar').disabled=false;
					
					}
				});					
	 
		}

		function validarCampo(valor){
			var nombre=0;
				for (i=0; i<=document.form1.cmbmodelo.length-1; i++) {
				 	if(document.form1.txtnombre.value==document.form1.cmbmodelo.options[i].text)	nombre=1;			 
				}
		 	document.form1.txtnombre.value=document.form1.txtnombre.value.replace(/^\s*|\s*$/g,"");	
			
			if(document.form1.txtnombre.value=="")
			{	 
				alert('<?=_("INGRESE EL NOMBRE DE LA PLANTILLA.") ;?>');
				document.form1.txtnombre.focus();
				 return false;
			}
			
			var filter=/^[\w\-Ñ\s]+$/i;
			
			if(!filter.test(document.form1.txtnombre.value)) {
				alert('<?=_("EL NOMBRE DE LA PLANTILLA NO ES VALIDO.") ;?>');
				document.form1.txtnombre.focus();
				return false;
			}
			
			if(nombre==1)
			{
				if(confirm('<?=_("DESEA SOBREESCRIBIR EL NOMBRE?.") ;?>'))
			    {
					document.form1.action='gtransferencia.php?valor='+valor;
					document.form1.submit();
			    }			
			}
			else
			{
				document.form1.action='gtransferencia.php?valor='+valor;
				document.form1.submit();
			}
		}
		
		function eliminarplantilla(valor){
		
			if(confirm('<?=_("DESEA ELIMINAR EL REGISTRO?.") ;?>'))
			{
				document.form1.action='gtransferencia.php?valor='+valor;
				document.form1.submit();
			}
		   
			return false;			
		}
		
		
		function marcar_todascuentas(){
		
			var Lista=document.getElementById('cmbcuenta[]');
			var Arreglo = $A(Lista);

			if ($('ckbtodocuenta').checked)
			Arreglo.each(function(el, indice){
				el.selected=true;
			});
			else
			Arreglo.each(function(el, indice){
				el.selected=false;
			});
			return;
		}
		
		function desactiva_check(){
			$('ckbtodocuenta').checked=false;
			return;
		}		
 		

		
		
	function serialize(form) {
		
		  if (!form || !form.elements) return;
		 
		  var serial = [], i, j, first;
		  var add = function (name, value) {
			serial.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
		  }
		 
		  var elems = form.elements;
		  for (i = 0; i < elems.length; i += 1, first = false) {
			if (elems[i].name.length > 0) { /* no incluye los elementos sin nombre */
			  switch (elems[i].type) {
				case 'select-one': first = true;
				case 'select-multiple':
				  for (j = 0; j < elems[i].options.length; j += 1)
					if (elems[i].options[j].selected) {
					  add(elems[i].name, elems[i].options[j].value);
					  if (first) break; /* detiene la búsqueda para  select-one */
					}
				  break;
				case 'checkbox':
				case 'radio': if (!elems[i].checked) break; /* sino continúa */
				default: add(elems[i].name, elems[i].value); break;
			  }
			}
		  }
		 
	  return serial.join('&');
	}
 



function exportar_excel(){

	var valors =  serialize($('form1'));  
	crear_reporte(valors)
	//window.open('exportar_transferencia.php?'+valors,'mywindow','width=400,height=200')
	
}		
		</script>

</head>

<body onload="document.getElementById('ckbtodocuenta').checked=true;marcar_todascuentas()">
 
      <form id="form1" name="form1" method="post" action="excel_reporte_audito.php" >
	  
        <table width="80%" border="0" cellpadding="1" cellspacing="1" bgcolor="#EAF4FF" style="border:1px dashed #333366">
			 <caption><?=_("REPORTE DE AUDITORIA") ;?></caption>
          <tr>
            <td width="180"><label>
              <input type="radio" name="rdopc" id="radio" value="1" checked onclick="comportamientoDiv('+','opc1');comportamientoDiv('-','opc2')" />
            <?=_("POR MES/ANIO") ;?></label></td>
          </tr>
          <tr>
            <td><div id='opc1' ><table width="200" border="0" align="center" cellpadding="1" cellspacing="1">
              <tr>
                <td><?=_("Mes") ;?></td>
                <td><?
				$con->cmb_array("cmbmes",$mes_del_anio,date("m"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","1");
		?></td>
                <td><?=_("A&ntilde;o") ;?></td>
                <td><?
				$con->cmb_array("cmbanio",$anio,($_POST["cmbanio"])?$_POST["cmbanio"]:date("Y"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
		?></td>
              </tr>
              
            </table> </div>  </td>
          </tr>
          <tr>
            <td><input type="radio" name="rdopc" id="radio2" value="2"  onclick="comportamientoDiv('-','opc1');comportamientoDiv('+','opc2')"  /><?=_("POR PERIODO DE FECHAS") ;?></td>
          </tr>
          <tr>
            <td><div id='opc2' style='display:none'><table width="309" border="0" align="center" cellpadding="1" cellspacing="1">
              <tr>
                <td width="141"><?=_("De") ;?>
                  <input name="fechaini" type="text" class="classtexto"  id="fechaini" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
                  <button   id="cal-button-1">...</button>
                  <script type="text/javascript">
					Calendar.setup({
					  inputField    : "fechaini",
					  button        : "cal-button-1",
					  align         : "Tr"
					});
				  </script></td>
                <td width="152"><?=_("al") ;?>
                  <input name="fechafin" type="text" class="classtexto" id="fechafin" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
                  <button   id="cal-button-2">...</button>
                  <script type="text/javascript">
						Calendar.setup({
						  inputField    : "fechafin",
						  button        : "cal-button-2",
						  align         : "Tr"
						});
					 </script></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;&nbsp;<?=_("CUENTAS") ;?>         
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-------------------->&nbsp;<?=_("TODOS") ;?>
            <input name="ckbtodocuenta" type="checkbox" id="ckbtodocuenta" value="1" onclick="marcar_todascuentas()" title="<?=_("TODOS LAS CUENTAS") ;?> " />
            </td>
          </tr>
          <tr>
            <td>&nbsp;&nbsp;<?
					if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
					$con->cmbselectdata($sql,"cmbcuenta[]","","onclick='desactiva_check()' size='7' multiple class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' ","2");	
				?>
                <div align="right">
                <input type="submit" name="btnexportar" id="btnexportar" value="&gt;&gt;&gt; EXPORTAR" style="text-align:center;font-weight:bold;font-size:10px;height:25px;"/>
              </div>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table> 		
 
 
		 
      </form>
 
</body>
</html>

	<script type="text/javascript">
 
	function crear_reporte(valores){
		 Dialog.alert({url: "crearReporte.php?"+valores, options: {method: 'get'}}, {className: "alphacube", width:540, okLabel: "Close"});

		}

	</script>