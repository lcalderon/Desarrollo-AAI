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
 
 	if($_POST["btnexportar"])	include_once("exportar_deficiencia.php");

	$datomodelo=$con->consultation("select IDMODELO,NOMBRE from $con->catalogo.catalogo_modeloplantilla_informe where TIPO='DEFICIENCIA' AND IDMODELO='".$_GET["idmodelo"]."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trasnferencia</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">
	@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");
	</style> 
	
	 
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

				new Ajax.Updater('div-informe', 'vista_deficiencia.php', {
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
					document.form1.action='gdeficiencia.php?valor='+valor;
					document.form1.submit();
			    }			
			}
			else
			{
				document.form1.action='gdeficiencia.php?valor='+valor;
				document.form1.submit();
			}
		}
		
		function eliminarplantilla(valor){
		
			if(confirm('<?=_("DESEA ELIMINAR EL REGISTRO?.") ;?>'))
			{
				document.form1.action='gdeficiencia.php?valor='+valor;
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
 	
		</script>
				
</head>
<body onload="document.getElementById('ckbtodocuenta').checked=true;marcar_todascuentas()">
<blockquote>
  <blockquote>
    <blockquote>
      <form id="form1" name="form1" method="post" action="" >
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#A2C0DF" style="border:1px dashed #333366">
			<caption><?=_("REPORTE DEFICIENCIAS DE CALIDAD") ;?></caption>
			<tr>
				<td width="180"><label><input type="radio" name="radio" id="radio" value="1" checked onclick="comportamientoDiv('+','opc1');comportamientoDiv('-','opc2')" /><?=_("POR MES/ANIO") ;?></label></td>
			</tr>
			<tr>
				<td><div id='opc1'><table width="200" border="0" align="center" cellpadding="1" cellspacing="1">
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
					</table></div>
				</td>
			</tr>
			<tr>
				<td><input type="radio" name="radio" id="radio2" value="2"  onclick="comportamientoDiv('-','opc1');comportamientoDiv('+','opc2')"  /><?=_("POR PERIODO DE FECHAS") ;?></td>
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
							</script>
						</td>
						<td width="152"><?=_("al") ;?>
							<input name="fechafin" type="text" class="classtexto" id="fechafin" readonly="readonly" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="12" maxlength="10" value="<?=date("Y-m-d");?>" />
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
						<input type="submit" name="btnexportar" id="btnexportar" value="&gt;&gt;&gt; EXPORTAR"  <?=($_GET["idmodelo"])?"":"DISABLED" ;?> style="text-align:center;font-weight:bold;font-size:10px;height:25px;"/>
					</div>         
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
        </table> 		
	
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#6095CA" style="border:1px solid #8080BF">
			<tr>
				<td width="73"><?=_("PLANTILLA")?></td>
				<td width="212"><? $con->cmbselectdata("SELECT IDMODELO,NOMBRE FROM $con->catalogo.catalogo_modeloplantilla_informe where TIPO='DEFICIENCIA' AND IDUSUARIO='".$_SESSION["user"]."' ","cmbmodelo",$datomodelo[0][0]," style='font-size:10px;width:500px; height:80px' size='5' onchange='document.form1.btnmostrar.disabled=false;document.form1.btneliminar.disabled=false' onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2"); ?></td>
				<td width="133"> 
					<input type="button" name="btnmostrar" id="btnmostrar" value="MOSTRAR" <?=($_GET["idmodelo"])?"":"DISABLED" ;?> onclick="mostrardata(document.form1.cmbmodelo.value)"/>
					<input type="button" name="btneliminar" id="btneliminar" value="ELIMINAR" <?=($_GET["idmodelo"])?"":"DISABLED" ;?> onclick="eliminarplantilla(document.form1.btneliminar.name)"/>
				</td>
			</tr>          
			<tr>
				<td><?=_("NOMBRE") ;?></td>
				<td><input name="txtnombre" type="text" class="classtexto" id="txtnombre" style="text-transform:uppercase;"  onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=($_GET["idmodelo"])?$datomodelo[0][1]:$opcions["NOMBRE"];?>" size="80" /></td>
				<td><input type="button" name="btngrabar" id="btngrabar" value="GRABAR PLANTILLA" onclick="validarCampo(this.name)"  /></td>
			</tr>
        </table>
        <div id='div-informe' style="width:110%" ><? include("vista_deficiencia.php");?></div>
        <br/>
        <br/>
        <br/>
    </form>
    </blockquote>
  </blockquote>
</blockquote>
</body>
</html>
