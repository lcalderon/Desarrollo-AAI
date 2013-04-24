<?php
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once('../../../modelo/functions.php');
 
    $con= new DB_mysqli();
	 
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	Auth::required($_SERVER['REQUEST_URI']);

	validar_permisos("MENU_TRANSFER",1);
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$anio=mostrar_Anio(10);		
	
 	if($_POST["btnexportar"])	include_once("exportar_transferencia.php");

	//$datomodelo=$con->consultation("select IDMODELO,NOMBRE from $con->catalogo.catalogo_modeloplantilla_informe where IDMODELO='".$_GET["idmodelo"]."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title>Reincidencias</title>
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
		
		function MostrarocultarDiv(valor){
			
			if(valor ==1){
				
				$('divporperiodo').style.display='none';
				$('divpormes').style.display='block';
			} else{
				$('divpormes').style.display='none';
				$('divporperiodo').style.display='block';				
			}		
			
		}
		
		function seleccionar_todo(){
			for (i=0;i<document.frmReincidencia.elements.length;i++)
				if(document.frmReincidencia.elements[i].type == "checkbox")	
					document.frmReincidencia.elements[i].checked=1
		}
		
		function deseleccionar_todo(){
			for (i=0;i<document.frmReincidencia.elements.length;i++)
				if(document.frmReincidencia.elements[i].type == "checkbox"  && document.frmReincidencia.elements[i].disabled==false)	
					document.frmReincidencia.elements[i].checked=0
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
					 
						// if(elems[i].name != 'cmbcuenta[]' && document.getElementById('ckbtodocuenta').checked == true){
						
						  add(elems[i].name, elems[i].options[j].value);
						  // } else if(document.getElementById('ckbtodocuenta').checked == false){
						  
								// add(elems[i].name, elems[i].options[j].value);
						  // }
						  
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
 
	
	var valors =  serialize($('frmReincidencia'));  
	crear_reporte(valors);
}		
		</script>

</head>

<body onload="$('cmbcuenta').selectedIndex=0">
 
    <form id="frmReincidencia" name="frmReincidencia" method="post" action="exportar_transferencia.php" >
		<table width="100%"   border="0" cellpadding="1" cellspacing="1" bgcolor="#1b7ca7" style="border:2px solid #eaea00" align="center">
			<tr>
				<td style="color:#FFFFFF"><input type="radio" name="radio" id="pormes" value="1" checked onclick="MostrarocultarDiv(this.value)"/>POR MES/ANIO</td>
				<td colspan="2">
					<div id="divpormes">
					<?			
						$con->cmb_array("cmbmes",$mes_del_anio,date("m"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","","--TODOS--","1");

						$con->cmb_array("cmbanio",$anio,($_POST["cmbanio"])?$_POST["cmbanio"]:date("Y"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
				 
					?>
					</div>
				</td>
			</tr>
			<tr>
				<td style="color:#FFFFFF"><input type="radio" name="radio" id="porperiodo" value="2" onclick="MostrarocultarDiv(this.value)"/>POR PERIODO DE FECHAS</td>
				<td colspan="2" style="color:#FFFFFF">
					<div id="divporperiodo" style="display:none">
					<?=_("De") ;?>
					<input name="fechaini" type="text" class="classtexto"  id="fechaini" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
					<button   id="cal-button-1">...</button>
						<script type="text/javascript">
						Calendar.setup({
						  inputField    : "fechaini",
						  button        : "cal-button-1",
						  align         : "Tr"
						});
						</script>
					&nbsp;<?=_("al") ;?>&nbsp;
					<input name="fechafin" type="text" id="fechafin" readonly="readonly" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
					<button   id="cal-button-2">...</button>
						<script type="text/javascript">
							Calendar.setup({
							  inputField    : "fechafin",
							  button        : "cal-button-2",
							  align         : "Tr"
							});
						</script>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td style="color:#FFFFFF" width="350">&nbsp;<strong>CUENTAS</strong></td>
				<td style="color:#FFFFFF"><strong>STATUS ASISTENCIA</strong></td>
				<td style="color:#FFFFFF"><strong>CONDICION DE SERVICIO</strong></td>
			</tr>
			<tr bgcolor="#135a79">
				<td>
					<?
						if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
						$con->cmbselectdata($sql,"cmbcuenta","","size='7' class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' ","2");	
					?>
				</td>
				<td>
					<?
						$con->cmb_array("cmbstatusasistencia[]",$desc_status_asistencia,"CON","size=7  multiple='multiple' class='classtexto' onfocus='coloronFocus(this)' onblur='colorOffFocus(this)'","1");
					?>
				</td>
				<td>
				<?
					$con->cmb_array("cmbcondicionservicio[]",$desc_cobertura_servicio,"COB","size=7  multiple='multiple' class='classtexto' onfocus='coloronFocus(this)' onblur='colorOffFocus(this)","",_('TODOS'));
				?>
				</td>
			</tr>	
			<tr>
				<td colspan="3" align="right">
					<input type="button" name="btnexportar" id="btnexportar" value="<?=_("EXPORTAR EXCEL")?>" onclick="exportar_excel()" style="font-size:9px;font-weight:bolder;font-size:11px;background-color:#004a00;color:#FFFFFF;height:35px;width:220px;" title="Exportar Archivo a Excel."/>   
				</td>
			</tr>
		</table>	  
    </form>
  
</body>
</html>

	<script type="text/javascript">

		function crear_reporte(valores){
// alert(valores);
			var win = null;
			
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("GENERACION DE REINCIDENCIAS")?>',
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