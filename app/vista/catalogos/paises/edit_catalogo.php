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

	$result=$con->query("SELECT IDPAIS,NOMBRE,IDUSUARIOMOD,FECHAMOD from catalogo_pais where IDPAIS='".$_GET["codigo"]."'");
	$row = $result->fetch_object();
	
	$rsmoneda=$con->query("select IDMONEDA,DESCRIPCION from catalogo_moneda where DESCRIPCION!='' order by DESCRIPCION ");
 
?>
<html>
	<head>
		<title>American Assist</title>

		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />

		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmeditregistro.txtcodigo.value=document.frmeditregistro.txtcodigo.value.replace(/^\s*|\s*$/g,"");			
				
				if(document.frmeditregistro.txtnombre.value =='' ){
					alert('<?=_("INGRESE EL NOMBRE DEL PAIS") ;?>');
					document.frmeditregistro.txtnombre.focus();
					return (false);
				}	

				document.frmeditregistro.txtnombre.value = document.frmeditregistro.txtnombre.value.toUpperCase();
				
			}			
		</script>
		
	</head>
<body>
<form name="frmeditregistro" action="actualizar_pais.php" method="POST" onSubmit = "return validarCampo(this)" >
	<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
  <table border="0" cellpadding="1" cellspacing="1" width="70%" class="catalogos">
    <tr bgcolor="#333333">
		<th style="text-align:left"><?=_("EDITAR PAIS") ;?></th>
    </tr>
    <tr class='modo1'>
		<td><?=_("CODIGO") ;?></td>
		<td><input name="txtcodigo" id="txtcodigo" readonly type="text" value="<?=$_GET['codigo']; ?>" size="5" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></td>
    </tr>
	<tr class='modo1'>
		<td><?=_("NOMBRE") ;?></td>
		<td><input name="txtnombre" id="txtnombre" type="text" value="<?=$row->NOMBRE; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></td>
    </tr> 

    <!--tr class='modo1'>
		<td>ULTIMA MODIFICACION</td>
		<td>USUARIO:&nbsp;<b><?//=$row->IDUSUARIOMOD; ?></b>&nbsp;FECHA:&nbsp; <b><?//=$row->FECHA_MOD; ?>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onclick="new parent.MochaUI.Window({id: 'containertest',title: '<?//=_("HISTORICO DE PAISES");?>',loadMethod: 'xhr',contentURL: '../../../../app/vista/catalogos/paises/historial.php?idpais=<?//=$_GET['codigo'];?>',container: 'pageWrapper',width: 540,height: 250,x: 100,y: 150});"></b></td>
	</tr -->
    <tr class='modo1'>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('CANCELAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
      <td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PAIS') ;?>" ></td>
    </tr>
  </table>           
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
  <input name="idpais" type="hidden" value="<?=$_GET['codigo'];?>">
</form>

		<div id="beneficiario" style="margin:1px;padding:1px;float:left;position:absolute;top:26px;left:425px;width:200px;height:50px;display: none" ></div>
		
		<div id="bloque" style="display: none">
		<div/>
		
</body>
</html>