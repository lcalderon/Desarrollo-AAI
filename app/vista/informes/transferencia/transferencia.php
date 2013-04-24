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

	validar_permisos("MENU_TRANSFER",1);
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$anio=mostrar_Anio(10);		
	
 	if($_POST["btnexportar"])	include_once("exportar_transferencia.php");

	$datomodelo=$con->consultation("select IDMODELO,NOMBRE from $con->catalogo.catalogo_modeloplantilla_informe where IDMODELO='".$_GET["idmodelo"]."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Trasnferencia</title>
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
			if($('ckbtodocuenta').checked ==1) valor=1; else valor=0;
			
			for (i=0;i<document.form1.elements.length;i++){
				if(document.form1.elements[i].type == "checkbox")
					document.form1.elements[i].checked=1
			}
			
			$('ckbtodocuenta').checked=valor;
			
		}
		
		function deseleccionar_todo(){
			
			if($('ckbtodocuenta').checked ==1) valor=1; else valor=0;
			
			for (i=0;i<document.form1.elements.length;i++){
				if(document.form1.elements[i].type == "checkbox"  && document.form1.elements[i].disabled==false)	
					document.form1.elements[i].checked=0
			}
			
			$('ckbtodocuenta').checked=valor;
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
				  //if(document.getElementById('ckbtodocuenta').checked == false){;
					  for (j = 0; j < elems[i].options.length; j += 1)
						if (elems[i].options[j].selected) {
					 
						if(elems[i].name != 'cmbcuenta[]' && document.getElementById('ckbtodocuenta').checked == true){
						
						  add(elems[i].name, elems[i].options[j].value);
						  } else if(document.getElementById('ckbtodocuenta').checked == false){
						  
								add(elems[i].name, elems[i].options[j].value);
						  }
						  
						  if (first) break; /* detiene la búsqueda para  select-one */
						}
					//}
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

			mesIni=$F('fechaini').substring(0,7);
			mesFin=$F('fechafin').substring(0,7);
		 
			//validando fechas por mes
			if($('fechacambia').checked){
			
				if(mesIni !=mesFin){
					alert('<?=_("RANGO DE FECHAS NO VALIDO") ;?>');
					return false;
				}
			}
			
			var valors =  serialize($('form1'));  
			crear_reporte(valors);
		}		
	</script>

</head>

