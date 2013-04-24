<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	 $con->select_db($con->catalogo);	
		
 	session_start();
	
	Auth::required($_SERVER['REQUEST_URI']);
	
	validar_permisos("MENU_SAC",1);
 
//verificar permisos de accesos a las cuentas	
	list($allcuentas,$ver_cuentas,$ids)=accesos_cuentas($_SESSION["user"]);	

//cantidad de ubigeo
    $nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=_("SAC - Nuevo Afiliado");?></title> 
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
		
		var mensaje='';
		$('ckbvicio').value='';
		document.frmNewAfiliado.txtidentificador.value=document.frmNewAfiliado.txtidentificador.value.replace(/^\s*|\s*$/g,"");	
		
		if(document.frmNewAfiliado.cmbopciones.options[document.frmNewAfiliado.cmbopciones.selectedIndex].text =='VICIO'){
			
			$('ckbvicio').value=1;
			
			if(document.frmNewAfiliado.txtacomentario.value==""){
				  alert("INGRESE LA OBSERVACION.");
				  document.frmNewAfiliado.txtacomentario.focus();
				  return (false);			 
			}
			
        } else{

			if(document.frmNewAfiliado.txtidentificador.value==""){
				  alert("INGRESE LA CLAVE DE IDENTIFICACION DEL AFILIADO.");
				  document.frmNewAfiliado.txtidentificador.focus();
				  return (false);
			}			   
	/* 
			if($('ckbvip')){
				
				if($('ckbvip').checked){
					
					if(document.frmNewAfiliado.cmbcuentavip.value==""){
						  alert("SELECCIONE ALGUNA CUENTA.");
						  document.frmNewAfiliado.cmbcuentavip.focus();
						   
						  return (false);
					}
				} else{
					if(document.frmNewAfiliado.cmbcuenta.value==""){
						  alert("SELECCIONE ALGUNA CUENTA.");
						  document.frmNewAfiliado.cmbcuenta.focus();
						  return (false);
					}			
				}			
			} else{
				
				if(document.frmNewAfiliado.cmbcuenta.value==""){
					  alert("SELECCIONE ALGUNA CUENTA.");
					  document.frmNewAfiliado.cmbcuenta.focus();
					  return (false);
				}	
			} */ 		
		 
			if(document.frmNewAfiliado.cmbcuenta.value==""){
					  alert("SELECCIONE ALGUNA CUENTA.");
					  document.frmNewAfiliado.cmbcuenta.focus();
					  return (false);
			}
			else if(document.frmNewAfiliado.cmbprograma.value==""){
					  alert("SELECCIONE EL PLAN.");
					  document.frmNewAfiliado.cmbprograma.focus();
					  return (false);
			} else if(document.frmNewAfiliado.txtnombres.value==""){
					  alert("INGRESE EL NOMBRE.");
					  document.frmNewAfiliado.txtnombres.focus();
					  return (false);
			} else if(document.frmNewAfiliado.txtpaterno.value==""){
					  alert("INGRESE EL APELLIDO PATERNO.");
					  document.frmNewAfiliado.txtpaterno.focus();
					  return (false);
			} else if(document.frmNewAfiliado.cmbasignacionc && document.frmNewAfiliado.cmbasignacionc.value==""){
					alert("ASIGNE EL AREA RESPONSABLE DEL CASO.");
					document.frmNewAfiliado.cmbasignacionc.focus();
				 return (false);		
			} else if(document.frmNewAfiliado.txtacomentario.value==""){
						  alert("INGRESE LA OBSERVACION.");
						  document.frmNewAfiliado.txtacomentario.focus();
						  return (false);			 
			}
			
			//if(!$('ckbvip').checked){
				if($('cmbstatusys').value =='VALIDADO') mensaje='\n *** STATUS SISTEMA: '+ $('cmbstatusys').value+' ,ESTE AFILIADO SERA HABILITADO PARA LA BUSQUEDA EN EL EXPEDIENTE.***'; else mensaje='\n *** STATUS SISTEMA: '+ $('cmbstatusys').options[$('cmbstatusys').selectedIndex].text +' ,ESTE AFILIADO NO SERA HABILITADO PARA LA BUSQUEDA EN EL EXPEDIENTE.***';
			//}
		}		

		if(confirm('<?=_("REALMENTE DESEA PROSEGUIR CON EL INGRESO DEL NUEVO REGISTRO?.") ;?>\n'+mensaje)){
			
			$('btngrabar').value='PROCESANDO...';
			$('btngrabar').disabled=true;
			
			new Ajax.Request('gnewafiliado.php',{
				method: 'post',
				parameters : $('frmNewAfiliado').serialize(true),
				onSuccess: function(resp){

					var elemento= resp.responseText;					

				 	if(elemento==1){
						alert('!!" SE GENERO EL NUEVO AFILIADO SATISFACTORIAMENTE, CVEAFILIADO: '+$('txtidentificador').value.toUpperCase());
						reDirigir('buscarafiliado.php');
					} else{
						reDirigir('newafiliado.php');
					}					 
				},
				onFailure: function(){
					alert('ERROR, NO SE HA REALIZADO LA OPERACION.'); 
					reDirigir('newafiliado.php');
				}
			});		
		}
		   
		return (false);
	}
	
