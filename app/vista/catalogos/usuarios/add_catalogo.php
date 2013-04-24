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
	
	$con->select_db($con->catalogo);
	
	session_start();
 	Auth::required($_SERVER['REQUEST_URI']);
	
	$IS_ADM=$con->consultation("SELECT COUNT(*) FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_GET["codigo"]."' AND IDGRUPO='ADMI'");
	if($IS_ADM[0][0] ==0 and $_SESSION["user"]!="ADMINISTRADOR")	$subquery="WHERE IDGRUPO !='ADMI'";	
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
			
				document.frmaddregistro.txtusuario.value=document.frmaddregistro.txtusuario.value.replace(/^\s*|\s*$/g,"");			
				document.frmaddregistro.txtnombre.value=document.frmaddregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");
				document.frmaddregistro.txtapellido.value=document.frmaddregistro.txtapellido.value.replace(/^\s*|\s*$/g,"");			
				document.frmaddregistro.txtcontrasena.value=document.frmaddregistro.txtcontrasena.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmaddregistro.txtusuario.value =='' ){
					alert('<?=_("INGRESE EL USUARIO.") ;?>');
					document.frmaddregistro.txtusuario.focus();
					return (false);
				}		
				else if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE.") ;?>');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}
				else if(document.frmaddregistro.txtapellido.value =='' ){
					alert('<?=_("INGRESE EL APELLIDO.") ;?>');
					document.frmaddregistro.txtapellido.focus();
					
					return (false);
				}	
					
				var nom=document.frmaddregistro.txtnombre.value.substring(0,1);
				var ape=document.frmaddregistro.txtapellido.value.substring(0,1);

				if((nom.length+ape.length+document.frmaddregistro.txtusuario.value.length) < 8)	{	alert('<?=_("LONGITUD MINIMA PARA EL USUARIO ES 8 DIGITOS.") ;?>');		document.frmaddregistro.txtusuario.focus(); return (false); }
				if(document.frmaddregistro.txtcontrasena.value.length < 8)	{	alert('<?=_("LONGITUD MINIMA PARA LA CONTRASENA 8 DIGITOS.") ;?>');		document.frmaddregistro.txtcontrasena.focus(); return (false); }

				if(document.frmaddregistro.txtrcontrasena.value != document.frmaddregistro.txtcontrasena.value){
					alert('<?=_("LA CONTRASENAS NO COINCIDEN.") ;?>');
					document.frmaddregistro.txtrcontrasena.focus();
					return (false);
				}
				
				var numeros="0123456789";
				var usuario=document.frmaddregistro.txtusuario.value.toUpperCase();
				var nombre=document.frmaddregistro.txtnombre.value.toUpperCase();
				var apellido=document.frmaddregistro.txtapellido.value.toUpperCase();
				var texto=document.frmaddregistro.txtcontrasena.value.toUpperCase();
				var cant=0;
				var cant2=0;
 
				var mayusculas="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
		 
				   for(i=0; i<texto.length; i++){
					  if(numeros.indexOf(texto.charAt(i),0)!=-1){
							cant=cant+1;
					  }					  
				   }
				
				   for(i=0; i<texto.length; i++){
					  if (mayusculas.indexOf(texto.charAt(i),0)!=-1){
						 cant2=cant2+1;
					  }

				   }

				if(cant == 0) { alert('<?=_("LA CONTRESENA DEBE CONTENER AL MENOS UN NUMERO") ;?>');	document.frmaddregistro.txtcontrasena.focus(); return (false); }
				if(cant2 == 0) { alert('<?=_("LA CONTRESENA DEBE CONTENER AL MENOS UN LETRA") ;?>'); 	document.frmaddregistro.txtcontrasena.focus();	return (false); }

   				var pat = new RegExp(usuario);
				var pat2 = new RegExp(nombre);
				var pat3 = new RegExp(apellido);
				
				if(pat.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NOMBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmaddregistro.txtcontrasena.focus();	return (false); }
				if(pat2.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NONBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmaddregistro.txtcontrasena.focus();	return (false); }
				if(pat3.test(texto)) { alert('<?=_("LA CONTRASENA NO DEBE CONTENER EL NONBRE,APELLIDO O NOMBRE DE USUARIO") ;?>');	document.frmaddregistro.txtcontrasena.focus();	return (false); }

				return(isEmail(document.frmaddregistro.txtemail));

				return (true);
			}
		</script>
				
		<script>
			function visualizarusu(){
						
				var nom=document.frmaddregistro.txtnombre.value.substring(0,1);
				var ape=document.frmaddregistro.txtapellido.value.substring(0,1);
				var usu=document.frmaddregistro.txtusuario.value;
				
			}	
		</script>		
	</head>
	<body onLoad="document.frmaddregistro.txtusuario.focus();">
	<form name="frmaddregistro" action="grabar_usuario.php" method="POST" onSubmit = "return validarCampo(this)" >
		<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
	
		<table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
			<tr bgcolor="#333333">
				<th style="text-align:left"><?=_("AGREGAR USUARIOS") ;?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("USUARIO") ;?></td>
				<td><input name="txtusuario" id="txtusuario" type="text" size="20" maxlength="15" onFocus="coloronFocus(this);clear_all(this.id)" onBlur="colorOffFocus(this);clear_all(this.id)" class="classtexto"  style="text-transform:uppercase;"><div id="usu"></div></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("NOMBRES") ;?></td>
				<td><input name="txtnombre" id="txtnombre" type="text" size="30" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>	
			<tr class='modo1'>
				<td><?=_("APELLIDOS") ;?></td>
				<td><input name="txtapellido" id="txtapellido" type="text" size="30" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>		
			<tr class='modo1'>
				<td><?=_("CONTRASENIA") ;?></td>
				<td><input name="txtcontrasena" id="txtcontrasena" type="password" size="20"  maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("RE-CONTRASENIA") ;?></td>
				<td><input name="txtrcontrasena" id="txtrcontrasena" type="password" size="20"  maxlength="15" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
			</tr>			
			<tr class='modo1'>
				<td><?=_("EMAIL") ;?></td>
				<td><input name="txtemail" id="txtemail" type="text" size="40" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" ></td>
			</tr>			
			<tr class='modo1'>
				<td><?=_("PERMISO CAMBIAR CONTRASE&Ntilde;A") ;?></td>
				<td><input type="checkbox" name="chkcambia" id="checkbox"  checked="checked" value="1" /></td>	
			</tr>		
			<tr class='modo1'>
				<td><?=_("ACTIVADO") ;?></td>
				<td><input type="checkbox" name="chkactivo" id="checkbox"  checked="checked" value="1" /></td>	
			</tr>
			<tr class='modo1'>
			  <td><?=_("GRUPOS") ;?></td>			  
				  <td>
				  <select name="cmbgrupos[]" id="cmbgrupos[]" size='7'  multiple onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'>
					<?		
						$i=0;	
						$result=$con->query("SELECT IDGRUPO,NOMBRE FROM $con->catalogo.catalogo_grupo $subquery order by NOMBRE");
						while($reg = $result->fetch_object())
						 {
							if(in_array($reg->IDGRUPO,$accesogrupo))	$selecion="selected";	else $selecion="";							
					?>
						<option value="<?=$reg->IDGRUPO;?>" <?=$selecion;?> ><?=$reg->NOMBRE;?></option>
					<? 
						 } 
					?>
					</select>
				</td>				
			</tr>
			<tr class='modo1'>				
				<td align="right"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR USUARIO') ;?>" >&nbsp;</td>
				<td><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>				
			</tr>
	    </table>	  
	</form>
		
	</body>
</html>