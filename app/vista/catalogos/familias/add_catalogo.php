<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
	$db= $con->catalogo;
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();
 	Auth::required($_SERVER['REQUEST_URI']);
	
	$con->select_db($bd);
?>
<html>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>

		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE LA DESCRIPCION DE LA FAMILIA.") ;?>');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}			
			
				return (true);
			}		
		</script>	
		
			<script language="JavaScript">
				
				function validarCampo(variable){
				
					document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
					if(document.frmaddregistro.txtnombre.value =='' ){
						alert('<?=_("INGRESE LA DESCRIPCION DE LA FAMILIA.") ;?>');
						document.frmaddregistro.txtnombre.focus();
						return (false);
					}
					
					return (true);
				}				
			</script>
			<script language="javascript" type="text/javascript">

				function shouldset(passon){
				if(document.frmaddregistro.txtcolor.value.length == 7){setcolor(passon)}
				}

				function setcolor(elem){
					document.frmaddregistro.txtcolor.value=elem
					document.frmaddregistro.selcolor.style.backgroundColor=elem
					comportamientoDiv('-','colores');					 
				}
			</script>
			
	</head>
	<body onLoad="document.frmaddregistro.txtnombre.focus();">
		<form name="frmaddregistro" action="grabar_familia.php" method="POST" onSubmit = "return validarCampo(this)" >
			<input name="idservicio" type="hidden" value="" />
			<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
			<table border="0" cellpadding="1" cellspacing="1" width="90%" class="catalogos">
				<tr bgcolor="#333333">
					<th style="text-align:left"><?=_("AGREGAR FAMILIA") ;?></th>
				</tr>
				<tr class='modo1'>
					<td><?=_("DESCRIPCION") ;?></td>
					<td><input type="text" name="txtnombre" size="30" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;"  ></td>
				</tr>	
				<tr class='modo1'>
					<td><?=_("COLOR") ;?></td>
					<td><input type="text" name="txtcolor" size="10" maxlength="7" onFocus="coloronFocus(this);comportamientoDiv('+','colores')" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;" ><input style="border-color: #8ba3b9;border-style: dashed;border-width: 1px;color: #333333;font-size: 10px;font-family: Verdana, Arial, Helvetica, sans-serif;" name="selcolor" size="5"   class="formu2" onfocus="this.blur()" type="text" ></td>
				</tr>				
				<tr class='modo1'>
				  <td><?=_("STATUS") ;?></td>
				  <td><input type="checkbox" name="chkactivo" id="chkactivo" value="1"  checked></td>
				</tr>		  
				</tr>	  
				<tr class='modo1'>
					<td><div align="right">
					  <input type="button" value="<?=_("CANCELAR") ;?> " onClick="reDirigir('general.php')" title="<?=_("CANCELAR") ;?>" style="font-size:10px;" >
					</div></td>
				  <td><input name="Submit" type="submit" value="<?=_("AGREGAR") ;?>" title="<?=_("AGREGAR FAMILIA") ;?>" style="font-size:10px; font-weight:bold" ></td>
				</tr>
            </table>
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
	<div id="colores" style="display:none;margin:1px;padding:1px;float:left;position:absolute;top:10px;left:425px;width:200px;height:50px;" >
		 <? require_once("paletacolores.html")?>
	</div>			
		</form> 
	</body>
 </html>