<body onload="document.getElementById('ckbtodocuenta').checked=true;marcar_todascuentas()">
<blockquote>
  <blockquote>
    <blockquote>
      <form id="form1" name="form1" method="post" action="exportar_transferencia.php" >
	  
	  <div id="cargando" class="spinner" style="display:none"><img src="/imagenes/iconos/spinner.gif" align="center" />Cargando…</div>	  
	  
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#EAF4FF" style="border:1px dashed #333366">			
			<tr>
				<td width="80%">
					<input type="radio" name="radio" id="radio" value="1" checked onclick="comportamientoDiv('+','opc1');comportamientoDiv('-','opc2')" />
					<?=_("POR MES/ANIO") ;?>
				</td>
				<td width="20%" align="right"><strong><?=_("ZONA HORARIA");?></strong>
					<select name="cmbzonahoraria" id="cmbzonahoraria">
						<option value="CABINA" Selected>Cabina</option>
						<option value="FILIAL" style="color:red">Filial</option>						
					</select> 
				</td>
			</tr>
			<tr>
				<td colspan="2"><div id='opc1' ><table width="200" border="0" align="center" cellpadding="1" cellspacing="1">
					<tr>
						<td><?=_("Mes") ;?></td>
						<td>
							<?
								$con->cmb_array("cmbmes",$mes_del_anio,date("m"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","1");
							?>
						</td>
						<td><?=_("A&ntilde;o") ;?></td>
						<td>
							<?
								$con->cmb_array("cmbanio",$anio,($_POST["cmbanio"])?$_POST["cmbanio"]:date("Y"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
							?>
						</td>
					</tr>              
				</table></div></td>
			</tr>
			<tr>
				<td colspan="2"><input type="radio" name="radio" id="fechacambia" value="2"  onclick="comportamientoDiv('-','opc1');comportamientoDiv('+','opc2')"  /><?=_("POR PERIODO DE FECHAS") ;?></td>
			</tr>
			<tr>
				<td colspan="2"><div id='opc2' style='display:none'><table width="309" border="0" align="center" cellpadding="1" cellspacing="1">
					<tr>
						<td width="141"><?=_("De") ;?>
							<input name="fechaini" id="fechaini" type="text" class="classtexto" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
							<button   id="cal-button-1">...</button>
							<script type="text/javascript">
								Calendar.setup({
								  inputField    : "fechaini",
								  button        : "cal-button-1",
								  align         : "Tr"
								});
							  </script>
						</td>
						<td width="152"><?=_("al") ;?>
							<input name="fechafin" id="fechafin" type="text" class="classtexto" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
							<button   id="cal-button-2">...</button>
							<script type="text/javascript">
								Calendar.setup({
								  inputField    : "fechafin",
								  button        : "cal-button-2",
								  align         : "Tr"
								});
							</script>
						</td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;&nbsp;<?=_("CUENTAS") ;?>         
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-------------------->&nbsp;<?=_("TODOS") ;?>
					<input name="ckbtodocuenta" type="checkbox" id="ckbtodocuenta" value="1" onclick="marcar_todascuentas()" title="<?=_("TODOS LAS CUENTAS") ;?>"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;&nbsp;<?
					if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
					$con->cmbselectdata($sql,"cmbcuenta[]","","onclick='desactiva_check()' size='7' multiple class='classtexto' onFocus='coloronFocus(this);desactiva_check()' onBlur='colorOffFocus(this)' ","2");	
					?>
					<div align="right"><input type="button" onclick="exportar_excel()"  name="btnexportar" id="btnexportar" value="&gt;&gt;&gt; EXPORTAR"  <?=($_GET["idmodelo"])?"":"DISABLED" ;?> style="text-align:center;font-weight:bold;font-size:10px;height:25px;"/></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
        </table> 	
		
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" style="border:1px solid #8080BF">
			<tr>
				<td width="73"><?=_("PLANTILLA") ;?></td>
				<td width="212"><? $con->cmbselectdata("SELECT IDMODELO,NOMBRE FROM $con->catalogo.catalogo_modeloplantilla_informe where TIPO='TRANSFERENCIA' AND IDUSUARIO='".$_SESSION["user"]."' ","cmbmodelo",$datomodelo[0][0]," style='font-size:10px;width:500px; height:80px' size='5' onchange='document.form1.btnmostrar.disabled=false;document.form1.btneliminar.disabled=false' onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2"); ?></td>
				<td width="133"> 
				  <input type="button" name="btnmostrar" id="btnmostrar" value="MOSTRAR" <?=($_GET["idmodelo"])?"":"DISABLED" ;?> onclick="mostrardata(document.form1.cmbmodelo.value)" />
				  <input type="button" name="btneliminar" id="btneliminar" value="ELIMINAR" <?=($_GET["idmodelo"])?"":"DISABLED" ;?> onclick="eliminarplantilla(document.form1.btneliminar.name)" />
				</td>
			</tr>          
			<tr>
				<td><?=_("NOMBRE") ;?></td>
				<td><input name="txtnombre" type="text" class="classtexto" id="txtnombre" style="text-transform:uppercase;"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=($_GET["idmodelo"])?$datomodelo[0][1]:$opcions["NOMBRE"];?>" size="80" /></td>
				<td><input type="button" name="btngrabar" id="btngrabar" value="GRABAR PLANTILLA" onclick="validarCampo(this.name)"  /></td>
			</tr>
        </table>
        <div id='div-informe'><? include("vista_transferencia.php"); ;?></div>
        <br/><br/><br/>		 
    </form>
    </blockquote>
  </blockquote>
</blockquote>
</body>
</html>

	<script type="text/javascript">
 
/* 		function crear_reporte(valores){
		
			 Dialog.alert({url: "crearReporte.php?"+valores, options: {method: 'get'}}, {className: "alphacube", width:540, okLabel: "Close"});

			}	 */		

		function crear_reporte(valores){

			var win = null;
			
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("GENERACION DE TRANSFERENCIA")?>',
					width: 650,
					height: 100,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: false,
					showModal  : true,
					//closable: false,
					minimizable: false,
					maximizable: false,
					resizable: false,
					opacity: 0.95,
					url: 'crearReporte.php?'+valores
				});

				win.showCenter('true');
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			
			return;
		}
	</script>