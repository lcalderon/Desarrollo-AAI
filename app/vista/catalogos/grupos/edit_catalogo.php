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

	$result=$con->query("SELECT IDGRUPO,NOMBRE,ACTIVO,FIJO FROM $con->catalogo.catalogo_grupo WHERE IDGRUPO='".$_GET["codigo"]."'");
	$row = $result->fetch_object();
	
	$_REQUEST["idcodigo"]=$_GET["codigo"];
?>
<html>
	<head>
		<title><?=_("American Assist");?></title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmeditregistro.txtnombre.value=document.frmeditregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmeditregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DEL GRUPO");?>.');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}			
			}			
		</script>
		
	</head>
<body>
<form name="frmeditregistro" action="actualizar_grupo.php" method="POST" onSubmit = "return validarCampo(this)" >
  <table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
    <tr bgcolor="#333333">
		<th style="text-align:left"><?=_("EDITAR GRUPO") ;?></th>
    </tr>
	<tr class='modo1'>
		<td><?=_("NOMBRE") ;?></td>
		<td><input name="txtnombre" id="txtnombre" type="text" value="<?=$row->NOMBRE; ?>" <? if($row->FIJO) echo "readonly" ; ?> size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr> 
	<tr class='modo1'>
		<td><?=_("ACTIVADO") ;?></td>
		<td><input type="checkbox" name="chkactivo"  <? if($row->ACTIVO==1) echo "checked" ; ?>  value="1" /></td>
    </tr>
    <tr class='modo1'>
		<td><?=_("ULTIMA MODIFICACION") ;?></td>
		<td><?=_("USUARIO") ;?>:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;<?=_("FECHA") ;?>:&nbsp; <b><?=$row->FECHAMOD; ?>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onclick="new parent.MochaUI.Window({id: 'containertest',title: '<?=_("HISTORICO DE GRUPOS");?>',loadMethod: 'xhr',contentURL: '../../app/vista/catalogos/grupos/historial.php?idperfil=<?=$_GET['codigo'];?>',container: 'pageWrapper',width: 540,height: 250,x: 100,y: 150});"></b></td>
	</tr>
    <tr class='modo1'>
      <td align="right"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR GRUPO') ;?>" ></td>
      <td><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>	  
    </tr>
  </table>           
		<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
		<input name="codigo" type="hidden" value="<?=$_GET['codigo'];?>">
</form> 
 
 <? include("gestioncuentas.php"); ?>
		
</body>
</html>