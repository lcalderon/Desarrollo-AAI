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
 	//Auth::required();	 

	$result=$con->query("SELECT IDPLANTILLAPERFIL,NOMBRE,ACTIVO,IDUSUARIOMOD,FECHAMOD from catalogo_plantillaperfil where IDPLANTILLAPERFIL='".$_GET["codigo"]."'");
	$row = $result->fetch_object();
 
?>
<html>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmeditregistro.txtnombre.value=document.frmeditregistro.txtnombre.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmeditregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DE LA SOCIEDAD.") ;?>');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}			
			}			
		</script>
		
	</head>
<body>
<form name="frmeditregistro" action="actualizar_plantilla.php" method="POST" onSubmit = "return validarCampo(this)" >
  <table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
    <tr bgcolor="#333333">
		<th style="text-align:left"><?=_("EDITAR PLANTILLA") ;?></th>
    </tr>
	<tr class='modo1'>
		<td><?=_("NOMBRE") ;?></td>
		<td><input name="txtnombre" id="txtnombre" type="text" value="<?=$row->NOMBRE; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr>		
    
    <tr class='modo1'>
		<td><?=_("ACTIVADO") ;?></td>
		<td><input type="checkbox" name="chkactivo"  <? if($row->ACTIVO==1) echo "checked " ; ?>  value="1" /></td>
    </tr>
    <tr class='modo1'>
		<td>ULTIMA MODIFICACION</td>
		<td>USUARIO:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;FECHA:&nbsp; <b><?=$row->FECHAMOD; ?>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onclick="new parent.MochaUI.Window({id: 'containertest',title: '<?=_("HISTORICO DE PLANTILLA");?>',loadMethod: 'xhr',contentURL: '../../app/vista/catalogos/sociedades/historial.php?idperfil=<?=$_GET['codigo'];?>',container: 'pageWrapper',width: 540,height: 250,x: 100,y: 150});"></b></td>
	</tr>
    <tr class='modo1'>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
      <td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PLANTILLA') ;?>" ></td>
    </tr>
	<tr class='modo1'>
	  <td colspan="2"><div align="right"><em><strong><a href="#" onClick="new parent.MochaUI.Window({id: 'containertest',title: '<?=_("ACCESOS AL SISTEMA");?>',loadMethod: 'iframe',contentURL: '../../../../app/vista/catalogos/plantillas/frmaccesos.php?idperfil=<?=$_GET["codigo"]; ?>',container: 'pageWrapper',width: 700,height: 410,x: 50,y:50});"><?=_('A&Ntilde;ADIR ACCESOS') ;?>
	  </a></strong></em></div></td>
	</tr>
  </table>           
		<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
		<input name="codigo" type="hidden" value="<?=$_GET['codigo'];?>">
</form>
 
		
</body>
</html>