<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");

	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	Auth::required($_SERVER['REQUEST_URI']);
?>
<html>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>

		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE LA DESCRIPCION DEL COSTO.") ;?>');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}
	
				return (true);
			}		
		</script>
	</head>
	<body onLoad="document.frmaddregistro.txtnombre.focus();show_info(document.frmaddregistro.chkcostonegocio.checked ,'frm_datos_negociado.php','')">
	<form name="frmaddregistro" action="grabar_costo.php" method="POST" onSubmit = "return validarCampo(this)" >
			<input name="idservicio" type="hidden" value=""/>
			<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="90%" class="catalogos">
				<tr bgcolor="#333333">
					<th style="text-align:left"><?=_("AGREGAR COSTO") ;?></th>
				</tr>
				<tr class='modo1'>
					<td><?=_("DESCRIPCION") ;?></td>
					<td><input type="text" name="txtnombre" size="30" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;"  ></td>
				</tr>
				<tr class='modo1'>
				  <td><?=_("CARGO A CUENTA") ;?></td>
				  <td>
					<?
						$con->cmb_array("cmbcargoacuenta",$desc_cargoacuenta,"","class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
					?>				</td>
				</tr>			
				<tr class='modo1'>
				  <td><?=_("COSTO NEGOCIADO") ;?></td>
				  <td><input type="checkbox" name="chkcostonegocio" id="chkcostonegocio" value="1" checked onclick="show_info(this.checked ,'frm_datos_negociado.php','')"></td>
				</tr>	
				<tr class='modo1'>
				  <td></td>
		          <td><div id="verdatonegociado" style="display: none;"></div></td>
		  </tr>
				<tr class='modo1'>
				  <td><?=_("STATUS") ;?></td>
				  <td><input type="checkbox" name="chkactivo" id="chkactivo" value="1"  checked></td>
				</tr>		  
				</tr>	  
				<tr class='modo1'>
					<td><div align="right">
					  <input type="button" class="botonstandar" value="<?=_("CANCELAR") ;?> " onClick="reDirigir('general.php')" title="<?=_("CANCELAR") ;?>">
					</div></td>
				  <td><input name="Submit"  class="botonstandar" type="submit" value="<?=_("AGREGAR") ;?>" title="<?=_("AGREGAR COSTO") ;?>" ></td>
				</tr>
            </table>
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
	</form> 
	</body>
 </html>