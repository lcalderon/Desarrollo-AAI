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
 	Auth::required($_SERVER['REQUEST_URI']);
		 
	 $con->select_db($bd);

	$idfamilia=$_GET["codigo"];
 
	$result=$con->query("select IDFAMILIA,DESCRIPCION,ACTIVO,IDUSUARIOMOD,FECHAMOD,COLOR from catalogo_familia WHERE IDFAMILIA='$idfamilia' ");
	$row = $result->fetch_object();	
	
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
			
			<style type="text/css">
			.style1 {color: #FFFFFF} .style3 {color: #000000}
            .style4 {font-family: "Times New Roman", Times, serif}
            </style>

			<script language="JavaScript">
				
				function validarCampo(variable){
				
					document.frmeditar.txtnombre.value=document.frmeditar.txtnombre.value.replace(/^\s*|\s*$/g,"");			
					if(document.frmeditar.txtnombre.value =='' ){
						alert('<?=_("INGRESE LA DESCRIPCION DE LA FAMILIA.") ;?>');
						document.frmeditar.txtnombre.focus();
						return (false);
					}
					
					return (true);
				}				
			</script>
			<script language="javascript" type="text/javascript">

				function shouldset(passon){
				if(document.frmeditar.txtcolor.value.length == 7){setcolor(passon)}
				}

				function setcolor(elem){
					document.frmeditar.txtcolor.value=elem;
					document.frmeditar.selcolor.style.backgroundColor=elem;
					comportamientoDiv('-','colores');					 
				}
			</script>

	</head>
	<body onLoad="document.frmeditar.txtnombre.focus();setcolor('<?=$row->COLOR;?>');">
	<form name="frmeditar" id="frmeditar" action="actualizar_familia.php" method="POST" onSubmit = "return validarCampo(this)"  >
		<input name="idfamilia" type="hidden" value="<?=$idfamilia; ?>" />
		<input name="idmoneda" type="hidden" value="<?=$row->IDMONEDA; ?>" />
		<input name="pag" type="hidden" value="<?=$_GET["pag"]; ?>" />
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
		
		<table border="0" cellpadding="1" cellspacing="1" width="90%"  class="catalogos">
			<tr>
				<th style="text-align:left"><?=_("EDITAR FAMILIA") ;?></th>
		  </tr>
			<tr class='modo1'>
				<td><span style="text-align:left">
				  <?=_("DESCRIPCION") ;?>
				</span></td>
			  <td><input name="txtnombre" type="text" value="<?=$row->DESCRIPCION; ?>" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("COLOR") ;?></td>
				<td><input type="text" name="txtcolor" size="10" maxlength="7" onFocus="coloronFocus(this);comportamientoDiv('+','colores')" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;" value="<?=$row->COLOR; ?>" ><input style="border-color: #8ba3b9;border-style: dashed;border-width: 1px;color: #333333;font-size: 10px;font-family: Verdana, Arial, Helvetica, sans-serif;" name="selcolor" size="5"   class="formu2" onfocus="this.blur()" type="text" ></td>
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
			  </span>:&nbsp; <b><?=$row->FECHAMOD; ?></b>&nbsp;<img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="reDirigir_ventana('../../../../app/vista/catalogos/familias/historial.php?idfamilia=<?=$row->IDFAMILIA;;?>','window','550','300','VENTANAFAMILIA');" ></img></td>                
		  </tr>				
			<tr class='modo1'>
				<td><div align="right">
				  <input type="button" value="<?=_("CANCELAR") ;?>" onClick="reDirigir('general.php')" title="<?=_("CANCELAR") ;?>" style="font-size:10px;">
				</div></td>
				<td><input name="Submit" type="submit" value="<?=_("GRABAR") ;?>" title="<?=_("GRABAR FAMILIA") ;?>" style="font-size:10px; font-weight:bold" ></td>
			</tr>
      </table>
	    <br>
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="font-size:9px" >       
		<tr>
			<td colspan="2"><?=_("Servicios asociados a esta familia:");?></td>
			<td>&nbsp;</td>
		    <td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2"><a onClick="reDirigir_ventana('../../../../app/vista/catalogos/servicios/add_catalogo.php?codigo=<?=$idafiliado;?>&opc=1','window','650','400','VENTANASERVICIO')" style="color:#003366; cursor:pointer; text-decoration:underline  " title="<?=_("NUEVO SERVICIO");?>"><?=_("AGREGAR NUEVO SERVICIO");?></a></td>
			<td>&nbsp;</td>
		    <td>&nbsp;</td>
		</tr>
		<tr>
			<td width="74" bgcolor="#333333"><div align="center"><span class="style1">
		    <?=_("IDSERVICIO");?>
		    </span></div></td>
			<td width="129" bgcolor="#333333"><div align="center"><span class="style1">
		    <?=_("SERVICIO") ;?>
		    </span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1">
		    <?=_("FAMILIA") ;?>
		    </span></div></td>
	  </tr>
		<?
			$result=$con->query("SELECT IDSERVICIO,DESCRIPCION  FROM catalogo_servicio WHERE IDFAMILIA=".$row->IDFAMILIA);
			$i=0;
			while($regs=$result->fetch_object())
			 {
			 if($i%2==0) $fondo='#CCCCCC'; else $fondo='#FFFFFF';
		?>
		<tr>
			<td bgcolor="<?=$fondo; ?>" style="text-align:center"><?=$regs->IDSERVICIO; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%"><?=$regs->DESCRIPCION; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->DESCRIPCION; ?></td>		    
		</tr>                  
        <?
				$i=$i+1;
			 }
		?>
    </table>
	    <p><br>
      </p>
	<div id="colores" style="display:none;margin:1px;padding:1px;float:left;position:absolute;top:10px;left:425px;width:200px;height:50px;" >
		 <? require_once("paletacolores.html")?>
	</div>	  
	</form>    
	</body>
 </html>