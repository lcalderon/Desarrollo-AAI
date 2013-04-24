<?php

	session_start();
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
 	include_once("../../../modelo/afiliado/asistencias.class.php");
	include_once("../../includes/arreglos.php");
	
	Auth::required();

	$con = new DB_mysqli();	
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	$asis = new asistencias($_GET["asistencia"]);
	$infoasistencia=$asis->informacionAsistencia();  		
	$telefonos=explode(",",$infoasistencia["TELEFONOS"]);		

	$sql_auditoria="SELECT IDASISTENCIA,GRABACION,P1,P2,P3,P4,P5,A1,A2,A3,EVALUACIONAUDITOR,IDCOORDINADOR,IDAUDITOR,OBSERVACION FROM $con->temporal.asistencia_auditoria_calidad WHERE IDASISTENCIA='".$_GET["asistencia"]."'";
	$exec_auditoria =$con->query($sql_auditoria);
	//$nreg_auditoria=$exec_auditoria->num_rows;
	while($rset_auditoria=$exec_auditoria->fetch_object()){
		$c1 = $rset_auditoria->P1;
		$c2 = $rset_auditoria->P2;	
		$c3 = $rset_auditoria->P3;
		$c4 = $rset_auditoria->P4;
		$c5 = $rset_auditoria->P5;
		$a1 = $rset_auditoria->A1;
		$a2 = $rset_auditoria->A2;
		$a3 = $rset_auditoria->A3;
		$grabacion = $rset_auditoria->GRABACION;
		$evaluacionaudito = $rset_auditoria->EVALUACIONAUDITOR;
		$coordina = $rset_auditoria->IDCOORDINADOR;
		$usuaudito = $rset_auditoria->IDAUDITOR;
		$comenta = $rset_auditoria->OBSERVACION;
		$idAuditoria = $rset_auditoria->IDASISTENCIA;
	}

	$nombreaudio = explode("\\", $grabacion);

	$Sql_coordinador="SELECT
			catalogo_usuario.IDUSUARIO,
			CONCAT(
				catalogo_usuario.APELLIDOS,
				', ',
				catalogo_usuario.NOMBRES
			)AS NOMBRES
		FROM
			$con->catalogo.catalogo_usuario		
		INNER JOIN $con->temporal.grupo_usuario ON grupo_usuario.IDUSUARIO = catalogo_usuario.IDUSUARIO
		WHERE
		grupo_usuario.IDGRUPO = 'CORD'
		AND catalogo_usuario.ACTIVO = 1";	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title><?=_("Gestion Calidad");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript">
		function $(e){if(typeof e=='string')e=document.getElementById(e); return e};
			window.onload=function(){
				$('file').onchange=function(){
					inputFile=$('file');
					netscape.security.PrivilegeManager.enablePrivilege("UniversalFileRead");
					$('txtPath').value=inputFile.value;
				};
			};
		 
		function marcar_options(nombre){
			$(nombre).checked=true;
		}
	</script>
