<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 	Auth::required();
	
	validar_permisos("MENU_SAC",1);
	
	$acciongen="showFormulario('GENERAL','','',true);document.form1.btngenera.disabled=true;document.form1.btnreitegro.disabled=false;document.form1.btnbaja.disabled=false;";
	$accionrei="showFormulario('REINTEGRO','','',true);document.form1.btnreitegro.disabled=true;document.form1.btngenera.disabled=false;document.form1.btnbaja.disabled=false";		
	$accionrea="''";
	$accionbaj="showFormulario('BAJAS','','',true);document.form1.btnbaja.disabled=true;document.form1.btngenera.disabled=false;document.form1.btnreitegro.disabled=false";
	$accioncam="''";
	
//verificar permisos de accesos a las cuentas
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);	

//cantidad de ubigeo
    $nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=_("SAC - Nuevo Afiliado");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">	
	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />	
	
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>	
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
	<style type="text/css">
	<!--
	.style5 {font-weight: bold}
	.style6 {color: #333333}
	.style7 {color: #FFFFFF}
	.style8 {color: #666666}
	-->
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
		
	</script>
	<script type="text/javascript">

		function validarIngreso(valors){
		
		document.form1.txtidentificador.value=document.form1.txtidentificador.value.replace(/^\s*|\s*$/g,"");	
		
		if(document.form1.cmbopciones.options[document.form1.cmbopciones.selectedIndex].text =='VICIO'){
			
			document.form1.ckbvicio.value=1;
			
			if(document.form1.txtacomentario.value==""){
				  alert("INGRESE LA OBSERVACION.");
				  document.form1.txtacomentario.focus();
				  return (false);			 
			}
        }
		else{
			   
			if(document.form1.txtidentificador.value==""){
					  alert("INGRESE EL CODIGO DE IDENTIFICACION.");
					  document.form1.txtidentificador.focus();
					  return (false);
			   } 				
			else if(document.form1.cmbcuenta.value==""){
					  alert("SELECCIONE ALGUN CUENTA.");
					  document.form1.cmbcuenta.focus();
					  return (false);
			   } 	
			 else if(document.form1.cmbprograma.value==""){
					  alert("SELECCIONE EL PLAN.");
					  document.form1.cmbprograma.focus();
					  return (false);
			   }	
			else if(document.form1.txtnombres.value==""){
					  alert("INGRESE EL NOMBRE.");
					  document.form1.txtnombres.focus();
					  return (false);
			   }  
			else if(document.form1.txtpaterno.value==""){
					  alert("INGRESE EL APELLIDO PATERNO.");
					  document.form1.txtpaterno.focus();
					  return (false);
			   } 	    
			else if(document.form1.txtacomentario.value==""){
						  alert("INGRESE LA OBSERVACION.");
						  document.form1.txtacomentario.focus();
						  return (false);			 
			   }
		}	



		  if(confirm('<?=_("REALMENTE DESEA PROSEGUIR CON EL INGRESO DEL NUEVO REGISTRO?.") ;?>'))
		   {
				document.form1.action="gnewafiliado.php" ;
				document.form1.submit();
		   }
		   
			return (false);
	}
			
	</script>
</head>
<body onload="showCombo('');showFormulario('GENERAL','','',true)">
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
	<div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><span class="current"><?=_("Contacto No V&aacute;lido") ;?></span><a href="reportes.php"><?=_("Reporte") ;?></a><a href="estadisticas.php"><?=_("Estad&iacute;stica") ;?></a></div>

<form id="form1" name="form1" method="post" action="" onSubmit="return validarIngreso(this)">

<input type="hidden" name="ckbvicio" id="ckbvicio" />
<h2 class="Box"><?=_("NUEVO AFILIADO") ;?></h2>
	
	<table width="99%" border="0" cellpadding="1" cellspacing="1" bgcolor="#F8F8F8" style="border:1px solid #EEEEEE">
		<tr>
		  <td colspan="4"></td>
		  <td><?=_("MODALID. PG") ;?></td>
		  <td colspan="3">
			<?	$con->cmb_array("cmbmodalidad",$modalidad_pg,""," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'",""); ?>	  </td>
		</tr>
		<tr>
			<td><?=_("CODIGO ID.") ;?></td>       
			<td colspan="3"><input name="txtidentificador" type="text" class="classtexto" id="txtidentificador"  style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);quitarcaracter(document.form1.txtidentificador)" size="28" maxlength="25"  /></td>
			<td><?=_("STATUS") ;?></td>
			<td colspan="3"><?
					$sql="select IDCUENTA,NOMBRE from catalogo_cuenta WHERE ACTIVO=1 order by NOMBRE";
					$con->cmb_array("cmbstatus",$desc_statusAfiliado,"CAN"," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
			?></td>
		  </tr>
		<tr>
		  <td><?=_("INI. VIGENCIA") ;?></td>
		  <td colspan="3"><input name="txtfechaini" id="txtfechaini" type="text" class="classtexto"  readonly style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);quitarcaracter(document.form1.txtidentificador)" size="14"/><button type="reset" id="f_trigger_b">...</button></td>
		  <td><?=_("FIN VIGENCIA") ;?></td>
		  <td colspan="3"><input name="txtfechafin" id="txtfechafin" type="text" class="classtexto"  readonly style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);quitarcaracter(document.form1.txtidentificador)" size="14"/>
		  <button type="reset" id="f_trigger_b2">...</button></td>
		</tr>	  
		<? include_once("cargar_combos.php"); ?>
		<tr>
			<td><span class="style6">
			  <?=_("NOMBRES") ;?>
			</span></td>
			<td width="245"><input name="txtnombres" type="text" class="classtexto" id="txtnombres" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="35" maxlength="37" /></td>
			<td width="49"><?=_("APE.PATERNO") ;?></td>
			<td width="146"><input name="txtpaterno" type="text" class="classtexto" id="txtpaterno" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="20" maxlength="22"  /></td>
			<td><?=_("APE.MATERNO") ;?></td>
			<td colspan="3"><input name="txtmaterno" type="text" class="classtexto" id="txtmaterno" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="20" maxlength="22"  /></td>
		  </tr>
		  <tr>
			<td><?=_("#DOCUMENTO") ;?></td>
			<td><input name="txtndocumento" type="text" class="classtexto" id="txtndocumento" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="15" maxlength="17"  /></td>
			<td><?=_("TIPODOC") ;?></td>
			<td><?
					$sql="select IDTIPODOCUMENTO,DESCRIPCION from catalogo_tipodocumento order by DESCRIPCION";
					$con->cmbselectdata($sql,"cmbtipodoc","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
			?></td>
			<td>
			  <?=_("GENERO") ;?>        </td>
			<td colspan="3"><select name="cmbgenero" id="cmbgenero" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" >
			  <option value="M"><?=_("MASCULINO") ;?></option>
			  <option value="F"><?=_("FEMENINO") ;?></option>
			</select></td>
		  </tr>
		<? include("frm_telefono.php");?>
		  <tr>
			<td><?=_("EMAIL1") ;?></td>
			<td><input name="txtemail" type="text" class="classtexto" id="txtemail" onfocus="coloronFocus(this);" onblur="colorOffFocus(this); isEmail(document.form1.txtemail);" size="35" maxlength="70"  /></td>
			<td><?=_("EMAIL2") ;?></td>
			<td colspan="3"><input name="txtemail2" type="text" class="classtexto" id="txtemail2" onfocus="coloronFocus(this);" onblur="colorOffFocus(this); isEmail(document.form1.txtemail2);" size="35" maxlength="70"  /></td>
			<td width="51"><?=_("EMAIL3") ;?></td>
			<td colspan="3"><input name="txtemail3" type="text" class="classtexto" id="txtemail3" onfocus="coloronFocus(this);" onblur="colorOffFocus(this); isEmail(document.form1.txtemail3);" size="35" maxlength="70"  /></td>
		  </tr>
	   <? include("../../includes/vista_entidades2.php");?>
			<input type='hidden'  name='LATITUD' id='latitud'  value = "" >
			<input type='hidden'  name='LONGITUD' id='longitud' value = "" >
		  <tr>
			<td><?=_("DIRECCION") ;?></td>
			<td colspan="3"><input type="text" name='DIRECCION' value="" id='direccion' size='60' autocomplete="off" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" ></td>
			<div id='sugeridos' class="autocomplete" style="display:none" ></div>
			<td><?=_("COD.POSTAL") ;?></td>
			<td width="182" colspan="3"><input name="txtcodpostal" type="text" class="classtexto" id="txtcodpostal" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="10" maxlength="10"  /></td>		
		   </tr>
	</table>
	<script type="text/javascript">
			Calendar.setup({
			inputField     :    "txtfechaini",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
	</script>	
	<script type="text/javascript">
			Calendar.setup({
			inputField     :    "txtfechafin",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b2",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
	</script>	
 		 
		  <table width="200" border="0" cellpadding="1" cellspacing="1" >
		  <tr>
			<td>
			<input type="button" name="btngenera" id="btngenera" value="GENERALIDADES" style="font-size:10px" disabled onclick=<?=$acciongen;?> /></td>
			<td><input type="button" name="btnreitegro" id="btnreitegro" value="REINTEGRO" style="font-size:10px" onclick=<?=$accionrei;?> /></td>
			<td><input type="button" name="btnreactivar" id="btnreactivar" value="REACTIVACION" disabled style="font-size:10px"   /></td>
			<td><input type="button" name="btnbaja" id="btnbaja" value="BAJA DEL SERVICIO " style="width:130px;font-size:10px;" onclick=<?=$accionbaj;?>  /></td>
			<td><input type="button" name="btncambio" id="btncambio" value="CAMBIO DE PROGRAMA" style="width:145px;font-size:10px" disabled /></td>
			<td><input type="button" name="btnquejasre" id="btnquejasre" value="RECLAMOS" style="width:90px;font-size:10px" disabled /></td>
			<td><input type="button" name="btnfpago" value="MEDIO DE PAGO" style="width:110px;font-size:10px" disabled /></td>
			<td><input type="button" name="btnbeneficiario" value="BENEFICIARIO" style="width:100px;font-size:10px" disabled /></td>
			<td><input type="button" name="btnvehicular" value="VEHICULOS" style="width:80px;font-size:10px"disabled /></td>
		  </tr>
		</table>
			<div id="tipogestion" style="display:none"></div>

	<div align="right">
		<input type="submit" name="btngrabar" id="btngrabar" value=">>> REGISTRAR" style="font-weight:bold;width:200px;font-size:10px"/>
	</div>
		  
</form>

	<script type="text/javascript">	
	
// **********************  Ajax para autocompletar las calles ********************************//
	new Ajax.Autocompleter('direccion',	'sugeridos',
	"../../../controlador/ajax/ajax_calles.php",
	{
		method: "get",
		paramName: "calle",
		callback: function(editor, paramText){
			parametros = "&cveentidad1="+ $F('cveentidad1')
			+"&cveentidad2="+$F('cveentidad2')
			+"&cveentidad3="+$F('cveentidad3')
			//+"&cveentidad4="+$F('cveentidad4')
			//+"&cveentidad5="+$F('cveentidad5')
			//+"&cveentidad6="+$F('cveentidad6')
			//+"&cveentidad7="+$F('cveentidad7');
			return  paramText+parametros;
		},
		afterUpdateElement: function(text,li){
			var coordenadas = li.id.split(',');
			$('latitud').value=coordenadas[0];
			$('longitud').value=coordenadas[1];
//			$('cvetipovia').value=coordenadas[2];


		},
		minChars: 2,

		selectFirst: true
	}
	);
	
	</script>	
</body>
</html>