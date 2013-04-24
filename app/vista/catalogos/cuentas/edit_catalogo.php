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

	$idafiliado=$_GET["codigo"];
	
	$result=$con->query("select FECHAMOD,IDUSUARIOMOD,IDCUENTA,NOMBRE,AFILIADOS,PILOTO,ACTIVO,CUENTAVIP,VALIDACIONEXTERNA from catalogo_cuenta where IDCUENTA='$idafiliado' ");
	$row = $result->fetch_object();	
	
	$rsmoneda=$con->query("select IDMONEDA,DESCRIPCION from catalogo_moneda where ACTIVO=1 order by IDMONEDA ");
	$rsociedad=$con->query("select IDSOCIEDAD,NOMBRE from catalogo_sociedad where ACTIVO=1 order by NOMBRE");
?>
<html>
	<head>
		<title>American Assist</title> 

			<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
			<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
			
			<link href="../../../../estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
			<script src="../../../../estilos/fronter_js/jquery.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/index.js" type="text/javascript"></script>
			
			<script language="JavaScript">
				
				function validarCampo(variable){
				
					document.frmeditar.txtnombre.value=document.frmeditar.txtnombre.value.replace(/^\s*|\s*$/g,"");			
					document.frmeditar.txtpiloto.value=document.frmeditar.txtpiloto.value.replace(/^\s*|\s*$/g,"");
					
					if(document.frmeditar.txtnombre.value =='' ){
						alert('<?=_("INGRESE EL NOMBRE") ;?>');
						document.frmeditar.txtnombre.focus();
						return (false);
					}			
					else if(document.frmeditar.txtpiloto.value =='' ){
						alert('<?=_("INGRESE EL NUMERO DE PILOTO") ;?>');
						document.frmeditar.txtpiloto.focus();
						return (false);
					}

					return (true);
				}				
			</script>
			
			<script type="text/javascript">
		
			function adicionarFila(){

				var tabla = document.getElementById("contenido").tBodies[0];
				var fila = document.createElement("TR");
				fila.setAttribute("align","left");

				var celda1 = document.createElement("TD");

				var celda2 = document.createElement("TD");
				celda2.setAttribute("style","background:#E9E9E9" );
				var sel = document.createElement("SELECT");
				sel.setAttribute("size","1");
				sel.setAttribute("onFocus","coloronFocus(this)");
				sel.setAttribute("onBlur","colorOffFocus(this)");
				sel.setAttribute("class","classtexto");
				sel.setAttribute("name","cmbsociedad[]" );
				<?php
				
					$i=1;
					while($reg= $rsociedad->fetch_object())
					 {
						 echo "opcion$i = document.createElement('OPTION');\n";
						 echo "opcion$i.innerHTML = '".$reg->NOMBRE."';\n";
						 echo "opcion$i.value = '".$reg->CVESOCIEDAD."';\n";
						// if($reg->CVESOCIEDAD == 1)	echo "opcion$i.selected = '".$reg->CVESOCIEDAD."';\n";
						 echo "sel.appendChild(opcion$i);\n";
						 
						$i=$i+1;
					 }
				 ?>
				 
				celda2.appendChild(sel);				

				var boton = document.createElement('IMG');

				boton.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
				boton.setAttribute('title','ELIMINAR');
				boton.setAttribute('border','0');
				boton.setAttribute('height','14');
				boton.onclick=function(){ borrarFila(this); }
				celda2.appendChild(boton);

				fila.appendChild(celda1);
				fila.appendChild(celda2);

				tabla.appendChild(fila);	

				}

			function borrarFila(button){
			
				var fila = button.parentNode.parentNode;
				var tabla = document.getElementById('contenido').getElementsByTagName('tbody')[0];
				tabla.removeChild(fila);
			}
			
			</script>
			
			<style type="text/css">
			<!--
			.style1 {color: #FFFFFF}
			-->
			</style>
</head>
	<body onLoad="document.frmeditar.txtnombre.focus()">
	<form name="frmeditar" id="frmeditar" action="actualizar_cuenta.php" method="POST" onSubmit = "return validarCampo(this)"  >
		<input name="idafiliado" type="hidden" value="<?=$idafiliado; ?>" />
		<input name="pag" type="hidden" value="<?=$pag; ?>"/>
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		
		<table border="0" cellpadding="1" cellspacing="1" width="90%" id="contenido"  class="catalogos">
			<tr>
				<th style="text-align:left"><?=_("EDITAR CUENTA") ;?></th>
			</tr>
			<tr class='modo1'>
			  <td><?=_("CODIGO") ;?></td>
			  <td><input type="text" name="txtcodigo" id="txtcodigo" readonly class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="7" maxlength="6" value="<?=$idafiliado; ?>" ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("NOMBRE") ;?></td>
				<td><input name="txtnombre" type="text" value="<?=$row->NOMBRE; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" ></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("PILOTO") ;?></td>
			  <td><input name="txtpiloto" type="text" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="10" maxlength="10" value="<?=$row->PILOTO; ?>"  onkeypress="return validarnum(event)"></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("CUENTA VIP") ;?></td>
			  <td><input type="checkbox" name="chkvip" id="chkvip" value="1" <?=($row->CUENTAVIP==1?'checked':''); ?>></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("VALIDACION EXTERNA") ;?></td>
			  <td><input type="checkbox" name="ckbvalidacion" id="ckbvalidacion" value="1" <?=($row->VALIDACIONEXTERNA==1?'checked':''); ?>></td>
		  </tr>
			<tr class='modo1'>
			  <td><?=_("DATA AFILIADO") ;?></td>
			  <td><input name="chkafiliado" type="checkbox" id="chkafiliado" value="1" <?=($row->AFILIADOS==1?'checked':''); ?> ></td>
			</tr>	
			<tr class='modo1'>
			  <td><?=_("ACTIVADO") ;?></td>
			  <td><input name="chkstatus" type="checkbox" id="chkstatus" value="1" <?=($row->ACTIVO==1?'checked':''); ?> ></td>
			</tr>	
			<tr class='modo1'>
				<td><?=_("ULTIMA MODIFICACION") ;?></td>
				<td><?=_("USUARIO:") ;?>&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;
			    <?=_("FECHA:") ;?> &nbsp; <b><?=$row->FECHAMOD; ?></b>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="reDirigir_ventana('../../../../app/vista/catalogos/cuentas/historial.php?idcuenta=<?=$row->IDCUENTA;?>','window','650','350','VENTANAHISTORIAL')"></img></td>
			</tr>				
			<tr class='modo1'>
				<td><div align="right">
				  <input type="button" class="botonstandar" value="<?=_("CANCELAR") ;?>" onClick="reDirigir('general.php')" title="<?=_("SALIR") ;?>">
				</div></td>
				<td><input name="Submit" type="submit" class="botonactualizar" value="<?=_("GRABAR") ;?>" title="<?=_("Grabar costo") ;?>" ></td>
		  </tr>
		  	<?							 
				if(count($regis) > 0)
				 {
					for ($in = 1 ; $in <= count($regis) ; $in ++) {
			?>
			<tr class='modo1'>
				<td><? if($in == 1) echo _("SOCIEDADES"); ?> </td>					
				<td>
			  		<select name="cmbsociedad[]" id="cmbsociedad" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
							<option value="" selected><?=_("Seleccione") ;?></option>
						<? 
						$rsociedad2=$con->query("select IDSOCIEDAD,NOMBRE from catalogo_sociedad where ACTIVO=1 order by NOMBRE ");
						while($rowm = $rsociedad2->fetch_object())
						 {							 
							if($rowm->IDSOCIEDAD == $regis[$in-1])
							 {
						?>							
							<option value="<?=$rowm->IDSOCIEDAD; ?>" selected><?=$rowm->NOMBRE; ?></option>							
						<?
							  }
							else
							 {								 
						?> 
							<option value="<?=$rowm->IDSOCIEDAD; ?>" ><?=$rowm->NOMBRE; ?></option>							
						<?
							 }
						 }
						?>								
					</select>
			<? if($in == 1) { ?> &nbsp;<input name="nueva_fila" type="button" id="nueva_fila" class="boton" value="+" onClick="adicionarFila()"> <? } ?>
			<? if($in  > 1) { ?> &nbsp;<img src="../../../../imagenes/iconos/deletep.gif" onClick="borrarFila(this)" border='0' width="16" height="14" title="<?=_("ELIMINAR") ;?>"> <? } ?>				</td>
			</tr>
			<?  
					}
				 }
				else
				 {
			?>			
			<!--tr class='modo1'>
				<td><?//=_("SOCIEDADES") ;?></td>					
				<td>
			  		<select name="cmbsociedad[]" id="cmbsociedad" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
						<option value="" selected><?//=_("Seleccione") ;?></option>
							<? 
							// $rsociedad2=$con->query("select IDSOCIEDAD,NOMBRE from catalogo_sociedad where ACTIVO=1 order by NOMBRE ");
							// while($rowm = $rsociedad2->fetch_object())
							 // {								 
							?>
								<option value="<?//=$rowm->IDSOCIEDAD; ?>" ><?//=$rowm->NOMBRE; ?></option>
							<?
							// }							 
							?>								
						  </select>
				&nbsp;<input name="nueva_fila" type="button" id="nueva_fila" class="boton" value="+" onClick="adicionarFila()"> 
				</td>
			</tr -->
			<?
				}
			?>
        </table>
	  <input name="pag2" type="hidden" value="<?=$_GET['pag'];?>">
	</form>
	
	<table width="420" border="0" cellpadding="1" cellspacing="1" style="font-size:9px" >       
		<tr>
			<td colspan="2"><?=_("Programas asociados a esta cuenta:");?></td>
			<td>&nbsp;</td>
		    <td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><a onClick="reDirigir_ventana('../../../../app/vista/catalogos/programas/add_catalogo.php?codigo=<?=$idafiliado;?>&opc=1','window','600','450','NUEVO_PLAN')" style="color:#003366; cursor:pointer; text-decoration:underline  " title="<?=_("NUEVO PROGRAMA");?>"><?=_("AGREGAR NUEVO PROGRAMA");?></a></td>
			<td>&nbsp;</td>
		    <td>&nbsp;</td>
		</tr>
		<tr>
			<td width="74" bgcolor="#333333"><span class="style1"><?=_("IDPROGRAMA");?></span></td>
			<td width="129" bgcolor="#333333"><span class="style1"><?=_("PROGRAMA") ;?></span></td>
			<td width="115" bgcolor="#333333"><span class="style1"><?=_("CUENTA") ;?></span></td>
		    <!-- td width="115" bgcolor="#FFFFFF">&nbsp;</td -->
		</tr>
		<?
			$result=$con->query("SELECT catalogo_cuenta.IDCUENTA,catalogo_programa.IDCUENTA,catalogo_programa.IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_cuenta.NOMBRE as nombrecuenta ,if(catalogo_programa.ACTIVO='1','Activado','Inactivo') as statusplan,catalogo_programa.FECHAINIVIGENCIA,catalogo_programa.FECHAFINVIGENCIA FROM catalogo_programa inner join catalogo_cuenta on catalogo_cuenta.IDCUENTA=catalogo_programa.IDCUENTA  where catalogo_programa.IDCUENTA='$idafiliado' ");
			$i=0;
            
			while($reg=$result->fetch_object()){
			 if($i%2==0) $fondo='#CCCCCC'; else $fondo='#FFFFFF';
		?>
		<tr>
			<td onClick="reDirigir_ventana('../../../../app/vista/catalogos/programas/edit_catalogo.php?codigo=<?=$reg->IDPROGRAMA;?>&opc=1','window','600','450','PLAN')"  bgcolor="<?=$fondo; ?>"><?=$reg->IDPROGRAMA; ?></td>
			<td onClick="reDirigir_ventana('../../../../app/vista/catalogos/programas/edit_catalogo.php?codigo=<?=$reg->IDPROGRAMA;?>&opc=1','window','600','450','PLAN')"  bgcolor="<?=$fondo; ?>"><?=$reg->NOMBRE; ?></td>
			<td bgcolor="<?=$fondo; ?>"><?=$reg->nombrecuenta; ?></td>
		    <!--td> <img src="../../../../imagenes/iconos/deletep.gif" onclick="confirmaRespuesta('ESTAS SERGURO QUE DESEAS ELIMINAR ESTE REGISTRO?','eliminacionser.php?idprograma=<?//=$reg->IDPROGRAMA ;?>&codigo=<?//=$reg->IDCUENTA ;?>')" border='0' width="16" height="14" title="<?//=_("ELIMINAR") ;?>" style="cursor:pointer"></td -->
		</tr>                  
        <?
				$i=$i+1;
			 }
		?>
    </table>
</body>
</html>