</head>
<body>
	<form name='frmaudito' id='frmaudito' method='POST' enctype="multipart/form-data">
		<input type='hidden' name='asistencia' value='<?=$_GET["asistencia"]?>'>
		<input type='hidden' name='idAuditoria' value='<?=$idAuditoria?>'>
		
		<table width="100%" border="1" style="border-collapse:collapse" bgcolor="#D2D3E3">
			<tbody>
				<tr>
					<td colspan="7" bgcolor="#2F5984" style="color:#FFFFFF;font-size:14px"><strong><?=_("AUDITO DE CAT: EVALUACION DE LA CALIDAD DE LA COMUNICACION")?></strong></td>
				</tr> 		
				<tr>
					<td align="right" colspan="7"><strong><?=_("STATUS")?></strong> 
					<?
						$con->cmb_array("cmbevaluacionAud",$evalauditoria,($infoasistencia["EVALAUDITORIA"] !="CERRADO")?"AUDITADO":"CERRADO","onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'",($infoasistencia["EVALAUDITORIA"]=="CERRADO")?"2":"1",_("TODOS"),"","SAUDITAR");
					?>
					</td>			
				</tr> 
				<tr>
					<td width="2%"><?=_("EXPEDIENTE")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><strong><?=$infoasistencia["IDEXPEDIENTE"]?></strong></td>
					<td width="13%"><?=_("ASISTENCIA")?></td>
					<td colspan="2" width="25%" bgcolor="#D5E9B6"><strong><?=$infoasistencia["IDASISTENCIA"]?></strong></td>
					<td rowspan="4" bgcolor="#ffff3c" width="19%"><div align="center"><strong><font color='red' size='08px'><b><?=$evaluacionaudito?></strong></div></td>
				</tr>                
				<tr>
					<td><?=_("FECHAREG.EXP.")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><strong><?=substr($infoasistencia["FECHAUSUARIOEXP"],0,19)?></strong></td>
					<td><?=_("USUARIOREG.EXP.")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><strong><?=substr($infoasistencia["FECHAUSUARIOEXP"],19,15)?></strong></td>
				</tr>			  
				<tr>
					<td><?=_("CUENTA")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><?=$infoasistencia["NOMBRECUENTA"]?></td>
					<td><?=_("PLAN")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><?=$infoasistencia["NOMBREPLAN"]?></td>
				</tr>
				<tr>
					<td ><?=_("STATUS ASIS.")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><?=$desc_status_asistencia[$infoasistencia["ARRSTATUSASISTENCIA"]]?></td>
					<td><?=_("SERVICIO")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><?=$infoasistencia["SERVICIO"]?></td>
				</tr>
				<tr>
					<td ><?=_("TELEFONO 1")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><input type='text' name="txtTelefono1" id="txtTelefono1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)' style="border-width:0" value="<?=$telefonos[0]?>" size='17' readonly><img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onClick="llamada($F('txtTelefono1'))"></td>
					<td><?=_("TELEFONO 2")?></td>
					<td colspan="3" bgcolor="#D5E9B6"><input type='text' name="txtTelefono2" id="txtTelefono2" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)' style="border-width:0" value="<?=$telefonos[1]?>" size='17' readonly><? if($telefonos[1]!=""){?><img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onClick="llamada($F('txtTelefono2'))"><? } ?></td>
				</tr>
				<tr>
					<td><?=_("COORDINADOR")?></td>
					<td colspan="2" bgcolor="#D5E9B6">
						<? $con->cmbselectdata($Sql_coordinador,"cmbcoordinador",$coordina,"onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'"); ?>
					</td>
					<td><?=_("AUDITOR")?></td>
					<td colspan="3" bgcolor="#D5E9B6"><strong><?=$usuaudito?></strong></td>
				</tr>
				<!--tr>
					<td><?=_("UBICACION")?></td>
					<td colspan="6" bgcolor="#D5E9B6">
					<?
						if($grabacion){
					?>
						<div id="div-fijo">
							<input type="text" name="txtrutafija" id="txtrutafija" readonly style="text-transform:none" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)' size="130" value="<?=$grabacion?>"/>
							<img src="../../../../imagenes/iconos/editars.gif" title="<?=_("Modificar")?>" style="cursor: pointer;" onclick="ocultarVisualizarDiv('div-fijo','div-examinar')" border="0">
						</div>
						<div id="div-examinar" style="display:none">
							<input type="file" name="file" id="file" size="120" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'/><img src="../../../../imagenes/iconos/cancel.gif" title="<?=_("Cancelar")?>" style="cursor: pointer;" onclick="ocultarVisualizarDiv('div-fijo','div-examinar')" border="0">
							<input type="hidden" name="txtruta" id="txtruta" style="text-transform:none" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'/>
						</div>						
					<? } else{ ?>
						<div id="div-examinar">
							<input type="file" name="file" id="file" size="120" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'/>
							<input type="hidden" name="txtruta" id="txtruta" style="text-transform:none" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'/>
						</div>
					<? } ?>
						<input type='hidden' name='txtPath' id='txtPath'>
					</td>
				</tr-->
				<tr>
					<td><?=_("COMENTARIO")?></td>
					<td colspan="6" >
						<textarea name="txtacomentario" id="txtacomentario" style="text-transform:uppercase;" cols="100" rows="1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><?=$comenta?></textarea>
					</td>	
				</tr>
			</tbody>
		</table>				  
		<br/>	  
		<table align="center" width='100%' border="1" style="border-collapse:collapse" bgcolor="#AECB8F">
			<tbody>
				<tr>
					<th width='12%' align="center" bgcolor="#719948">1<BR><?=_("PROTOCOLO TELEFONICO")?></th>
					<th width='12%' align="center" bgcolor="#719948">2<BR><?=_("ESCUCHA ACTIVA")?></th>
					<th width='12%' align="center" bgcolor="#719948">3<BR><?=_("MANEJO SPEECH Y PROCESO")?></th>
					<th width='12%' align="center" bgcolor="#719948">4<BR><?=_("SONRISA TELEFONICA")?></th>
					<th width='12%' align="center" bgcolor="#719948">5<BR><?=_("MANEJO DE TMO")?></th>				
				</tr>
				<tr>
					<td onclick="marcar_options('cumple_prot')"><input type='radio' name='p1' id='cumple_prot' value='1' <?=(isset($c1) && $c1==1)?'checked':''; ?>><?=_('CUMPLE')?></td>
					<td onclick="marcar_options('cumple_escucha')"><input type='radio' name='p2' id='cumple_escucha' value='1' <?= (isset($c2) && $c2==1)?'checked':'';?>><?=_('CUMPLE')?></td>
					<td onclick="marcar_options('cumple_manejo')"><input type='radio' name='p3' id='cumple_manejo' value='1' <?=(isset($c3) && $c3==1)?'checked':'';?>><?=_('CUMPLE')?></td>
					<td onclick="marcar_options('cumple_sonria')"><input type='radio' name='p4' id='cumple_sonria' value='1' <?=(isset($c4) && $c4==1)?'checked':'';?>><?=_('CUMPLE')?></td>
					<td onclick="marcar_options('cumple_tmo')"><input type='radio' name='p5' id='cumple_tmo' value='1' <?=(isset($c5) && $c5==1)?'checked':'';?>><?=_('CUMPLE')?></td>
				</tr>	
				<tr>
					<td onclick="marcar_options('regular_prot')"><input type='radio' name='p1' id='regular_prot' value='0.5' <?=(isset($c1) && $c1==0.5)?'checked':'';?>><?=_('REGULAR')?></td>
					<td onclick="marcar_options('regular_escucha')"><input type='radio' name='p2' id='regular_escucha' value='0.5' <?= (isset($c2) && $c2==0.5)?'checked':''; ?>><?=_('REGULAR')?></td>
					<td onclick="marcar_options('regular_manejo')"><input type='radio' name='p3' id='regular_manejo' value='0.5' <?=(isset($c3) && $c3==0.5)?'checked':''; ?>><?=_('REGULAR')?></td>
					<td onclick="marcar_options('regular_sonrisa')"><input type='radio' name='p4' id='regular_sonrisa' value='0.5' <?=(isset($c4)&& $c4==0.5)?'checked':''; ?>><?=_('REGULAR')?></td>
					<td onclick="marcar_options('regular_tmo')"><input type='radio' name='p5' id='regular_tmo' value='0.5' <?=(isset($c5) && $c5==0.5)?'checked':'';?>><?=_('REGULAR')?></td>
				</tr>	
				<tr>
					<td onclick="marcar_options('ncumple_prot')"><input type='radio' name='p1' id='ncumple_prot' value='0' <?=(isset($c1) && $c1==0)?'checked':''; ?>><?=_('NO CUMPLE')?></td>
					<td onclick="marcar_options('ncumple_escucha')"><input type='radio' name='p2' id='ncumple_escucha' value='0' <?=(isset($c2) && $c2==0)?'checked':''; ?>><?=_('NO CUMPLE')?></td>
					<td onclick="marcar_options('ncumple_manejo')"><input type='radio' name='p3' id='ncumple_manejo' value='0' <?=(isset($c3) && $c3==0)?'checked':'';?>><?=_('NO CUMPLE')?></td>
					<td onclick="marcar_options('ncumple_sonrisa')"><input type='radio' name='p4' id='ncumple_sonrisa' value='0' <?=(isset($c4)&& $c4==0)?'checked':''; ?>><?=_('NO CUMPLE')?></td>
					<td onclick="marcar_options('ncumple_tmo')"><input type='radio' name='p5' id='ncumple_tmo' value='0' <?=(isset($c5) && $c5==0)?'checked':''; ?>><?=_('NO CUMPLE')?></td>
				</tr>				
				<!--tr>
					<td>
						<input type='radio' name='p1' value='1' <?=(isset($c1) && $c1==1)?'checked':''; ?>><?=_('CUMPLE')?><br>
						<input type='radio' name='p1' value='0.5' <?=(isset($c1) && $c1==0.5)?'checked':'';?>><?=_('REGULAR')?><br>
						<input type='radio' name='p1' value='0' <?=(isset($c1) && $c1==0)?'checked':''; ?>><?=_('NO CUMPLE')?>
					</td>
					<td>
						<input type='radio' name='p2' id='p2' value='1' <?= (isset($c2) && $c2==1)?'checked':'';?>><?=_('CUMPLE')?><br>
						<input type='radio' name='p2' id='p2' value='0.5' <?= (isset($c2) && $c2==0.5)?'checked':''; ?>><?=_('REGULAR')?><br>
						<input type='radio' name='p2' id='p2' value='0' <?=(isset($c2) && $c2==0)?'checked':''; ?>><?=_('NO CUMPLE')?>
					</td>
					<td>
						<input type='radio' name='p3'  value='1' <?=(isset($c3) && $c3==1)?'checked':'';?>><?=_('CUMPLE')?><br>
						<input type='radio' name='p3'  value='0.5' <?=(isset($c3) && $c3==0.5)?'checked':''; ?>><?=_('REGULAR')?><br>
						<input type='radio' name='p3'  value='0' <?=(isset($c3) && $c3==0)?'checked':'';?>><?=_('NO CUMPLE')?>
					</td>
					<td>
						<input type='radio' name='p4'  value='1' <?=(isset($c4) && $c4==1)?'checked':'';?>><?=_('CUMPLE')?><br>
						<input type='radio' name='p4'  value='0.5' <?=(isset($c4)&& $c4==0.5)?'checked':''; ?>><?=_('REGULAR')?><br>
						<input type='radio' name='p4'  value='0' <?=(isset($c4)&& $c4==0)?'checked':''; ?>><?=_('NO CUMPLE')?>
					</td>
					<td>
						<input type='radio' name='p5'  value='1' <?=(isset($c5) && $c5==1)?'checked':'';?>><?=_('CUMPLE')?><br>
						<input type='radio' name='p5'  value='0.5' <?=(isset($c5) && $c5==0.5)?'checked':'';?>><?=_('REGULAR')?><br>
						<input type='radio' name='p5'  value='0' <?=(isset($c5) && $c5==0)?'checked':''; ?>><?=_('NO CUMPLE')?>
					</td>
				</tr -->
			</tbody>
		</table>
		<br/>
		<table width='100%' border="1" style="border-collapse:collapse" bgcolor="#AECB8F">
			<tbody>
				<tr>
					<th width='8%' bgcolor="#719948"><?=_("MANEJO DE CONEXION")?></th>
					<th width='8%' bgcolor="#719948"><?=_("MANEJO CL CRITICO")?></th>
					<th width='8%' bgcolor="#719948"><?=_("USA FRASES NEGATIVAS")?></th>
				</tr>
				<tr>
					<td onclick="marcar_options('siconexion')"><input type='radio' name='a1' id="siconexion" value='1' <?=(isset($a1) && $a1==1)?'checked':'';?>><?=_('SI')?></td>
					<td onclick="marcar_options('sicritico')"><input type='radio' name='a2' id="sicritico" value='1' <?=(isset($a2) && $a2==1)?'checked':''; ?>><?=_('SI')?></td>
					<td onclick="marcar_options('sifrases')"><input type='radio' name='a3' id="sifrases" value='1' <?=(isset($a3) && $a3==1)?'checked':''; ?>><?=_('SI')?></td>
				</tr>
				<tr>
					<td onclick="marcar_options('noconexion')"><input type='radio' name='a1'  value='0' id="noconexion" <?=(isset($a1) && $a1==0)?'checked':'';?>><?=_('NO')?></td>
					<td onclick="marcar_options('nocritico')"><input type='radio' name='a2'  value='0' id="nocritico" <?=(isset($a2) && $a2==0)?'checked':''; ?>><?=_('NO')?></td>
					<td onclick="marcar_options('nofrases')"><input type='radio' name='a3'  value='0' id="nofrases" <?=(isset($a3) && $a3==0)?'checked':''; ?>><?=_('NO')?></td>
				</tr>
				<tr>
					<td onclick="marcar_options('naconexion')"><input type='radio' name='a1'  value='-1' id="naconexion" <?=(isset($a1) && $a1==-1)?'checked':'';?>><?=_('NO APLICA')?></td>
					<td onclick="marcar_options('nacritico')"><input type='radio' name='a2'  value='-1' id="nacritico" <?=(isset($a2) && $a2==-1)?'checked':''; ?>><?=_('NO APLICA')?></td>
					<td onclick="marcar_options('nafrases')"><input type='radio' name='a3'  value='-1' id="nafrases" <?=(isset($a3) && $a3==-1)?'checked':''; ?>><?=_('NO APLICA')?></td>
				</tr>				
				<!--tr>
					<td valign='top'>
						<input type='radio' name='a1'  value='1' <?=(isset($a1) && $a1==1)?'checked':'';?>><?=_('SI')?><br/>
						<input type='radio' name='a1'  value='0' <?=(isset($a1) && $a1==0)?'checked':'';?>><?=_('NO')?><br/>
						<input type='radio' name='a1'  value='-1' <?=(isset($a1) && $a1==-1)?'checked':'';?>><?=_('NO APLICA')?>
						
					</td>
					<td valign='top'>
						<input type='radio' name='a2'  value='1' <?=(isset($a2) && $a2==1)?'checked':''; ?>><?=_('SI')?><br/>
						<input type='radio' name='a2'  value='0' <?=(isset($a2) && $a2==0)?'checked':''; ?>><?=_('NO')?><br/>
						<input type='radio' name='a2'  value='-1' <?=(isset($a2) && $a2==-1)?'checked':''; ?>><?=_('NO APLICA')?>
					</td>
					<td valign='top'>
						<input type='radio' name='a3'  value='1' <?=(isset($a3) && $a3==1)?'checked':''; ?>><?=_('SI')?><br/>
						<input type='radio' name='a3'  value='0' <?=(isset($a3) && $a3==0)?'checked':''; ?>><?=_('NO')?><br/>
						<input type='radio' name='a3'  value='-1' <?=(isset($a3) && $a3==-1)?'checked':''; ?>><?=_('NO APLICA')?>
					</td>
				</tr -->
			</tbody>
		</table>				  
		<table align="right">
			<tbody>
				<tr>					
					<td><input type='button' id='btn_grabar' value='Grabar' class='guardar' style="font-weight:bold" onClick="EnviarAudito()" <? if($infoasistencia["EVALAUDITORIA"] =="CERRADO") echo "disabled";?> ><input type='button' id='btn_salir' value='Salir' class='cancelar' onClick="parent.win.close();" ></td>
				</tr>
			</tbody>
		</table>
	</form>