//cambio cuenta-plan
	function cambio_cuentaplan(valor){
 
		new Ajax.Updater('cambio_plan','mostrarprograma.php',{
			parameters:{
				opc:valor
			},
			method: 'post'		 
			 
		});			
	}
	
//cambio cuenta plan VIP
/*
	function cambio_afiliadovip(valor){
		
		if(valor && (document.frmNewAfiliado.cmbopciones.options[document.frmNewAfiliado.cmbopciones.selectedIndex].text !='VICIO')){
			
			$('cambio_cuenta').style.display='none';
			$('cambio_cuentavip').style.display='block';
			$('cmbcuenta').selectedIndex=0;
			$('cmbstatusys').selectedIndex=0;
			$('cmbstatusys')[1].disabled=true;
			
 
			cambio_cuentaplan('');
		} else{
			
			$('cambio_cuentavip').style.display='none';
			$('cambio_cuenta').style.display='block';
			$('cmbcuentavip').selectedIndex=0;
			$('cmbstatusys')[1].disabled=false;
			$('ckbvip').checked=false;
			
			cambio_cuentaplan('');
		}	 	
	}	
*/
//cambio gestion
	function cambio_gestion(dato1,dato2){
 
		new Ajax.Updater('tipogestion','tipogestion.php',{
			parameters:{
				opc:dato1,
				opc2:dato2
			},
			method: 'post',
			onSuccess: function(resp){
				if(dato1 =='GENERAL'){
					$('btngenera').disabled=true;
					$('btnreitegro').disabled=false;		 
					$('btnreclamos').disabled=false;		 
				
				} else if(dato1 =='REINTEGRO'){
					$('btngenera').disabled=false;
					$('btnreitegro').disabled=true;
					$('btnreclamos').disabled=false;
					
				} else{
					$('btngenera').disabled=false;
					$('btnreitegro').disabled=false;
					$('btnreclamos').disabled=true;					
				}	 
			}			
		});			
	}	
	</script>
