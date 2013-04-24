<?
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once('../../../modelo/functions.php');
	
	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	Auth::required(); 

	//validar_permisos("MENU_TRANSFER",1);

	//verificar permisos de accesos a las cuentas.	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$anio=mostrar_Anio(10);		
	  
 	if($_POST["btnexportar"])	include_once("exportar_encuesta.php");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_("American Assist") ;?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	


	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>
	
 	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>	
	 
	<script type="text/javascript">	 
		  
		function crear_reporte(valores){
		
			var win = null;
			
			if (win != null) alert('<?=_("CIERRE LA VENTANA ANTERIOR")?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("GENERACION DE ENCUESTA")?>',
					width: 550,
					height: 90,
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

				var valors =  serialize($('frmexporta'));  
				crear_reporte(valors);		
		}		
 				
/* 		function crear_reporte(valores){
			 Dialog.alert({url: "crearReporte.php?"+valores, options: {method: 'get'}}, {className: "alphacube", width:540, okLabel: "Close"});
		}
			 */
		function mostrar_plan(valor){
				new Ajax.Updater('div-plan', 'mostrarplan.php', {
					parameters: { idcodigo: valor },
					method: 'post'
				});			 
		}		
			
	</script>
</head>

<body onload="mostrar_plan('')">
<br>

	<form name='frmexporta' id='frmexporta' method='POST' action='exportar_encuesta.php'>	
	
		<table width="99%" border="0" cellpadding="1" cellspacing="1" bgcolor="#EEEEEE" style="border:1px solid #A8A8A8">
			<caption><?=_('ESTADISTICAS DE ENCUESTA')?></caption>
			<tr>
				<td><?=_("FECHA") ;?></td>
				<td width="20%">
					<?
					$con->cmb_array("cmbmes",$mes_del_anio,date("m"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","1");
					$con->cmb_array("cmbanio",$anio,($_POST["cmbanio"])?$_POST["cmbanio"]:date("Y"),"class='classtexto' onFocus='coloronFocus(this)' onBlur='colorOffFocus(this)'","1");
				?></td>
				<td><?=_("CCL PREVIO") ;?></td>
				<td><input name="txtccl" type="text" id="txtccl" size="6" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class='classtexto' onKeyPress="return validarnumero(event)" maxlength="2"/>%</td>
				<td><?=_("OBJETIVO") ;?></td>
				<td colspan="2"><input name="txtobjetivo" type="text" id="txtobjetivo" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class='classtexto' size="6" onKeyPress="return validarnumero(event)" maxlength="3"/></td>
			</tr>
			<tr>
				<td><?=_("CUENTA") ;?></td>
				<td>
					<?
						if($allcuentas==1)	$Sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $Sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
						$con->cmbselectdata($Sql_cuenta,"cmbcuenta","","class='classtexto' onFocus='coloronFocus(this)' onchange='mostrar_plan(this.value)'","","TODOS >>>");			
					?>
				</td>
				<td><?=_("PLAN") ;?></td>
				<td colspan="4"><div id="div-plan"></div></td>
			</tr>
			<tr>
				<td><?=_("STATUS") ;?></td>
				<td>		
					<select name="cmbstatusEncuenta[]" id="cmbstatusEncuenta[]" size='5'  multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>
					<?		
						$accesogrupo[]="EVAL";
						$accesogrupo[]="CERR";
						
						$result=$con->query("SELECT DISTINCT ARRSTATUSENCUESTA  FROM $con->temporal.asistencia ORDER BY ARRSTATUSENCUESTA ");
						while($reg = $result->fetch_object())
						 {
							if(in_array($reg->ARRSTATUSENCUESTA,$accesogrupo))	$selecion="selected";	else $selecion="";							
					?>
						<option value="<?=$reg->ARRSTATUSENCUESTA;?>" <?=$selecion;?> ><?=$evalencuesta_new[$reg->ARRSTATUSENCUESTA];?></option>
					<? 
						 } 
					?>
				</select>	 
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><input type="button" name="btnexportar" id="btnexportar" onclick="exportar_excel()" style="font-weight:bold" value="&gt;&gt;&gt;EXPORTAR"></td>
			</tr>
		</table>
	</form>
</body>
</html>
  