</body>
<script language='javascript'>

	function llamada(numero){

		numero = numero;
		new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',{
			method : 'get',
			parameters: {
				prefijo: "",
				num: numero,
				ext: '<?=$idextension?>'
			}
		});
		
		return;
	}

	function EnviarAudito(){
    
		if (!comp_seleccion(document.frmaudito.p1)) alert("<?=_('NO HA SELECCIONADO LA OPCION EN PROTOCOLO TELEFONICO')?>");
		else if (!comp_seleccion(document.frmaudito.p2)) alert("<?=_('NO HA SELECCIONADO LA OPCION EN ESCUCHA ACTIVA')?>");
		else if (!comp_seleccion(document.frmaudito.p3)) alert("<?=_('NO HA SELECCIONADO LA OPCION EN MANEJO SPEECH Y PROCESO')?>");
		else if (!comp_seleccion(document.frmaudito.p4)) alert("<?=_('NO HA SELECCIONADO LA OPCION SONRISA TELEFONICA')?>");
		else if (!comp_seleccion(document.frmaudito.p5)) alert("<?=_('NO HA SELECCIONADO LA OPCION MANEJO DE TMO')?>");
		else if (!comp_seleccion(document.frmaudito.a1)) alert("<?=_('NO HA SELECCIONADO LA OPCION MANEJO DE CONEXION')?>");
		else if (!comp_seleccion(document.frmaudito.a2)) alert("<?=_('NO HA SELECCIONADO LA OPCION MANEJO CL CRITICO')?>");
		else if (!comp_seleccion(document.frmaudito.a3)) alert("<?=_('NO HA SELECCIONADO LA OPCION USA FRASES NEGATIVAS')?>");
		else	
		
		if(confirm("<?=_('ESTA SEGURO QUE DESEA GUARDAR LA AUDITORIA?')?>")){
			document.getElementById('frmaudito').action='grabarauditoria.php';
			document.getElementById('frmaudito').submit()
		}
	}

	function Cerrar(){
		parent.win.close();
		return;
	}

	function comp_seleccion(campo){
		var sw= false;
		var n =campo.length;
		for (i=0; i<n;i++)
			if (campo[i].checked == true) sw=true;
			return sw;
	}
</script>