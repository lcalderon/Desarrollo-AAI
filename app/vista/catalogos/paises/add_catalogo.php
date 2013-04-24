<?php
	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
			
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
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>		
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				document.frmaddregistro.txtcodigo.value=document.frmaddregistro.txtcodigo.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmaddregistro.txtcodigo.value =='' ){
					alert('<?=_("INGRESE EL CODIGO DEL PAIS") ;?>');
					document.frmaddregistro.txtcodigo.focus();
					return (false);
				}
				else if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DEL PAIS") ;?>');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}	

				document.frmaddregistro.txtnombre.value = document.frmaddregistro.txtnombre.value.toUpperCase();
				document.frmaddregistro.txtcodigo.value = document.frmaddregistro.txtcodigo.value.toUpperCase();
				
				return (true);
			}
			
		</script>
	
	</head>
	<body onLoad="document.frmaddregistro.txtcodigo.focus();">
	<form name="frmaddregistro" action="grabar_pais.php" method="POST" onSubmit = "return validarCampo(this)" >
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
			<tr bgcolor="#333333">
				<th style="text-align:left"><?=_("AGREGAR PAIS") ;?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("CODIGO") ;?></td>			
				<td><input name="txtcodigo" type="text" id="txtcodigo" size="3" maxlength="2" onFocus="coloronFocus(this);clear_all(this.id)" onBlur="colorOffFocus(this);clear_all(this.id)" class="classtexto" style="text-transform:uppercase;" ></td> 				
			</tr>
			<tr class='modo1'>
				<td><?=_("NOMBRE") ;?></td>
				<td><input name="txtnombre" id="txtnombre" type="text" size="40" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>			
			<tr class='modo1'>				
				<td align="right"><input  type="button" class="botonstandar" value="<?=_('CANCELAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
				<td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PAIS') ;?>" >&nbsp;</td>
			</tr>
	    </table>            
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">					
	</form>
		
	</body>
</html>