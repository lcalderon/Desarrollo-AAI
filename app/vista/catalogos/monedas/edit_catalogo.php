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

	$result=$con->query("SELECT IDMONEDA,DESCRIPCION,SIMBOLO,ACTIVO,IDUSUARIOMOD,FECHAMOD from catalogo_moneda where IDMONEDA='".$_GET["codigo"]."'");
	$row = $result->fetch_object();
 
?>
<html>
	<head>
		<title>American Assist</title>

		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
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
					alert('<?=_("INGRESE EL NOMBRE DEL PAIS") ;?>');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}
				else if(document.frmeditregistro.txtsimbolo.value =='' ){
					alert('<?=_("INGRESE EL SIMBOLO") ;?>');
					document.frmeditregistro.txtsimbolo.focus();
					return (false);
				}	
				
				document.frmeditregistro.txtnombre.value = document.frmeditregistro.txtnombre.value.toUpperCase();
				
				return (true);
				
			}			
		</script>
		
	</head>
<body>
<form name="frmeditregistro" action="actualizar_moneda.php" method="POST" onSubmit = "return validarCampo(this)" >
	<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
  <table border="0" cellpadding="1" cellspacing="1" width="95%" class="catalogos">
    <tr bgcolor="#333333">
		<th width="35%" style="text-align:left"><?=_("EDITAR MONEDA") ;?></th>
    </tr>
    
	<tr class='modo1'>
		<td><?=_("CODIGO") ;?></td>
		<td width="65%"><input name="idmoneda" id="idmoneda" type="text" value="<?=$row->IDMONEDA; ?>" readonly size="5" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr>		
	<tr class='modo1'>
		<td><?=_("NOMBRE") ;?></td>
		<td width="65%"><input name="txtnombre" id="txtnombre" type="text" value="<?=$row->DESCRIPCION; ?>" size="25" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr>		
	<tr class='modo1'>
		<td><?=_("SIMBOLO") ;?></td>
		<td><input name="txtsimbolo" id="txtsimbolo" type="text" value="<?=$row->SIMBOLO; ?>" size="12" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr>	
    <tr class='modo1'>
		<td><?=_("ACTIVADO") ;?></td>
		<td><input type="checkbox" name="chkactivo"  <? if($row->ACTIVO==1) echo "checked " ; ?>  value="1" /></td>
    </tr>
    <tr class='modo1'>
		<td><?=_("ULTIMA MODIFICACION") ;?></td>
		<td><?=_("USUARIO") ;?>:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;<?=_("FECHA") ;?>:&nbsp; <b><?=$row->FECHAMOD; ?>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onclick="reDirigir_ventana('../../../../app/vista/catalogos/monedas/historial.php?idmoneda=<?=$row->IDMONEDA;?>','window','650','350','VENTANAMONEDA')"></b></td>
	</tr>
    <tr class='modo1'>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('CANCELAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
      <td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR MONEDA') ;?>" ></td>
    </tr>
  </table>           
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
			<input name="idmoneda" type="hidden" value="<?=$_GET['codigo'];?>">
</form>

		<div id="beneficiario" style="margin:1px;padding:1px;float:left;position:absolute;top:26px;left:425px;width:200px;height:50px;display: none" ></div>
		<div id="bloque" style="display: none"><div/>
		
</body>
</html>