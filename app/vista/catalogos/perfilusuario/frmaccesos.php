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
	
	$perfil=$_GET["idperfil"];

	$rsperfil=$con->query("SELECT IDMODULO FROM $con->temporal.acceso_plantillaperfil where IDPLANTILLAPERFIL='$perfil' order by IDMODULO");
	while($regusu = $rsperfil->fetch_object())
	 {
		$accesos[]=$regusu->IDMODULO; 
	 }

	$data=$con->consultation("SELECT count(IDMODULO) FROM $con->temporal.seguridadmodulo where  TIPO='MENU_CAT'  group by TIPO order by count(IDMODULO) DESC limit 1");	
	
	//$i=1;
	$rs_cuenta=$con->query("select IDMODULO from $con->temporal.acceso_plantillaperfil where IDPLANTILLAPERFIL='$perfil' order by IDMODULO ");
	while($regcue = $rs_cuenta->fetch_object())
	 {
		$rowcuenta[]=$regcue->IDMODULO;		
		if($regcue->IDMODULO=="ALLCUENTA")	$todoscuentas=1;
	 }
 
	$cantidad=$con->consultation("select count(*)+4 from $con->catalogo.catalogo_cuenta order by NOMBRE ");	
	$allvalor=$con->consultation("SELECT count(*) FROM $con->temporal.acceso_plantillaperfil where IDUSUARIO='$usuario' ");

	$rs_accesos=$con->query("SELECT IDMODULO FROM $con->temporal.seguridad_modulosxusuario where IDUSUARIO='".$_SESSION["user"]."'"); 
 	while($reg_access = $rs_accesos->fetch_object())	$reg_accesos[]=$reg_access->IDMODULO;	
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<title>American Assist</title>
	<link href="pop.css" media="all" rel="stylesheet" type="text/css"/>  
	
	<style type="text/css">
	<!--
	.style3 {
	color: #003399;
	font-weight: bold;
	}
	-->
	</style>

	<link rel="stylesheet" href="treeview/jquery.treeview.css" />
	<link rel="stylesheet" href="treeview/demo/screen.css" />
	
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/jquery-1.3.2.js"></script>	 
	<script src="treeview/lib/jquery.cookie.js" type="text/javascript"></script>
	<script src="treeview/jquery.treeview.js" type="text/javascript"></script>	
	<script type="text/javascript" src="treeview/demo/demo.js"></script>
	
	<link type="text/css" href="../../../../librerias/jquery-ui-1.7.1/development-bundle/demos/demos.css" rel="stylesheet" />		
	<link type="text/css" href="../../../../librerias/jquery-ui-1.7.1/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/ui/ui.core.js"></script>
	<script type="text/javascript" src="../../../../librerias/jquery-ui-1.7.1/development-bundle/ui/ui.tabs.js"></script>
	
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
		<script LANGUAGE="JavaScript">

			function moveVals(n, from, to) {
				if (document.layers) {
					fromObj = document.layers[from];
					to = document.layers[to];
				} else if (document.all) {
					fromObj = document.all(from);
					to = document.all(to);
				}	
				else if (document.getElementById) {
					fromObj = document.getElementById(from);
					to = document.getElementById(to);
				}
				 
				if (n == 1 || n == 2) {
					var indTo = to.length-1;
					for (i=fromObj.length-1; i>=0; i--) {
						if (n==1 || fromObj.options[i].selected) {
							indTo++;
							to.options[indTo] = new Option(fromObj.options[i].text, fromObj.options[i].value);
							fromObj.options[i] = null;
						}
					}
				} else if (n == 3 || n == 4) {
					var indFrom = fromObj.length-1;
					for (i=to.length-1; i>=0; i--) {
						if (n==4 || to.options[i].selected) {
							indFrom++;
							fromObj.options[indFrom] = new Option(to.options[i].text, to.options[i].value);
							to.options[i] = null;
						}
					}
				}
			}

			function frmButtons() {
				var select = "chosen[]";
				var avail = "avail";
				if (document.layers) {
					var sel = document.layers[select];
					var av = document.layers[avail];
				} else if (document.all) {
					var sel = document.all(select);
					var av = document.all(avail);
				}	
				else if (document.getElementById) {
					var sel = document.getElementById(select);
					var av = document.getElementById(avail);
				}	
				
				if (sel.length <= 0) {
					// document.frmAddPro.btnR.disabled = true;
					// document.frmAddPro.btnRR.disabled = true;
				} else {
					// document.frmAddPro.btnR.disabled = false;
					// document.frmAddPro.btnRR.disabled = false;
				}
				if (av.length <= 0) {
					// document.frmAddPro.btnL.disabled = true;
					// document.frmAddPro.btnLL.disabled = true;
				} else {
					// document.frmAddPro.btnL.disabled = false;
					// document.frmAddPro.btnLL.disabled = false;
				}
			}
 
			function marcarTodos(id) 
			{
				var els = document.getElementById(id).options;
				for(i = 0; i < els.length; i++)
					els[i].selected = true;
			}
	 
			function marcarckb(valor,nombre){
				
				if(nombre.checked)
				{
					for (x = valor; x < (valor*1)+3; x++)
					{
						document.getElementById('MARCA'+x).checked=true;
					}
				
				}
				else
					{
						for (x = valor; x < (valor*1)+3; x++)
						{
							document.getElementById('MARCA'+x).checked=false;
						}	
					}	
			}		
			
			function verificarckb(valor,nombre){
				var i=0;
					for (x = valor; x < (valor*1)+3; x++)
					{
						if(document.getElementById('MARCA'+x).checked)	i=i+1;	
					}
					
					if(i == 0)	nombre.checked=false; else	nombre.checked=true;

		
			}

			function seleccionar(activar){
				//alert('<?=$cantida[0][0] ;?>');
				var valor=(document.frmAddPro.elements.length)-(<?=$cantidad[0][0] ;?>);
				 
				for (i=0;i<valor;i++)
				if(document.frmAddPro.elements[i].type == "checkbox"  && document.frmAddPro.elements[i].disabled==false)
				//if(document.frmAddPro.elements[i].type == "checkbox")
				 
				document.frmAddPro.elements[i].checked=activar;
				//if(activar==1)	comenzar();
			}
			
			function seleccionar2(activar){
				//alert('<?=$cantida[0][0] ;?>');
				var valor=(document.frmAddPro.elements.length)-(<?=$cantidad[0][0] ;?>);
				var valor2= valor+(<?=$cantidad[0][0] ;?>)-2;
	
				for (i=valor;i<valor2;i++)
				if(document.frmAddPro.elements[i].type == "checkbox")
				 
				document.frmAddPro.elements[i].checked=activar;

			}

			function invertir(){
				temp = document.frmAddPro.elements.length;
				 
				for (i=0; i < temp; i++){
				if(document.frmAddPro.elements[i].checked == 1 && document.frmAddPro.elements[i].disabled==false){ document.frmAddPro.elements[i].checked = 0; }
				//if(document.frmAddPro.elements[i].checked == 1){ document.frmAddPro.elements[i].checked = 0; }
				else if(document.frmAddPro.elements[i].checked == 0 && document.frmAddPro.elements[i].disabled==false) { document.frmAddPro.elements[i].checked = 1; }
				//else if(document.frmAddPro.elements[i].checked == 0) { document.frmAddPro.elements[i].checked = 1; }
				}
			}
			
	</script>
	<script type="text/javascript">
		$(function() {
			$("#tabs").tabs();
		});
	 
		function mostrar_div(nombrediv,valors){
	 
			document.getElementById(nombrediv).style.display=valors;
			if(valors=='block')		document.getElementById('verseleccion').style.display=valors;	else document.getElementById('verseleccion').style.display=valors;
			 
		 } 	
	</script>	
	
	
	

