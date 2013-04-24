<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");

	$con = new DB_mysqli();	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
 
  	session_start(); 
 	Auth::required();	
?>
<html>
	<head>
		<title><?=_("American Assist") ;?></title> 
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DEL GRUPO") ;?>.');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}
				
				return (true);
			}
			
		</script>
	
	</head>
	<body onLoad="document.frmaddregistro.txtnombre.focus();">
		<form name="frmaddregistro" action="grabar_grupo.php" method="POST" onSubmit = "return validarCampo(this)" >
			<table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
				<tr bgcolor="#333333">
					<th style="text-align:left"><?=_("AGREGAR GRUPO") ;?></th>
				</tr>
				<tr class='modo1'>
					<td><?=_("NOMBRE") ;?></td>
					<td><input name="txtnombre" id="txtnombre" type="text" size="40" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;" ></td>
				</tr>
				
				<tr class='modo1'>
					<td><?=_("ACTIVADO") ;?></td>
					<td><input type="checkbox" name="chkactivo" id="checkbox"  checked="checked" value="1" /></td>	
				</tr>
				<tr class='modo1'>				
					<td align="right"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR GRUPO') ;?>" >&nbsp;</td>
					<td><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>				
				</tr>
			</table>            
				<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">					
		</form>		
	</body>
</html>