</head>
<body onload="cambio_gestion('GENERAL',1)">
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
	<div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><span class="current"><?=_("Contacto No V&aacute;lido") ;?></span><a href="reportes.php"><?=_("Reporte") ;?></a><a href="estadisticas.php"><?=_("Estad&iacute;stica") ;?></a></div>

	<h2 class="Box"><?=_("NUEVO AFILIADO");?></h2>

	<form id="frmNewAfiliado" name="frmNewAfiliado" method="post" action="">

		<input type="hidden" name="ckbvicio" id="ckbvicio"/>
		<input  type="hidden" name="txturlacces"id="txturlacces" value="<?=urlencode($_SERVER['REQUEST_URI'])?>"/>
		
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#c4c4c4" style="border:2px solid #9fd1ff">
			<tr>
				<? 	/*if(validar_permisos("CREARCLIENTE_VIP",0)){ ?>
				<td><?=_("VIP");?></td>
				<td><input type="checkbox" name="ckbvip" id="ckbvip" title="Registrar Cliente VIP" onclick="cambio_afiliadovip(this.checked)"/></td>
				<? } else{ ?>
				<td colspan="2"></td>
				<? } */?>
				<td colspan="2"></td>
				<td><strong><?=_("CODIGO ID.");?></strong></td>
				<td><input name="txtidentificador" type="text" class="classtexto" id="txtidentificador"  style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this);quitarcaracter(document.frmNewAfiliado.txtidentificador);" size="28" maxlength="25"  /></td>
				<td><strong><?=_("STATUS AFIL.");?></strong></td>
				<td>
					<?			 
						$con->cmb_array("cmbstatus",$desc_statusAfiliado,"ACT"," $disabled title='Status Afiliado.' class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
					?>
				</td>
				<td><?=_("MODALID. PG");?></td>
				<td>
					<?	$con->cmb_array("cmbmodalidad",$modalidad_pg,""," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'",""); ?>	  </td>
				</td>
			</tr>
			<tr>
				<td><strong><?=_("CUENTA");?></strong></td>
				<td>
					<div id="cambio_cuenta">
						<?							
							if($allcuentas==1)	$sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";							
							$con->cmbselectdata($sql_cuenta,"cmbcuenta","","onChange='cambio_cuentaplan(this.value)' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
						?>
					</div>					
				</td>
				<td><strong><?=_("PLAN");?></strong></td>
				<td>			 		
					<div id="cambio_plan">
						<select name="cmbprograma" id="cmbprograma" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
							<option value=""><?=_("CUENTA");?></option>
						</select>
					</div>
				</td>
				<td><?=_("INI. VIGENCIA");?></td>
				<td><input name="txtfechaini" id="txtfechaini" type="text" class="classtexto"  readonly style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);quitarcaracter(document.frmNewAfiliado.txtidentificador)" size="14"/><button type="reset" id="f_trigger_b">...</button></td>
				<td><?=_("FIN VIGENCIA");?></td>
				<td><input name="txtfechafin" id="txtfechafin" type="text" class="classtexto"  readonly style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);quitarcaracter(document.frmNewAfiliado.txtidentificador)" size="14"/><button type="reset" id="f_trigger_b2">...</button></td>
			</tr>
			<tr>
				<td><strong><?=_("NOMBRES");?></strong></td>
				<td><input name="txtnombres" type="text" class="classtexto" id="txtnombres" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="35" maxlength="37" /></td>
				<td><strong><?=_("APE.PATERNO");?></strong></td>
				<td><input name="txtpaterno" type="text" class="classtexto" id="txtpaterno" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="35" maxlength="37" /></td>
				<td><?=_("APE.MATERNO");?></td>
				<td><input name="txtmaterno" type="text" class="classtexto" id="txtmaterno" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="35" maxlength="37" /></td>
				<td><?=_("GENERO");?></td>
				<td>
					<select name="cmbgenero" id="cmbgenero" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" >
						<option value=""><?=_("SELECCIONE") ;?></option>
						<option value="M"><?=_("MASCULINO") ;?></option>
						<option value="F"><?=_("FEMENINO") ;?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=_("#DOCUMENTO");?></td>
				<td><input name="txtndocumento" type="text" class="classtexto" id="txtndocumento" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="22" maxlength="23"  /></td>
				<td><?=_("TIPODOC");?></td>
				<td>
					<?
						$sql="select IDTIPODOCUMENTO,DESCRIPCION from catalogo_tipodocumento order by DESCRIPCION";
						$con->cmbselectdata($sql,"cmbtipodoc","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
					?>
				</td>
				<td><?=_("EMAIL1");?></td>
				<td><input name="txtemail" type="text" class="classtexto" id="txtemail" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this); isEmail(document.frmNewAfiliado.txtemail);" size="35" maxlength="70"  /></td>
				<td><?=_("EMAIL2");?></td>
				<td><input name="txtemail2" type="text" class="classtexto" id="txtemail2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this); isEmail(document.frmNewAfiliado.txtemail2);" size="35" maxlength="70"  /></td>
			</tr>
		  
			<tr>
				<td><?=_("TELEFONO1");?></td>
				<td><input name="txttelefono[]" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txttelefono" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[1];?>" /><?
							$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtelefono0",$tipotelefono[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
						?><input type="button" name="btnver1" title="Mas..." id="btnver1" value="V" style="font-weight:bold;width:30px;font-size:10px;" onClick="verificardiv('telefono1',this.value,this.name);" /><? if($telefono[1]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onClick="llamada('<?=$codigoa[1];?>','<?=$telefono[1];?>')" title="Llamar" /><? }?></td>
				<td><?=_("TELEFONO2");?></td>
				<td><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono" <?=($_GET["verinfo"])?$readonly:"" ;?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[2];?>"/><?
							$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtelefono1",$tipotelefono[2]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
					?><input type="button" name="btnver2" title="Mas..." id="btnver2" value="V" style="font-weight:bold;width:30px;font-size:10px;"  onclick="verificardiv('telefono2',this.value,this.name);" /> <? if($telefono[2]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onClick="llamada('<?=$codigoa[1];?>','<?=$telefono[2];?>')" title="Llamar" /><? }?></td>
				<td><?=_("TELEFONO3");?></td>
				<td><input name="txttelefono[]" type="text" class="classtexto" id="txttelefono"  <?=($_GET["verinfo"])?$readonly:"" ;?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[3];?>" /><?
						$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
						$con->cmbselectdata($sql,"cmbtelefono2",$tipotelefono[3]," $disabled onchange=\"verificardiv('+','telefono3',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
					?><input type="button" name="btnver3" title="Mas..." id="btnver3" value="V" style="font-weight:bold;width:30px;font-size:10px;"  onclick="verificardiv('telefono3',this.value,this.name);" /><? if($telefono[3]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onClick="llamada('<?=$codigoa[3];?>','<?=$telefono[3];?>')" title="Llamar" /><? }?></td>
				<td><?=_("TELEFONO4");?></td>
				<td><span class="style5">
					  <input name="txttelefono[]" type="text" class="classtexto" id="txttelefono" style="text-transform:uppercase;" <?=($_GET["verinfo"])?$readonly:"" ;?> onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[4];?>"/></span><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
						$con->cmbselectdata($sql,"cmbtelefono3",$tipotelefono[4]," $disabled onchange=\"verificardiv('+','telefono4',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
				?><input type="button" name="btnver4" title="Mas..." id="btnver4" value="V" style="font-weight:bold;width:30px;font-size:10px;" onClick="verificardiv('telefono4',this.value,this.name);" /><? if($telefono[4]!=""){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onClick="llamada('<?=$codigoa[4];?>','<?=$telefono[4];?>')" title="Llamar" /><? }?></td>
			</tr>
			<tr>
				<td colspan="2">	
				<div id='telefono1' style="display: none;border:1px dashed #ff8080;background-color:#ffe7d1">
					&nbsp;<strong><?=_("COD.AREA");?></strong>&nbsp;&nbsp;<input name="txtcodigoa0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[1];?>"/> <br/>
					&nbsp;<strong><?=_("PROVEEDOR TELEFONIA");?></strong>&nbsp;
						<?
							$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtsp0",$tsp[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
						?>				 
					<br/>&nbsp;<strong><?=_("EXTENSION");?></strong>&nbsp;<input name="txtextension0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[1];?>" />
				</div>		
				</td>  
				<td colspan="2">	
				<div id='telefono2' style="display: none;border:1px dashed #ff8080;background-color:#ffe7d1">
					&nbsp;<strong><?=_("COD.AREA");?></strong>&nbsp;&nbsp;<input name="txtcodigoa0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[1];?>"/> <br/>
					&nbsp;<strong><?=_("PROVEEDOR TELEFONIA");?></strong>&nbsp;
						<?
							$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtsp1",$tsp[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
						?>		 													
					<br/>&nbsp;<strong><?=_("EXTENSION");?></strong>&nbsp;<input name="txtextension0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[1];?>" />
				</div>		
				</td>    
				<td colspan="2">	
					<div id='telefono3' style="display: none;border:1px dashed #ff8080;background-color:#ffe7d1">
					&nbsp;<strong><?=_("COD.AREA");?></strong>&nbsp;&nbsp;<input name="txtcodigoa0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[1];?>"/> <br/>
					&nbsp;<strong><?=_("PROVEEDOR TELEFONIA");?></strong>&nbsp;
						<?
							$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtsp2",$tsp[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
						?>				
					<br/>&nbsp;<strong><?=_("EXTENSION");?></strong>&nbsp;<input name="txtextension0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[1];?>" />
					</div>		
				</td>   
				<td colspan="2">	
				<div id='telefono4' style="display: none;border:1px dashed #ff8080;background-color:#ffe7d1">
					&nbsp;<strong><?=_("COD.AREA");?></strong>&nbsp;&nbsp;<input name="txtcodigoa0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[1];?>"/> <br/>
					&nbsp;<strong><?=_("PROVEEDOR TELEFONIA");?></strong>&nbsp;
						<?
							$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
							$con->cmbselectdata($sql,"cmbtsp3",$tsp[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
						?>					
					<br/>&nbsp;<strong><?=_("EXTENSION");?></strong>&nbsp;<input name="txtextension0" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension0" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[1];?>" />
				</div>		
				</td> 
			</tr>
			<? include("../../includes/vista_entidades2.php");?>
			<input type='hidden'  name='LATITUD' id='latitud' value = "" >
			<input type='hidden'  name='LONGITUD' id='longitud' value = "" >
			<tr>
				<td><?=_("DIRECCION") ;?></td>
				<td colspan="5">
					<input type="text" name='DIRECCION' value="" id='direccion' size='80' autocomplete="off" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" >
					<div id='sugeridos' class="autocomplete" style="display:none" >
				</td>
				<td><strong><?=_("STATUS SISTEMA") ;?></strong></td>
				<td>
					<select name="cmbstatusys" id="cmbstatusys" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
						<? if(validar_permisos("STATUS_SISTEMA_AFILIADO")){ ?><option value="VALIDADO"><?=_("VALIDADO");?></option><?}?>
						<option value="SINVALIDAR" selected><?=_("SIN VALIDAR");?></option>
					</select>
				</td>
			</tr>
		</table>
		<table width="200" border="0" cellpadding="1" cellspacing="1">
			<tr>
				<td><input type="button" name="btngenera" id="btngenera" value="GENERALIDADES" style="font-size:11px;font-weight:bold" disabled onclick="cambio_gestion('GENERAL',1)"/></td>
				<td><input type="button" name="btnreitegro" id="btnreitegro" value="REINTEGRO" style="font-size:11px;font-weight:bold" onclick="cambio_gestion('REINTEGRO',1)"/></td>
				<td><input type="button" name="btnreclamos" id="btnreclamos" value="RECLAMOS" style="font-size:11px;font-weight:bold" onclick="cambio_gestion('QUEJASRECLAMO',1)"/></td>	
			</tr>
		</table>		
 	<div id="tipogestion" ></div>	 
		 
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
		 
	<div align="right">
		<input type="button" name="btngrabar" id="btngrabar" value=">>> <?=_("REGISTRAR");?> <<<" style="font-weight:bold;font-size:14px;height:55px;" onclick="validarIngreso()" title="Registrar Nuevo Afiliado"/>
	</div>
	
	</form>

	<script type="text/javasc+ript">	
	
// **********************  Ajax para autocompletar las calles ********************************//
  	new Ajax.Autocompleter('direccion',	'sugeridos',"../../../controlador/ajax/ajax_calles.php",{
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
	});	 
	</script>	
</body>
</html>