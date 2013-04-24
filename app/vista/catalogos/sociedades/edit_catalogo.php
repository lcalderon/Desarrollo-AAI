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

	$result=$con->query("SELECT IDUSUARIOMOD,FECHAMOD,IDSOCIEDAD,NOMBRE,IPSERVIDOR_ASTERISK,USUARIO_MANAGER,PASSWORD_MANAGER,PREFIJO,CONTEXTO,ACTIVO from catalogo_sociedad where IDSOCIEDAD='".$_GET["codigo"]."' ");
	$row = $result->fetch_object();
 
?>
<html>
	<head>
		<title>American Assist</title>

		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />

		<link href="../../../../estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
		<script src="../../../../estilos/fronter_js/jquery.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
		<script src="../../../../estilos/fronter_js/index.js" type="text/javascript"></script>	
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmeditregistro.txtnombre.value=document.frmeditregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmeditregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DE LA SOCIEDAD") ;?>');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}
				
				document.frmeditregistro.txtnombre.value = document.frmeditregistro.txtnombre.value.toUpperCase();
				
			}			
		</script>
		
	</head>
<body>
<form name="frmeditregistro" action="actualizar_sociedad.php" method="POST" onSubmit = "return validarCampo(this)" >
	<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
  <table border="0" cellpadding="1" cellspacing="1" width="85%" class="catalogos">
    <tr bgcolor="#333333">
		<th style="text-align:left"><?=_("EDITAR SOCIEDAD") ;?></th>
    </tr>
    <tr class='modo1'>
		<td><?=_("CODIGO") ;?></td>
		<td><input name="txtcodigo" id="txtcodigo" readonly type="text" value="<?=$_GET['codigo']; ?>" size="5" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></td>
    </tr>
	<tr class='modo1'>
		<td><?=_("NOMBRE") ;?></td>
		<td><input name="txtnombre" id="txtnombre" type="text" value="<?=$row->NOMBRE; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr>		
	<tr class='modo1'>
		<td><?=_("IP SERVIDOR ASTERISK") ;?></td>
		<td><input name="txtip" type="text" class="classtexto" id="txtip"  style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$row->IPSERVIDOR_ASTERISK; ?>" size="18" maxlength="15" ></td>
	</tr>            
	<tr class='modo1'>
	  <td><?=_("PREFIJO") ;?></td>
	  <td><input name="txtprefijo" type="text" class="classtexto" id="txtprefijo"  style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$row->PREFIJO; ?>" size="7" maxlength="5" ></td>
  </tr>
	<tr class='modo1'>
	  <td><?=_("CONTEXO") ;?></td>
	  <td><input name="txtcontexto" type="text" class="classtexto" id="txtcontexto"  style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$row->CONTEXTO; ?>" size="10" maxlength="8" ></td>
  </tr>
	<tr class='modo1'>
	  <td><?=_("USUARIO MANAGER") ;?></td>
	  <td><input name="txtusuariomanag" type="text" class="classtexto" id="txtusuariomanag"  style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$row->USUARIO_MANAGER; ?>" size="20" maxlength="15" ></td>
  </tr>
	<tr class='modo1'>
	  <td><?=_("PASSWORD MANAGER") ;?></td>
	  <td><input name="txtpasswordmanag" type="password" class="classtexto" id="txtpasswordmanag"  style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$row->PASSWORD_MANAGER; ?>" size="20" maxlength="15" ></td>
  </tr>    
    <tr class='modo1'>
		<td><?=_("ACTIVADO") ;?></td>
		<td><input type="checkbox" name="chkactivo"  <? if($row->ACTIVO==1) echo "checked " ; ?>  value="1" /></td>
    </tr>
    <tr class='modo1'>
		<td>ULTIMA MODIFICACION</td>
		<td>USUARIO:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;FECHA:&nbsp; <b><?=$row->FECHAMOD; ?>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onclick="reDirigir_ventana('../../../../app/vista/catalogos/sociedades/historial.php?idsociedad=<?=$row->IDSOCIEDAD;?>','window','650','350','VENTANASOCIEDAD');"></b></td>
	</tr>
    <tr class='modo1'>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('CANCELAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
      <td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PAIS') ;?>" ></td>
    </tr>
  </table>           
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
  <input name="idsociedad" type="hidden" value="<?=$_GET['codigo'];?>">
</form>

		<div id="beneficiario" style="margin:1px;padding:1px;float:left;position:absolute;top:26px;left:425px;width:200px;height:50px;display: none" ></div>
		
		<div id="bloque" style="display: none">
		<div/>
		
</body>
</html>