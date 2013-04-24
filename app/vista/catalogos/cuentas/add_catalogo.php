<?php
	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
	$con->select_db($con->catalogo);
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 			
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
				document.frmaddregistro.txtpiloto.value=document.frmaddregistro.txtpiloto.value.replace(/^\s*|\s*$/g,"");
				
				if(document.frmaddregistro.txtcodigo.value =='' ){
					alert('<?=_("DIGITE EL CODIGO") ;?>');
					document.frmaddregistro.txtcodigo.focus();
					return (false);
				}
				else if(document.frmaddregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE") ;?>');
					document.frmaddregistro.txtnombre.focus();
					return (false);
				}			
				else if(document.frmaddregistro.txtpiloto.value =='' ){
					alert('<?=_("INGRESE EL NUMERO DE PILOTO") ;?>');
					document.frmaddregistro.txtpiloto.focus();
					return (false);
				}

				return (true);
			}		
		</script>

	</head>
	<body onLoad="document.frmaddregistro.txtcodigo.focus();">
	<form name="frmaddregistro" action="grabar_cuenta.php" method="POST" onSubmit = "return validarCampo(this)" >
			<input name="idservicio" type="hidden" value="" />
			<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		<table border="0" cellpadding="1" cellspacing="1" width="90%" class="catalogos">
			<tr bgcolor="#333333">
				<th style="text-align:left"><?=_("AGREGAR CUENTA") ;?></th>
			</tr>
			<tr class='modo1'>
				<td><?=_("CODIGO") ;?></td>
				 <td><input name="txtcodigo" type="text" onKeyPress="return validarletra(event)" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);clear_all(this.id)" onBlur="colorOffFocus(this);clear_all(this.id)" size="7" maxlength="6" id="txtcodigo"  ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("DESCRIPCION") ;?></td>
				<td><input type="text" name="txtnombre" size="30" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;"  ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("PILOTO") ;?></td>
				<td><input name="txtpiloto" id="txtpiloto" type="text" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="10" maxlength="10" onKeyPress="return validarnum(event)" ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("CUENTA VIP") ;?></td>
				<td><input type="checkbox" name="chkvip" id="chkvip" value="1" ></td>
			</tr>		
			<tr class='modo1'>
			  <td><?=_("VALIDACION EXTERNA") ;?></td>
			  <td><input type="checkbox" name="ckbvalidacion" id="ckbvalidacion" value="1" ></td>
		  </tr>
			<tr class='modo1'>
				<td><?=_("DATA AFILIADO") ;?></td>
				<td><input type="checkbox" name="chkafiliado" id="chkafiliado" value="1"  checked></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("ACTIVADO") ;?></td>
			  <td><input type="checkbox" name="chkstatus" id="chkstatus" value="1"  checked></td>
			</tr>
			</tr>
			<tr class='modo1'>
				<td style="text-align:right"><input type="button" class="botonstandar" value="<?=_("CANCELAR") ;?> " onClick="reDirigir('general.php')" title="<?=_("SALIR") ;?>"></td>
				<td><input name="Submit"  class="botonstandar" type="submit" value="<?=_("GRABAR") ;?>" title="<?=_("AGREGAR CUENTA") ;?>" ></td>
			</tr>
        </table>
		<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
	</form> 
	</body>
 </html>