</head>
<body>
 	<form name="frmAddPro" method="post" action="gaccesos.php" onSubmit="marcarTodos('chosen[]');"  >
		<div class="demo">
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1"><?=_("MENUS DEL SISTEMA") ;?></a></li>
					<li><a href="#tabs-2"><?=_("ACCIONES") ;?></a></li>
					<li><a href="#tabs-3"><?=_("CUENTAS x USUARIOS") ;?></a></li>
				</ul>
				<div id="tabs-1">
					<table border="0" cellpadding="1" cellspacing="1" id="tabla_paises" style="border:1px solid #336600">
						<tr>
							<th style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("CATALOGOS") ;?></th>
							<th style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("INFORMES") ;?></th>
							<th style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("MODULOS") ;?></th>
							<th style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("PROCESOS") ;?></th>
							<th style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("SEGURIDAD") ;?></th>
						</tr>
						<tr><td bgcolor="#ECF5FF">							
								<table border="0" cellpadding="1" cellspacing="1" id="tabla_paises">
									<tr><td>
											<div id="main">
												<ul id="red" class="treeview-red"> 
												 <?
													$rsmenus=$con->query("SELECT substr(IDMODULO,6,30) as NOMBRECAT,IDMODULO,substr(DESCRIPCION,10,20)  as DESCRIPCION, TIPO FROM $con->temporal.seguridadmodulo where TIPO in('MENU_CAT') order by DESCRIPCION ");
													while($regmenu = $rsmenus->fetch_object())
													 {													 
														$rsusuaario=$con->query("SELECT IDMODULO FROM $con->temporal.acceso_plantillaperfil where IDPLANTILLAPERFIL='$perfil' and IDMODULO LIKE '".$regmenu->NOMBRECAT."%' order by IDMODULO");
															while($regusu = $rsusuaario->fetch_object())
															{
																$indice=$indice+1;
																if($regusu->IDMODULO ==$regmenu->NOMBRECAT."_AGREGAR")	{ $marcagr="checked";	$valorsagr=$regusu->IDMODULO; }
																if($regusu->IDMODULO ==$regmenu->NOMBRECAT."_EDITAR")	{ $marcaedit="checked"; $valorsedi=$regusu->IDMODULO; }
																if($regusu->IDMODULO ==$regmenu->NOMBRECAT."_ELIMINAR")	{	$marcaeli="checked";	$valorseli=$regusu->IDMODULO;}																
															}
															
															$indice=0;
															if(!in_array($regmenu->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearCatologo="DISABLED";
													?>
														<li><span><input type="checkbox" name="checkbox<?=($id+1); ?>"  onclick="marcarckb('<?=($id+1); ?>',document.frmAddPro.checkbox<?=($id+1); ?>)" <? if($marcagr!="" or  $marcaedit!="" or $marcaeli!="")	echo "checked"; else echo $bloquearCatologo; ?>   ><?=$regmenu->DESCRIPCION;?></span>
															<ul>
																<li><span><input type="checkbox" name="ckbpermisos[]" id="<?="MARCA".($id+1); ?>"  value="<?=$regmenu->NOMBRECAT."_AGREGAR"; ?>" onclick="verificarckb('<?=($id+1); ?>',document.frmAddPro.checkbox<?=($id+1); ?>)" <? if($marcagr)	echo $marcagr; else echo $bloquearCatologo;?> />Agregar</span></li>				 
																<li><span><input type="checkbox" name="ckbpermisos[]"  id="<?="MARCA".($id+2); ?>" value="<?=$regmenu->NOMBRECAT."_EDITAR"; ?>" onclick="verificarckb('<?=($id+1); ?>',document.frmAddPro.checkbox<?=($id+1); ?>)" <? if($marcaedit)	echo $marcaedit; else echo $bloquearCatologo;?>/>Editar</span></li>
																<li><span><input type="checkbox"name="ckbpermisos[]" id="<?="MARCA".($id+3); ?>" value="<?=$regmenu->NOMBRECAT."_ELIMINAR"; ?>" onclick="verificarckb('<?=($id+1); ?>',document.frmAddPro.checkbox<?=($id+1); ?>)" <? if($marcaeli)	echo $marcaeli; else echo $bloquearCatologo;?> />Eliminar</span></li>
																<!--input type="hidden" name="nombremenu[]" value="<? //=$regmenu->IDMODULO; ?>"  -->
															</ul>
														</li>
													<?
															$id=$id+3;
															$marcagr=""; $marcaedit="";	$marcaeli="";
															$bloquearCatologo="";
														}
													?>
												</ul>
	
										</div>
									</td></tr>
								</table>		
							</td>                            
 
                            <td bgcolor="#ECF5FF">
								<table border="0" cellpadding="1" cellspacing="1" id="tabla_paises" style="font-size:12px">
									<?
										$result=$con->query("SELECT IDMODULO,DESCRIPCION FROM $con->temporal.seguridadmodulo where TIPO='MENU_INF' order by DESCRIPCION  ");
										while($reg = $result->fetch_object())
										 {
											$ii=$ii+1;
											if(in_array($reg->IDMODULO,$accesos))	$marcar="checked";
											if(!in_array($reg->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearInforme="DISABLED";
									?>	
									<tr>
										<td><input type="checkbox" name="ckbpermisos[]" value="<?=$reg->IDMODULO; ?>" <? if($marcar and $marcar!="") echo $marcar; else echo $bloquearInforme;?> /></td>
										<td><?=strtoupper($reg->DESCRIPCION); ?></td>
						 
									</tr>
									<?
											$marcar="";
											$bloquearInforme="";
										
										 }
										 
										 
											for ($x = $ii+1 ; $x <= $data[0][0] +2; $x ++) {
									?>
									<tr>
										<td height="22"><input type="checkbox" name="ckbpermisosx[]" disabled /></td>
										<td>&nbsp;</td>
									</tr>
								
									<?						 
											}
										 						 
									?>
								</table>
							</td>
                             
							<td bgcolor="#ECF5FF">
								<table border="0" cellpadding="1" cellspacing="1" id="tabla_paises" style="font-size:12px">
									<?
										$result=$con->query("SELECT IDMODULO,DESCRIPCION FROM $con->temporal.seguridadmodulo where TIPO='MENU_MOD' order by DESCRIPCION  ");
										while($reg = $result->fetch_object())
										 {
											$ii=$ii+1;
											if(in_array($reg->IDMODULO,$accesos))	$marcar="checked";
											if(!in_array($reg->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearModulo="DISABLED";
									?>	
									<tr>
										<td><input type="checkbox" name="ckbpermisos[]" value="<?=$reg->IDMODULO; ?>" <? if($marcar and $marcar!="") echo $marcar; else echo $bloquearModulo;?> /></td>
										<td><?=strtoupper($reg->DESCRIPCION); ?></td>
						 
									</tr>
									<?
											$marcar="";
											$bloquearModulo="";
										
										 }
									 
											for ($x = $ii+1 ; $x <= $data[0][0] +7; $x ++) {
									?>
									<tr>
										<td height="22"><input type="checkbox" name="ckbpermisosx[]" disabled /></td>
										<td>&nbsp;</td>
									</tr>
								
									<?		 
										}						 
									?>
								</table>
							</td>

							<td bgcolor="#ECF5FF"><table border="0" cellpadding="1" cellspacing="1"  style="font-size:12px">
							<?		
								$i=0;	
								$result=$con->query("SELECT IDMODULO,DESCRIPCION FROM $con->temporal.seguridadmodulo where TIPO='MENU_PRO' order by DESCRIPCION  ");
								while($reg = $result->fetch_object())
								 {
								 $nn=$nn+1;
									if(in_array($reg->IDMODULO,$accesos))	$marcar="checked";	else $disaseg="disabled";
									if(!in_array($reg->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearProceso="DISABLED";
							?>	
								<tr>
									<td><input type="checkbox" name="ckbpermisos[]" value="<?=$reg->IDMODULO; ?>" <? if($marcar and $marcar!="") echo $marcar; else echo $bloquearProceso;?> /></td>
									<td><?=strtoupper($reg->DESCRIPCION); ?></td>
								</tr>
							<?
									$marcar="";		$disaseg="";
									$bloquearProceso="";
								 }
									
								if($nn!=$data[0][0])
								 {
									for ($x = $nn+1 ; $x <= $data[0][0]+2 ; $x ++) {
							?>
								<tr>
									<td height="22"><input type="checkbox" name="ckbpermisosx[]" disabled /></td>
									<td>&nbsp;</td>
								</tr>
							
							<? 
									}
								 } 
							?>
								</table>
							</td>							
							<td bgcolor="#ECF5FF"><table border="0" cellpadding="1" cellspacing="1" id="tabla_paises" style="font-size:12px">
							<?		
								$i=0;	
								$result=$con->query("SELECT IDMODULO,DESCRIPCION FROM $con->temporal.seguridadmodulo where TIPO='MENU_SEG' order by DESCRIPCION  ");
								while($reg = $result->fetch_object()){
									
									$nn=$nn+1;
									if(in_array($reg->IDMODULO,$accesos))	$marcar="checked";
									if(!in_array($reg->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearSeguridad="DISABLED";
							?>	
								<tr>
									<td><input type="checkbox" name="ckbpermisos[]" value="<?=$reg->IDMODULO; ?>" <? if($marcar and $marcar!="") echo $marcar; else echo $bloquearSeguridad;?> /></td>
									<td><?=strtoupper($reg->DESCRIPCION); ?></td>
								</tr>
							<?
									$marcar="";
									$bloquearSeguridad="";
								 }
																
									for ($x = $nn+1 ; $x <= $data[0][0]+14 ; $x ++) {
							?>
								<tr>
									<td height="22"><input type="checkbox" name="ckbpermisosx[]" disabled /></td>
									<td>&nbsp;</td>
								</tr>							
							<? 									
								 } 
							?>
								</table>
							</td></tr>
						</table>	
						<a href="javascript:seleccionar(1)"><?=_("MARCAR TODOS") ;?></a> | <a href="javascript:seleccionar(0)"><?=_("DESMARCAR TODOS") ;?></a> <!--a href="javascript:invertir()"><?//=_("INVERTIR SELECCION") ;?></a -->
					</div>
					<div id="tabs-2" style="background:#eaf4ff">						
						<table width="450" border="0" cellpadding="1" cellspacing="0" style="border:1px solid #336600;font-size:12px">
							<tr>
								<th WIDTH="170"  height="14" style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("OPCIONES") ;?>:</th>
								<th height="14"></th>
								<th WIDTH="170" height="14" style="text-align:left; background-color:  #336600;color:#FFFFFF"><?=_("PERMISOS ACTUALES") ;?>:</th>
							</tr>
							<tr>
								<td height="150">
									<select name="avail" size="7" id="avail" MULTIPLE style='font-size:10px;width:300px; height:200px' onDblClick="javascript: moveVals(2, 'avail', 'chosen[]'); frmButtons(); marcarTodos('chosen[]'); return false;" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this);'>								
										<? 			
											//$con->cmbselectdata("select IDMODULO,DESCRIPCION from $con->temporal.seguridadmodulo WHERE TIPO='ACCION' and  IDMODULO not like   'CAT%' and IDMODULO not in (select IDMODULO from $con->temporal.acceso_plantillaperfil  where IDPLANTILLAPERFIL='$perfil') order by IDMODULO ","avail",$selected,"MULTIPLE style='font-size:10px;width:300px; height:200px' onDblClick=\"javascript: moveVals(2, 'avail', 'chosen[]'); frmButtons(); marcarTodos('chosen[]'); return false;\" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this);'","2"); 
									 
											$result=$con->query("select IDMODULO,DESCRIPCION from $con->temporal.seguridadmodulo WHERE TIPO='ACCION' and  IDMODULO not like   'CAT%' and IDMODULO not in (select IDMODULO from $con->temporal.acceso_plantillaperfil  where IDPLANTILLAPERFIL='$perfil') order by DESCRIPCION");
											while($reg_accion = $result->fetch_object()){
												
												if(!in_array($reg_accion->IDMODULO,$reg_accesos) and $_SESSION["user"] !="ADMINISTRADOR")	$bloquearAccion="DISABLED";
											
										?>										
												<option value="<?=$reg_accion->IDMODULO ?>" <?=$bloquearAccion?> ><?=$reg_accion->DESCRIPCION ?></option>									
										<? 
												$bloquearAccion="";
											}											 
										?>
									</select>
								</td>
								<td ALIGN="CENTER" VALIGN="CENTER" height="150">
									<!--input TYPE="button" VALUE="&gt;&gt;" style="font-weight:bold" NAME="btnLL" onClick="javascript: moveVals(1, 'avail', 'chosen[]'); frmButtons(); marcarTodos('chosen[]'); return false;"><br-->
									<input TYPE="button" VALUE="&gt;" style="font-weight:bold" NAME="btnL" onClick="javascript: moveVals(2, 'avail', 'chosen[]'); frmButtons(); marcarTodos('chosen[]'); return false; "><br>
									<input TYPE="button" VALUE="&lt;"style="font-weight:bold" NAME="btnR" onClick="javascript: moveVals(3, 'avail', 'chosen[]'); frmButtons(); return false;"><br>
									<!--input TYPE="button" VALUE="&lt;&lt;" style="font-weight:bold" NAME="btnRR" onClick="javascript: moveVals(4, 'avail', 'chosen[]'); frmButtons(); marcarTodos('chosen[]'); return false;"-->
								</td>
								<td height="150">					  
								<? 
									$con->cmbselectdata("SELECT seguridadmodulo.IDMODULO,seguridadmodulo.DESCRIPCION from $con->temporal.seguridadmodulo inner join $con->temporal.acceso_plantillaperfil on acceso_plantillaperfil.IDMODULO= seguridadmodulo.IDMODULO where acceso_plantillaperfil.IDPLANTILLAPERFIL='$perfil' and seguridadmodulo.TIPO='ACCION' and seguridadmodulo.IDMODULO not like 'CAT%' order by seguridadmodulo.DESCRIPCION ","chosen[]",$selected,"MULTIPLE style='font-size:10px;width:300px; height:200px' onDblClick=\"javascript: moveVals(2, 'avail', 'chosen[]'); frmButtons(); return false;\" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this);'","2"); 
								?>
								</td>
							</tr>
					</table>				
				</div>				
				<div id="tabs-3">						
			
				<table width="63%" height="66" border="0" cellpadding="1" cellspacing="1" style="font-size:12px">
				  <tr>
					<td width="285"><table width="250" border="0" cellpadding="1" cellspacing="1">
					  <tr>
						<td width="20"><label>
						  <input name="rdseleccion" type="radio" id="radio" value="1" <?=($todoscuentas==1)?"checked":"";?> onclick="mostrar_div('resultado','none')">
						</label></td>
						<td width="95"><?=_("TODOS") ;?></td>
					  </tr>
					  <tr>
						<td><input type="radio" name="rdseleccion" id="radio2" value="2"  <?=($todoscuentas==0)?"checked":"";?> onclick="mostrar_div('resultado','block')"></td>
						<td><?=_("POR CUENTAS") ;?></td>
					  </tr>
					</table></td>
				 
				  </tr>
				</table>
  						
				<div id="resultado" Style="display:<?=($todoscuentas)?"none":"block";?>;overflow:auto;padding-top:1px; padding-Left:1px; padding-bottom:15px;height:198px; width:325px;">			
					<table  border="0" cellpadding="1" cellspacing="1" style="border:1px solid #333333;font-size:12px">							
					<? 										
						$rscuentas=$con->query("select IDCUENTA,NOMBRE from $con->catalogo.catalogo_cuenta order by NOMBRE ");
						  
						while($reg = $rscuentas->fetch_object()){						
							if(in_array($reg->IDCUENTA,$rowcuenta))	$valor="checked";
							if($c%2==0) $fondo='#CADCE3'; else $fondo='#F9F9F9';	
					?>								
							  <tr bgcolor="<?=$fondo;?>">
								<td colspan="2"><input type="checkbox" name="chkcuentas[]" value="<?=$reg->IDCUENTA; ?>"  <?=$valor;?> ><strong><?=$reg->NOMBRE; ?></strong></td>
							  </tr>
					<?
								$valor="";																	
								$c=$c+1;
								$fondo="";
							}
					?> 
					</table>							
				</div>
				<div id="verseleccion" Style="display:<?=($todoscuentas)?"none":"block";?>">
					<a href="javascript:seleccionar2(1)"><?=_("MARCAR TODOS") ;?></a> | <a href="javascript:seleccionar2(0)"><?=_("DESMARCAR TODOS") ;?></a> <!--a href="javascript:invertir()"><?//=_("INVERTIR SELECCION") ;?></a -->
				</div>				
				</div>		
			</div>
				<p><input type="submit" name="btnaceptar2" style="font-weight:bold" id="btnaceptar2" value="<?=_("GRABAR ACCESOS") ;?>" title="<?=_("GRABAR ACCESOS") ;?>" /></p>
				<input type="hidden" name="idperfil" id="idperfil" value="<?=$_GET["idperfil"];?>" />
		</div>
	</form>
</body>
</html>