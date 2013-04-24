<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
		
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start(); 
	Auth::required($_SERVER['REQUEST_URI']);

	$idcosto=$_GET["codigo"];
 
	//consulta todos los costos
	$result=$con->query("select IDCOSTO,ARRCARGOACUENTA,COSTONEGOCIADO,DESCRIPCION,ACTIVO,FECHAMOD,IDUSUARIOMOD from $con->catalogo.catalogo_costo WHERE IDCOSTO='$idcosto' ");
	$row = $result->fetch_object();	
?>
<html>
	<head>
		<title>American Assist</title> 

		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>		
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	

		<style type="text/css">
			.style1 {color: #FFFFFF} .style3 {color: #000000}
            .style4 {font-family: "Times New Roman", Times, serif}
            </style>

			<script language="JavaScript">
				
				function validarCampo(variable){
				
					document.frmeditar.txtnombre.value=document.frmeditar.txtnombre.value.replace(/^\s*|\s*$/g,"");			
					if(document.frmeditar.txtnombre.value =='' ){
						alert('<?=_("INGRESE LA DESCRIPCION DEL COSTO.") ;?>');
						document.frmeditar.txtnombre.focus();
						return (false);
					}
					
					document.frmeditar.txtnombre.value = document.frmeditar.txtnombre.value.toUpperCase(); 
					
					return (true);
				}				
			</script>
	</head>
	<body onLoad="document.frmeditar.txtnombre.focus();show_info(document.frmeditar.chkcostonegocio.checked,'frm_datos_negociado.php','<?=$row->IDCOSTO; ?>')">
	<form name="frmeditar" id="frmeditar" action="actualizar_costo.php" method="POST" onSubmit = "return validarCampo(this)"  >
		<input name="idcosto" type="hidden" value="<?=$idcosto; ?>"/>
		<input name="pag" type="hidden" value="<?=$_GET["pag"]; ?>"/>
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		
		<table border="0" cellpadding="1" cellspacing="1" width="80%"  class="catalogos">
			<tr>
				<th style="text-align:left"><?=_("EDITAR COSTO") ;?></th>
		  </tr>
			<tr class='modo1'>
				<td><span style="text-align:left">
				  <?=_("DESCRIPCION") ;?>
				</span></td>
			  <td><input name="txtnombre" type="text" value="<?=$row->DESCRIPCION; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" ></td>
			</tr>
		      <tr class='modo1'>
				  <td><?=_("CARGO A CUENTA") ;?></td>
				  <td>
					<?
						$con->cmb_array("cmbcargoacuenta",$desc_cargoacuenta,$desc_cargoacuenta[$row->ARRCARGOACUENTA],"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
					?>		
				</td>
			</tr>	
			<tr class='modo1'>
			  <td><span style="text-align:left">
			    <?=_("COSTO NEGOCIADO") ;?>
			  </span></td>
			  <td><input type="checkbox" name="chkcostonegocio" id="chkcostonegocio" value="1" <?=($row->COSTONEGOCIADO==1?'checked':''); ?> onclick="show_info(this.checked ,'frm_datos_negociado.php','<?=$row->IDCOSTO; ?>')" ></td>
			</tr>	
			<tr class='modo1'>
			  <td></td>
	          <td><div id="verdatonegociado" style="display: none;"></div></td>
		  </tr>
			<tr class='modo1'>
			  <td><span style="text-align:left">
			    <?=_("ACTIVADO") ;?>
			  </span></td>
			  <td><input type="checkbox" name="chkstatus" id="chkstatus" value="1" <?=($row->ACTIVO==1?'checked':''); ?>></td>
			</tr>
			<tr class='modo1'>
				<td><span style="text-align:left">
				  <?=_("ULTIMA MODIFICACION") ;?>
				</span></td>
			  <td><span style="text-align:left">
			    <?=_("USUARIO") ;?>:
			  </span>&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;<span style="text-align:left">
			  <?=_("FECHA") ;?>
			  </span>:&nbsp; <b><?=$row->FECHAMOD; ?></b>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="new parent.MochaUI.Window({id: 'containertest8',title: '<?=_("HISTORICO DE COSTOS");?>',loadMethod: 'xhr',contentURL: '../../app/vista/catalogos/costos/historial.php?idcosto=<?=$row->IDCOSTO;?>',container: 'pageWrapper',width: 650,height: 250,x: 100,y: 150});"></img></td>
			</tr>				
			<tr class='modo1'>
				<td><div align="right">
				  <input type="button" class="botonstandar" value="<?=_("CANCELAR") ;?>" onClick="reDirigir('general.php')" title="<?=_("CANCELAR") ;?>">
				</div></td>
				<td><input name="Submit" type="submit" class="botonactualizar" value="<?=_("GRABAR") ;?>" title="<?=_("GRABAR COSTO") ;?>" ></td>
			</tr>
        </table>
			<div id="resultado"></div>
	</form>
	</body>
 </html>