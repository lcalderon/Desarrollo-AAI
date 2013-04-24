<?php

	session_start();

	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/functions.php");
	include_once("../../../modelo/validar_permisos.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");

	$con= new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

//verificar sesion activa.
	Auth::required($_SERVER['REQUEST_URI']);

	if($_GET["origen"]=="HISTORICOEXP") validar_permisos("MENU_CONSULTAHIST",1);  else    validar_permisos("MENU_EXPEDIENTE",1);

//cantidad de ubigeo.
    $nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
    $cantidadValidar=$con->lee_parametro("CANTIDAD_ENTIDADES_AVALIDAR");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<title><?=($_GET["idexpediente"])?_("Expediente # ").$_GET["idexpediente"]:_("Recepcion de Expediente");?></title>
	<style type="text/css">
	<!--
	.style1 {color: #000000}
	.style2 {
		color: #e1240e;
		font-weight: bold;
	}
	.style3 {
		color: #000099;
		font-weight: bold;
	}
	.style5 {font-weight: bold}
	
	body {
		margin-left: 6px;
		margin-top: 0px;
		margin-right: 1px;
		margin-bottom: 0px;
	}	
	-->
	</style>	
	
	<!-- se usa para validar y dar estilos -->
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
	<!-- se usa para el autocompletar -->
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
	<link href="../../../../librerias/windows_js_1.3/themes/mac_os_x.css" rel="stylesheet" type="text/css" ></link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_ext.js"></script>
	
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico"/>
	  
	<script language="javascript">


		function validar_telefono(){
		
			new Ajax.Request('validar_telefono.php',{
				method: 'post',
				parameters:{
					telefono:$F('txtclavetitular')
				},				
					onSuccess: function(respuesta){
					
					var respuestaFinal=respuesta.responseText.replace(/<[^>]+>/g,'');				
					respuestaFinal= respuestaFinal.replace(/^\s+|\s+$/g,'') ;
					
					alert(respuestaFinal);
					if(respuestaFinal !='NO EXISTE'){
									
						//$('cmbautorizacion').selectedIndex=3;
					
						for(var i =($('cmbautorizacion').length) ;i > 0;i--){
						
							if($('cmbautorizacion').options[i-1].value !='WEB') $('cmbautorizacion').options[i-1] = null;
						 
						}
					}
						
					},
					onFailure: function() { alert('ERROR, NO SE HA REALIZADO LA OPERACION.'); }						
				});				 
		 
		}  
	
		function validarCampo(){

			id_codigo=document.getElementById('idafiliado').value;
			if(id_codigo =='0') id_codigo='';
			
			document.myForm.txtnombretitular.value=document.myForm.txtnombretitular.value.replace(/^\s*|\s*$/g,"");
			document.myForm.txtmaternotitular.value=document.myForm.txtmaternotitular.value.replace(/^\s*|\s*$/g,"");
			document.myForm.txtnomautoriza.value=document.myForm.txtnomautoriza.value.replace(/^\s*|\s*$/g,"");
			document.myForm.txttelefonotitular[0].value=document.myForm.txttelefonotitular[0].value.replace(/^\s*|\s*$/g,"");
			document.myForm.DIRECCION.value=document.myForm.DIRECCION.value.replace(/^\s*|\s*$/g,"");

			if(document.myForm.cmbcuentatitular.value ==''){
			
				alert('<?=_("SELECCIONE LA CUENTA") ;?>');
				document.myForm.cmbcuentatitular.focus();
				cambiarestilo(document.myForm.cmbcuentatitular);
				return (false);
				
			} else if(document.myForm.cmbprogramatitular.value ==''){
			
				alert('<?=_("SELECCIONE EL PROGRAMA") ;?>');
				document.myForm.cmbprogramatitular.focus();
				cambiarestilo(document.myForm.cmbprogramatitular);
				return (false);
				
			} else if(document.myForm.txtpaternotitular.value ==''){
			
				alert('<?=_("INGRESE EL APELLIDO PATERNO DEL TITULAR") ;?>');
				document.myForm.txtpaternotitular.focus();
				cambiarestilo(document.myForm.txtpaternotitular);
				return (false);
				
			} else if(document.myForm.txtnombretitular.value ==''  && !id_codigo){
			
				alert('<?=_("INGRESE EL NOMBRE DEL TITULAR") ;?>');
				document.myForm.txtnombretitular.focus();
				cambiarestilo(document.myForm.txtnombretitular);
				return (false);
				
			} else if(document.myForm.txttelefonotitular[0].value ==''){
			
				alert('<?=_("INGRESE EL NUMERO TELEFONICO DEL TITULAR") ;?>.');
				document.myForm.txttelefonotitular[0].focus();
				cambiarestilo(document.myForm.txttelefonotitular[0]);
				return (false);
				
			} else if(document.myForm.txttelefonotitular[1].value ==''){
			
				alert('<?=_("INGRESE EL SEGUNDO NUMERO TELEFONICO DEL TITULAR") ;?>.');
				document.myForm.txttelefonotitular[1].focus();
				cambiarestilo(document.myForm.txttelefonotitular[1]);
				return (false);
				
			} else if(document.myForm.txtpaternocontacto.value ==''){
			
				alert('<?=_("INGRESE EL APELLIDO PATERNO DEL CONTACTO") ;?>.');
				verificardiv('contacto','V','btntitularver2');
				document.myForm.txtpaternocontacto.focus();
				cambiarestilo(document.myForm.txtpaternocontacto);
				return (false);
				
			} else if(document.myForm.txtnombrecontacto.value =='' && !id_codigo){
			
				alert('<?=_("INGRESE EL NOMBRE DEL CONTACTO") ;?>.');
				verificardiv('contacto','V','btntitularver2');
				document.myForm.txtnombrecontacto.focus();
				cambiarestilo(document.myForm.txtnombrecontacto);
				return (false);
				
			} else if(document.myForm.txttelefonocontacto[0].value ==''){
			
				alert('<?=_("INGRESE EL NUMERO TELEFONICO DEL CONTACTO") ;?>.');
				verificardiv('contacto','V','btntitularver2');
				document.myForm.txttelefonocontacto[0].focus();
				cambiarestilo(document.myForm.txttelefonocontacto[0]);
				return (false);
			} else if(document.myForm.CVEENTIDAD1.value ==''){
			
				alert('<?=_("SELECCIONE ")._("ENTIDAD 1") ;?>');
				document.myForm.CVEENTIDAD1.focus();
				cambiarestilo(document.CVEENTIDAD1.DIRECCION);
				return (false);	
			} else if(document.myForm.CVEENTIDAD2.value ==''){
			
				alert('<?=_("SELECCIONE ")._("ENTIDAD 2") ;?>');
				document.myForm.CVEENTIDAD2.focus();
				cambiarestilo(document.CVEENTIDAD2.DIRECCION);
				return (false);
			
		   <? if($nroubigeo >=3 and $cantidadValidar >=3){ ?>			
			} else if(document.myForm.CVEENTIDAD3.value ==''){
			
				alert('<?=_("SELECCIONE ")._("ENTIDAD 3") ;?>');
				document.myForm.CVEENTIDAD3.focus();
				cambiarestilo(document.CVEENTIDAD3.DIRECCION);
				return (false);
				
			<? } ?>	
			} else if(document.myForm.DIRECCION.value =='' ){
			
				alert('<?=_("INGRESE LA DIRECCION") ;?>');
				document.myForm.DIRECCION.focus();
				cambiarestilo(document.myForm.DIRECCION);
				return (false);
				
			} else if(document.myForm.cmbautorizacion.value ==''){
			
				alert('<?=_("SELECCIONE EL MEDIO DE AUTORIZACION.") ;?>');
				document.myForm.cmbautorizacion.focus();
				cambiarestilo(document.myForm.cmbautorizacion);
				return (false);
				
			} else if(document.myForm.cmbautorizacion.value!='BD'){

				if(document.myForm.cmbautorizacion.value!='' && document.myForm.txtnomautoriza.value ==''){

					alert('<?=_("INGRESE EL NOMBRE DEL AUTORIZANTE") ;?>');				
					
					document.myForm.txtnomautoriza.focus();
					cambiarestilo(document.myForm.txtnomautoriza);
					return (false);

				}
			}

			if(document.myForm.idexpediente.value !='' &&  document.myForm.cmbstatusexp.value=='CER'){

				return verificar_status(document.myForm.idexpediente.value);
				
			} else{

				if(confirm('<?=(!$_GET["idexpediente"])?_("DESEA REGISTRAR UN NUEVO EXPEDIENTE?."):_("REALMENTE DESEA PROSEGUIR CON LA ACTUALIZACION DEL EXPEDIENTE?.") ;?>')){

						$('btngrabarexp').value='PROCESANDO...';
						$('btngrabarexp').disabled=true;
						if($('btnasistencia')) $('btnasistencia').disabled=true;
						if($('btnreincidencia')) $('btnreincidencia').disabled=true;

						new Ajax.Request('../../../controlador/expediente/PanelExpediente.php',{
							method : 'post',
							parameters : $('myForm').serialize(true),
							//encoding: 'UTF-8',
							onSuccess: function(resp){
								variables=resp.responseText;

								var elemento= variables.split('&');							
								var idexpediente= elemento[0];

								<? if(!$_GET["idexpediente"]){ ?> alert('!!! *** '+'<?=_("SE GENERO EL EXPEDIENTE NRO ") ;?>'+idexpediente+' *** !!!'); <? } ?>
														
								url= '../../expediente/entrada/expediente_frmexpediente.php?idexpediente='+variables;
								
								reDirigir(url);
								
							},
							onFailure: function() { alert('ERROR, NO SE HA REALIZADO LA OPERACION.'); }						
						});
				}
			}
			
			return (false);
		}

		function cambiarestilo(obj){
			obj.className = 'cambicolor';
		}

		function recargar_titular(idafiliado,cuenta,plan){
			mostrarDiv('titular','vista_datos_titular.phtml',cuenta,plan,'',idafiliado);
			recargar_ubigeo(idafiliado);
		}

		function recargar_reincidencias(idexpediente,idafiliado,cuenta,plan){

			mostrarDiv('titular','vista_datos_titular.phtml',cuenta,plan,'',idafiliado,idexpediente);

			recargar_ubigeo(idafiliado);
		}

		function ventana_beneficiario(){

			var cod_afiliado=document.myForm.idafiliado.value;
			window.open("data_beneficiario.php?cod_afiliado="+cod_afiliado,"mediop","height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}

		function ventana_sac(){

			var busqueda=document.myForm.txtclavetitular.value;
			var busqueda2=document.myForm.cmbcuentatitular.value;
			window.open("../../catalogos/siac/buscarafiliado.php?busqueda="+busqueda+"&buscarafiliado=1&cmbbusqueda=1&cuenta="+busqueda2,"mediop","height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}

		function ventana_busqueda_externa(){

			var ncertificado = document.myForm.txtclavetitular.value;
			var ndocumento = document.myForm.txtnumdoctitular.value;
			var appaterno = document.myForm.txtpaternotitular.value;
			var apmaterno = document.myForm.txtmaternotitular.value;
			var nombres = document.myForm.txtnombretitular.value;
			
			var url = "?ncertificado="+ncertificado+"&ndocumento="+ndocumento+"&appaterno="+appaterno+"&apmaterno="+apmaterno+"&nombres="+nombres;
			
			window.open("../consulta/buscar_externo.php"+url,"mediop","height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}
			
		function ventana_reincidencia(){

			var busqueda=document.myForm.txtclavetitular.value;
			var busqueda2=document.myForm.cmbcuentatitular.value;
			var busqueda3=document.myForm.cmbprogramatitular.value;
			window.open("data_reincidencia.php?busqueda="+busqueda+"&cuenta="+busqueda2+"&buscarafiliado=1&plan="+busqueda3,"mediop","height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}

		function ventana_ubigeo(info){

			var expediente=document.myForm.idexpediente.value;
			var cveexpediente=document.myForm.txtclavetitular.value;
			if(document.myForm.idexpediente.value=='')	expediente=document.myForm.idexpediente2.value;

			window.open("frmubigeo.php?info="+info+"&id_expediente="+expediente+"&cve_id="+cveexpediente,"expedientes","height=400, width=800,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}

		function ventana_expedientehistorico(exped){

			var expediente=document.myForm.idexpediente.value;
			var cveexpediente=document.myForm.txtclavetitular.value;
			var cuenta=$('cmbcuentatitular').value;
			var plan=$('cmbprogramatitular').value;
			
			if(document.myForm.idexpediente.value=='')	expediente=document.myForm.idexpediente2.value;
			if(exped)	validaexist=1; else validaexist='';

			window.open("data_expedientehistorico.php?id_expediente="+expediente+"&validaexist="+validaexist+"&cve_id="+cveexpediente+'&cuenta='+cuenta+'&plan='+plan,"mediop","height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		}
		
		function copiarTitular(){

			document.myForm.txtpaternocontacto.value=document.myForm.txtpaternotitular.value;
			document.myForm.txtmaternocontacto.value=document.myForm.txtmaternotitular.value;
			document.myForm.txtnombrecontacto.value=document.myForm.txtnombretitular.value;
			document.myForm.txtnumdoccontacto.value=document.myForm.txtnumdoctitular.value;
			document.myForm.txtdigcontacto.value=document.myForm.txtdigtitular.value;
			document.myForm.txttelefonocontacto[0].value=document.myForm.txttelefonotitular[0].value.toUpperCase();
			document.myForm.txttelefonocontacto[1].value=document.myForm.txttelefonotitular[1].value.toUpperCase();
			document.myForm.txttelefonocontacto[2].value=document.myForm.txttelefonotitular[2].value.toUpperCase();
			document.myForm.txttelefonocontacto[3].value=document.myForm.txttelefonotitular[3].value.toUpperCase();
			document.myForm.cmbtipodoccontacto.selectedIndex=document.myForm.cmbtipodoctitular.selectedIndex;
			document.myForm.cmbtelefono0contacto.selectedIndex=document.myForm.cmbtelefono0titular.selectedIndex;
			document.myForm.cmbtelefono1contacto.selectedIndex=document.myForm.cmbtelefono1titular.selectedIndex;
			document.myForm.cmbtelefono2contacto.selectedIndex=document.myForm.cmbtelefono2titular.selectedIndex;
			document.myForm.cmbtelefono3contacto.selectedIndex=document.myForm.cmbtelefono3titular.selectedIndex;
			document.myForm.txtcodigoa0contacto.value=document.myForm.txtcodigoa0titular.value;
			document.myForm.txtcodigoa1contacto.value=document.myForm.txtcodigoa1titular.value;
			document.myForm.txtcodigoa2contacto.value=document.myForm.txtcodigoa2titular.value;
			document.myForm.txtcodigoa3contacto.value=document.myForm.txtcodigoa3titular.value;
			document.myForm.cmbtsp0contacto.selectedIndex=document.myForm.cmbtsp0titular.selectedIndex;
			document.myForm.cmbtsp1contacto.selectedIndex=document.myForm.cmbtsp1titular.selectedIndex;
			document.myForm.cmbtsp2contacto.selectedIndex=document.myForm.cmbtsp2titular.selectedIndex;
			document.myForm.cmbtsp3contacto.selectedIndex=document.myForm.cmbtsp3titular.selectedIndex;
			document.myForm.txtextension0contacto.value=document.myForm.txtextension0titular.value;
			document.myForm.txtextension1contacto.value=document.myForm.txtextension1titular.value;
			document.myForm.txtextension2contacto.value=document.myForm.txtextension2titular.value;
			document.myForm.txtextension3contacto.value=document.myForm.txtextension3titular.value;
			document.myForm.idbeneficiarioexis.value='';
		}
	</script>
	 
	<script type="text/javascript">

		function verificardiv(nombrediv,valors,nombre){

			if(valors=='V'){
				comportamientoDiv('+',nombrediv);
				document.getElementById(nombre).value='O';
			} else{
				comportamientoDiv('-',nombrediv);
				document.getElementById(nombre).value='V';
			}
		}

		function mostrarDivInfo(valors){

			if(valors=='1'){
				document.getElementById('div-info').style.display='block';
				document.getElementById('mostrar').style.display='none';
				document.getElementById('ocultar').style.display='block';
			} else{
				document.getElementById('div-info').style.display='none';
				document.getElementById('ocultar').style.display='none';
				document.getElementById('mostrar').style.display='block';
			}
		}

		function bloquear_boton(valors){

			if(valors=='V'){
				document.myForm.btncopiar.disabled=true;
				document.myForm.btnbeneficiario.disabled=true
			} else{
				document.myForm.btncopiar.disabled=false;
				document.myForm.btnbeneficiario.disabled=false
			}
		}

	</script>
	
	<script type="text/javascript">
	//visulaizar tabs
		function tab(pestana,panel){
		
			pst 	= document.getElementById(pestana);
			pnl 	= document.getElementById(panel);
			psts	= document.getElementById('tabs').getElementsByTagName('li');
			pnls	= document.getElementById('paneles').getElementsByTagName('div');
			
			// eliminamos las clases de las pestañas
			for(i=0; i< psts.length; i++){
				psts[i].className = '';
			}
			
			// Añadimos la clase "actual" a la pestaña activa
			pst.className = 'actual';
			
			// eliminamos las clases de las pestañas
			for(i=0; i< pnls.length; i++)
			{
				pnls[i].style.display = 'none';
			}
			
			// Añadimos la clase "actual" a la pestaña activa
			pnl.style.display = 'block';
		}
	</script>

</head>
<body <?=(!$_GET["idexpediente"])?"onLoad='ventana_sac()'":""?> > 
<script type="text/javascript"  src="../../../../librerias/wz_tooltip/wz_tooltip.js"></script>
<script type="text/javascript" src="../../../../librerias/wz_tooltip/tip_balloon.js"></script>
<script type="text/javascript" src="../../../../librerias/wz_tooltip/tip_centerwindow.js"></script>
<?php

//Validar id expediente     
    if(crypt($_GET["idexpediente"],'666')!=$_GET["varexis"] and $_GET["idexpediente"] and $_SESSION["user"]!="LCALDERON"){
        echo "<script>";
        echo "alert('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');";
        echo "window.close();";
        echo "</script>";

        die('*** ID DEL EXPEDIENTE NO ESTA VALIDADO!!! ***');
    }
 
//visualizar el logo de pruebas     
    if($con->logoMensaje){
?>
    <div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
 
	<form id='myForm' name='myForm' action="" method="post">
	<input type="hidden" name="idexpediente" id="idexpediente" value="<?=$_GET["idexpediente"];?>" />
	<input type="hidden" name="fechaapertura" id="fechaapertura" value="<?=date("Y-m-d H:i:s");?>" />
	<input  type="hidden" name="txturlacces"id="txturlacces" value="<?=urlencode($_SERVER['REQUEST_URI'])?>"/>
 	<!--Tabla titular--> 	
		<div id="titular" style="width:100%"><?  include("vista_datos_titular.phtml"); ?></div>		
	<br/>
	<!--Tabla contacto--> 
	<table width="100%" border="0" cellpadding="1" cellspacing="0">
		<tr>
			<td width="11%" bgcolor="#002f5e"  style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;color:#FFFFFF"><strong><?=_('DATOS CONTACTO')?></strong></td>
			<td  colspan="3" width="100"><input type="button" name="btntitularver2" title="<?=_('Mostrar/Ocultar contacto')?>" id="btntitularver2" value="0" style="font-weight:bold;width:30px;font-size:10px;" onclick="verificardiv('contacto',this.value,this.name);bloquear_boton(this.value);" />
				<input type="button" name="btncopiar" title="<?=_('Copiar Titular')?>" id="btncopiar" value="<?=_('COPIAR INFO TITULAR')?>" style="font-weight:bold;font-size:10px;"  onclick="copiarTitular();" />
				<input type="button" name="btnbeneficiario" title="<?=_('Seleccionar Beneficiario')?>" id="btnbeneficiario" value="<?=_('BENEFICIARIO')?>" style="font-weight:bold;font-size:10px;" onClick="ventana_beneficiario()" />
			</td>
		</tr>
		<tr>
			<td style="border:1px solid #002f5e" colspan="4" bgcolor="#F1F4F5"><div id="contacto"><? include('vista_datos_contacto.phtml'); ?></div></td>
		</tr>
	</table>
	<br/>		
	<!--Tabla ubicacion del servicio--> 	
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" width="16%" height="20"  bgcolor="#004080"  style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;color:#FFFFFF"><strong><?=_('UBICACION DEL SERVICIO')?></strong></td>
			<td colspan="2" width="16%" height="20"  bgcolor="#004080"  style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;color:#FFFFFF"><strong><?=_('AUTORIZACIONES')?></strong></td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="div-ubigeo"><? include("vista_datos_ubigeo.phtml"); ?></div>
			</td>
			<td colspan="2">
				<div id="div-autorizacion"><? include("vista_autorizaciones.phtml"); ?></div>
			</td>
		</tr>	  
	</table>
	<span class="style2">(*)<?=_("Campos obligatorios.") ;?></span>
	<br/>	
	<? if(validar_permisos("EXPED_GRABAR")){ ?><input type="button" name="btngrabarexp" id="btngrabarexp" value="<?=_("GRABAR EXPEDIENTE") ;?>"  onClick="validarCampo()" <?=$disabledall;?> style="text-align:center;font-weight:bold;font-size:10px;height:35px;"/><? } ?>
	<? if(validar_permisos("EXPED_ASIGNARFAM")) { ?> 	<? if($_GET["idexpediente"]) { ?><input type="button" name="btnasistencia" id="btnasistencia" value="<?=_("ASIGNAR ASISTENCIA") ;?>" <?=$disabledall;?> onClick="openWindow_familia()"  style="text-align:center;font-weight:bold;font-size:10px;height:35px;"  /> <? } } ?>
	</form>
	
	<div id="div-expasistencia"><? if($_GET["idexpediente"]) include("expediente_asistencia.php");?></div>
		
	</body>
</html>
	
	<script type="text/javascript">
// actualizar las asistencia 
	<? if($_GET["idexpediente"]){ ?>
		new Ajax.PeriodicalUpdater('div-expasistencia', 'expediente_asistencia.php?idexpediente=<?=$_GET["idexpediente"];?>&gestion=<?=$_GET["gestion"];?>&urlActivo='+$('txturlacces').value, {
			method: 'get', frequency: 8, decay: 1
		});
	<? } ?>

	function openWindow_familia(){
		
		status=document.getElementById('statusafi').value;
		
		if(document.myForm.cmbcuentatitular.value =='' ){
			alert('<?=_("SELECCIONE LA CUENTA.") ;?>');
			document.myForm.cmbcuentatitular.focus();
			cambiarestilo(document.myForm.cmbcuentatitular);		
		} else if(document.myForm.cmbprogramatitular.value =='' ){
			alert('<?=_("SELECCIONE EL PROGRAMA.") ;?>');
			document.myForm.cmbprogramatitular.focus();
			cambiarestilo(document.myForm.cmbprogramatitular);
			return (false);
		} else{
				Dialog.alert({url: "../../catalogos/asignacion_familia/asignar_familia.php?idexpediente=<?=$row->IDEXPEDIENTE;?>&idprograma=<?=$row->IDPROGRAMA;?>&idcuenta=<?=$row->IDCUENTA;?>&status="+status, options: {method: 'get'}}, {className: "alphacube", width:770, height:542, okLabel: "Cerrar"});
		}
	}

	var validar_func = '';
	var win = null;

	</script>
	
	<script type="text/javascript">

	function verificar_status(idexpediente){

		new Ajax.Request('verficar_status.php',{
			method: 'post',
			parameters:{
				idexpediente:idexpediente
			},
			onSuccess: function(t){

				if(t.responseText ==0){

					if(confirm('<?=_("ESTA SEGURO DE CERRAR EL EXPEDIENTE, UNA VEZ CERRADO NO PODRA REALIZAR CAMBIOS")?>.')){

						$('btngrabarexp').value='PROCESANDO...';
						$('btngrabarexp').disabled=true;

						new Ajax.Request('../../../controlador/expediente/PanelExpediente.php',{
							method : 'post',
							parameters : $('myForm').serialize(true),
							onSuccess: function(resp){
								variables=resp.responseText;

								var elemento= variables.split('&');
								var idexpediente= elemento[0];

								alert('!!! '+'<?=_("HISTORICO - SE CERRO EL EXPEDIENTE NRO ") ;?>'+idexpediente);
								window.close();

							}
						});
					}
				} else{
					alert('*** <?=_("NO ES POSIBLE CERRAR EL EXPEDIENTE, AUN EXISTE ASISTENCIA(S) NO CONCLUIDO(S)")?>. ***');
				}

				return false;
			}
		});
	}

//vista contacto
	function recargar_contacto(idcodigo){

		new Ajax.Updater('contacto', 'vista_datos_contacto.phtml',{
			parameters: { idcodigo: idcodigo },
			method: 'post'
		});
	}

//vista ubigeo
	function recargar_ubigeo(idcodigo,opc,opcion2){
 
		new Ajax.Updater('div-ubigeo', 'vista_datos_ubigeo.phtml',{
			parameters:{
				idcodigo:idcodigo,
				id_codigo:opcion2,
				opcion:opc
			},
			method: 'post',
			evalScripts:true,
			onSuccess: function(resp){
	 
				new Ajax.Updater('div-autorizacion', 'vista_autorizaciones.phtml',{
					parameters: { idcodigo: idcodigo },
					method: 'post'
				});			
			}
		});			
	}

	function verifica_cuenta(){

		new Ajax.Request('verficacuenta.php',{
			method: 'post',
			asynchronous: true,
			postBody: 'idexpediente=<?=$row->IDEXPEDIENTE?>'+'&idplan='+$F('cmbprogramatitular'),
			onSuccess: function(resp){

				var elemento= resp.responseText.split(',');

				if(elemento[0]==1){
					alert('NO ES POSIBLE CAMBIAR DE CUENTA O PLAN, YA FUE CREADO LA ASISTENCIA.\n'+'***  '+elemento[3]);
					document.myForm.cmbcuentatitular.value=elemento[1];
					mostrarDiv('div-programa','mostrarprograma.php',elemento[1],elemento[2]);
				}
			}
		});
	}
	
//repuesta activar afliado	
	function respuesta_activacion(){
	
		document.getElementById('div-statusafiliado').innerHTML = '<?=_("ACTIVO")?>';
		document.getElementById('div-activacion').innerHTML= '<img src="../../../../imagenes/iconos/succes.gif" align="middle">&nbsp;SE ACTIVO CORRECTAMENTE AL AFILIADO.';
		document.getElementById('div-activacion').style.backgroundColor= '#BBFFBB';
		document.getElementById('div-activacion').style.border = 'solid 2px #5EFF5E';
		setTimeout("document.getElementById('div-activacion').style .display = 'none'",6000);
	
	}

//ventana activar afiliado
	function activar_afiliado(ckecbox){
		
		idafiliado=$F('idafiliado');
		codcuenta=$F('cmbcuentatitular');
		codprograma=$F('cmbprogramatitular');
		
		window.showModalDialog("activar_afiliado.php?idafiliado="+idafiliado+"&codcuenta="+codcuenta+"&codprograma="+codprograma, "Activacion", "dialogHeight: 150px; dialogWidth: 570px; dialogTop: 200px; dialogLeft: 200px; center: no;menubar:no; resizable: no; status: no;resizable:no;toolbar:no;");
	}	
	
	function ver_detalle(idprograma){

		var validar_func = '';
		var win = null;
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("DETALLE DEL CONTRATO")?>',
					width: 800,
					height: 500,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: "../../plantillas/contrato.php?idprograma="+idprograma
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win){
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				
				Windows.addObserver(myObserver);
			}
			
			return;		 
	}
	
	/* function activar_afiliado2(ckecbox){
 
		marcado=$('cbkactivar').checked;
		
		if(marcado){
			if(confirm('<?=_("ESTA SEGURO DE ACTIVAR AL AFILIADO?") ;?>')){

				new Ajax.Request('activar_afiliado.php', {
					method: 'post',
					postBody: 'marcado='+marcado+'&idafiliado='+$F('idafiliado')+'&codcuenta='+$F('cmbcuentatitular')+'&codprograma='+$F('cmbprogramatitular'),
					onCreate: function(objeto){
						document.getElementById('div-activacion').innerHTML= '<img src="../../../../imagenes/iconos/loader.gif">';
					},					
					onSuccess: function(resp) {

						if(resp.responseText ==1) document.getElementById('div-statusafiliado').innerHTML = '<?=_("ACTIVO")?>';
						
						document.getElementById('div-activacion').innerHTML= '<img src="../../../../imagenes/iconos/succes.gif" align="middle">&nbsp;SE ACTIVO CORRECTAMENTE AL AFILIADO.';
						document.getElementById('div-activacion').style.backgroundColor= '#BBFFBB';
						document.getElementById('div-activacion').style.border = 'solid 2px #5EFF5E';
						setTimeout("document.getElementById('div-activacion').style.display ='none'",6000);
						
					}				 
					 
				});
				
			} else{
				$('cbkactivar').checked=false;
			}
		}
	} */
	</script>