<?php

	session_start();
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../../modelo/afiliado/asistencias.class.php");
	include_once("../../includes/arreglos.php");
	
	Auth::required();

	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

		$asis = new asistencias($_GET["asistencia"]);
		$infoasistencia=$asis->informacionAsistencia();
		
		$telefonos=explode(",",$infoasistencia["TELEFONOS"]);
		$exec_encuesta =$con->query("SELECT * FROM  $con->temporal.asistencia_encuesta_calidad WHERE IDASISTENCIA='".$_GET["asistencia"]."'");
		$nreg_encuesta=$exec_encuesta->num_rows;
		while($rset_encuesta=$exec_encuesta->fetch_object()){
		
			$c1 = $rset_encuesta->C1;
			$c2 = $rset_encuesta->C2;	
			$c3 = $rset_encuesta->C3;
			$c4 = $rset_encuesta->C4;
			$c5 = $rset_encuesta->C5;
			$t1 = $rset_encuesta->T1;
			$t2 = $rset_encuesta->T2;
			$t3 = $rset_encuesta->T3;
			$t4 = $rset_encuesta->T4;
			$t5 = $rset_encuesta->T5;
			$g1 = $rset_encuesta->G1;
			$g2 = $rset_encuesta->G2;
			$g3 = $rset_encuesta->G3;
			$g4 = $rset_encuesta->G4;
			$g5 = $rset_encuesta->G5;
			$g6 = $rset_encuesta->G6;
			$nombreinfo = $rset_encuesta->NOMBREINFORMANTE;
			$otros = $rset_encuesta->OTROS;
			$usuarioencuesta = $rset_encuesta->IDUSUARIOMOD;
			$emails = $rset_encuesta->EMAILS;
			$comenta = $rset_encuesta->COMENTARIO;
			$evaluacionaudito = $rset_encuesta->EVALENCUESTA;
			$idEncuesta = $rset_encuesta->IDASISTENCIA;		
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title><?=_("Gestion Calidad");?></title>
	<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
</head>
<body>
	<form name='frmencuesta' id='frmencuesta' method='post'>
		<input type="hidden" name="asistencia" value="<?=$infoasistencia["IDASISTENCIA"]?>">
		<input type='hidden' name='idEncuesta' value='<?=$idEncuesta?>'>
		
		<table width="100%" border="1" style="border-collapse:collapse" bgcolor="#BCD7E0">
			<tbody>
				<tr>
					<td colspan="7" bgcolor="#38385E" style="color:#FFFFFF;font-size:14px"><strong><?=_("GESTIONAR ENCUESTA")?></strong></td>
				</tr> 		
				<tr>
					<td align="right" colspan="7"><strong><?=_("STATUS")?></strong>
					<?
						$con->cmb_array("cmbevaluacionenc",$evalencuesta_new,($infoasistencia["ARRSTATUSENCUESTA"] !="SEVA")?$infoasistencia["ARRSTATUSENCUESTA"]:"EVAL","onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'",($infoasistencia["ARRSTATUSENCUESTA"]=="CERR")?"2":"1","","","SEVA");
					?>
					</td>			
				</tr> 
				<tr>
					<td width="2%"><?=_("EXPEDIENTE")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><strong><?=$infoasistencia["IDEXPEDIENTE"]?></strong></td>
					<td width="13%"><?=_("ASISTENCIA")?></td>
					<td colspan="2" width="25%" bgcolor="#D5E9B6"><strong><?=$infoasistencia["IDASISTENCIA"]?></strong></td>
					<td rowspan="5" bgcolor="#ffff3c" width="19%"><div align="center"><strong><font color='red' size='08px'><b><?=$evaluacionaudito?></strong></div></td>
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
					<td colspan="2" bgcolor="#D5E9B6"><input type='text' name="txtTelefono2" id="txtTelefono2" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)' style="border-width:0" value="<?=$telefonos[1]?>" size='17' readonly><? if($telefonos[1]!=""){?><img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onClick="llamada($F('txtTelefono2'))"><? } ?></td>
				</tr>
				<tr>
					<td><?=_("AFILIADO")?></td>
					<td colspan="2" bgcolor="#D5E9B6"><?=$infoasistencia["NOBREAFILIADO"]?></td>	
 					<td><?=_("ENCUESTADOR")?></td>
					<td colspan="3" bgcolor="#719948" style="color:#FFFFFF"><strong><?=$usuarioencuesta?></strong></td>
				</tr> 
				<tr bgcolor="#F4F4F4">
					<td><strong><?=_("EMAILS")?></strong></td>
					<td colspan="2"><input type="text" name="txtemail" id="txtemail" style="text-transform:uppercase;" size="49" value="<?=$emails?>" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'/></td>
					<td><strong><?=_("COMENTARIO")?></strong></td>
					<td colspan="3" ><textarea name="txtacomentario" id="txtacomentario" style="text-transform:uppercase;" cols="65" rows="1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><?=$comenta?></textarea></td>
				</tr>
			</tbody>       
		</table>
 	
		<table  width="100%" border="1" style="border-collapse:collapse" bgcolor="#B2CD94">
			<tr bgcolor="#719948">
				<td><strong><?=_("LA PERSONA QUE ATENDIO SU LLAMADA TELEFONICA")?></strong></td>
				<td><strong><?=_("EL TECNICO QUE ATENDIO SU PROBLEMA")?></strong></td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0">
						<tr>
							<td ><?=_('1 - LO ESCUCHO ATENTAMENTE ?')?></td>
							<td><select id="p1" name="p1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($c1=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($c1=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0"  <? if($c1=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($c1=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>			
						</tr>
						<tr>
							<td><?=_('2 - SE EXPRESABA DE FORMA CLARA Y FACIL DE ENTENDER ?')?></td>
							<td><select id="p2" name="p2" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($c2=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($c2=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0"  <? if($c2=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($c2=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('3 - ENTENDIO SU PROBLEMA ?')?></td>
							<td><select id="p3" name="p3" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($c3=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($c3=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($c3=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($c3=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('4 - FUE AMABLE ?')?></td>
							<td><select id="p4" name="p4" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($c4=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($c4=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($c4=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($c4=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('5 - PUDO BRINDARLE LA AYUDA QUE ESPERABA ?')?></td>
							<td><select id="p5" name="p5" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($c5=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($c5=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($c5=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($c5=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" border="0">
						<tr>
							<td ><?=_('1 - TENIA PREVIO CONOCIMIENTO DEL PROBLEMA A RESOLVER?')?></td>
							<td ><select id="t1" name="t1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($t1=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($t1=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($t1=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($t1=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						
						</tr>
						<tr>
							<td><?=_('2 - LE EXPLICO EL PROBLEMA Y LAS TAREAS A REALIZAR ?')?></td>
							<td><select id="t2" name="t2" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($t2=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($t2=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($t2=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($t2=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('3 - FUE AMABLE Y RESPETUOSO ?')?></td>
							<td><select id="t3" name="t3" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($t3=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($t3=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($t3=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="100" <? if($t3=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('4 - TRABAJO CON EFICIENCIA Y LIMPIEZA ?')?></td>
							<td><select id="t4" name="t4" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($t4=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($t4=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($t4=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($t4=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
						<tr>
							<td><?=_('5 - ARRIBO EN LOS HORARIOS COORDINADOS POR TELEFONO ? ')?></td>
							<td><select id="t5" name="t5" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($t5=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($t5=='1'){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($t5=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($t5=='101'){ echo 'selected' ; }?>><?=_('NS/NC')?></option></select></td>				
						</tr>
					</table>
				</td>
			</tr>  
			<tr bgcolor="#719948">
				<td colspan="2"><strong><?=_("SATISFACCION GLOBAL")?></strong></td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0">
						<tr>
							<td><?=_("1 - RAPIDEZ DE LA RESPUESTA TELEFONICA")?></td>
							<td><select id="s1" name="s1" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g1=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="10" <? if($g1==10){ echo 'selected' ; }?>><?=_('EXCELENTE')?></option><option value="7.5" <? if($g1==7.5){ echo 'selected' ; }?>><?=_('MUY BUENO')?></option><option value="5" <? if($g1=='5'){ echo 'selected' ; }?>><?=_('BUENO')?></option><option value="2.5" <? if($g1=='2.5'){ echo 'selected' ; }?>><?=_('REGULAR')?></option><option value="0" <? if($g1=='0'){ echo 'selected' ; }?>><?=_('MALO')?></option></select></td>
						</tr>
						<tr>
							<td><?=_("2 - RAPIDEZ EN LA ASIGNACIN DEL TECNICO")?></td>
							<td><select id="s2" name="s2" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g2=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="10" <? if($g2==10){ echo 'selected' ; }?>><?=_('EXCELENTE')?></option><option value="7.5" <? if($g2==7.5){ echo 'selected' ; }?>><?=_('MUY BUENO')?></option><option value="5" <? if($g2==5){ echo 'selected' ; }?>><?=_('BUENO')?></option><option value="2.5" <? if($g2=='2.5'){ echo 'selected' ; }?>><?=_('REGULAR')?></option><option value="0" <? if($g2=='0'){ echo 'selected' ; }?>><?=_('MALO')?></option></select></td>
						</tr>
						<tr>
							<td><?=_("3 - SATISFACCION GENERAL CON NUESTROS TELEFONISTAS")?></td>
							<td><select id="s3" name="s3" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g3=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="10" <? if($g3==10){ echo 'selected' ; }?>><?=_('MUY SATISFECHO')?></option><option value="7" <? if($g3==7){ echo 'selected' ; }?>><?=_('SATISFECHO')?></option><option value="4" <? if($g3==4){ echo 'selected' ; }?>><?=_('POCO SATISFECHO')?></option><option value="0" <? if($g3=='0'){ echo 'selected' ; }?>><?=_('INSATISFECHO')?></option></select></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" border="0">
						<tr>
							<td><?=_("4 - CALIDAD DE LA SOLUCION TECNICA")?></td>
							<td><select id="s4" name="s4" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g4=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="10" <? if($g4==10){ echo 'selected' ; }?>><?=_('EXCELENTE')?></option><option value="7.5" <? if($g4==7.5){ echo 'selected' ; }?>><?=_('MUY BUENO')?></option><option value="5" <? if($g4==5){ echo 'selected' ; }?>><?=_('BUENO')?></option><option value="2.5" <? if($g4=='2.5'){ echo 'selected' ; }?>><?=_('REGULAR')?></option><option value="0" <? if($g4=='0'){ echo 'selected' ; }?>><?=_('MALO')?></option></select></td>
						</tr>
						<tr>
							<td><?=_("5 - SATISFACCION GENERAL CON NUESTROS TECNICOS")?></td>
							<td><select id="s5" name="s5" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g5=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="10" <? if($g5==10){ echo 'selected' ; }?>><?=_('MUY SATISFECHO')?></option><option value="7" <? if($g5==7){ echo 'selected' ; }?>><?=_('SATISFECHO')?></option><option value="4" <? if($g5==4){ echo 'selected' ; }?>><?=_('POCO SATISFECHO')?></option><option value="0" <? if($g5=='0'){ echo 'selected' ; }?>><?=_('INSATISFECHO')?></option></select></td>
						</tr>
						<tr>
							<td height="25"><?=_("6 - RECOMENDARIA UD NUESTROS SERVICIOS?")?></td>
						   <td><select id="s6" name="s6" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'><option value="100" <? if($g6=='100' || $nreg_encuesta=='0'){ echo 'selected'; }?>><?=_('SELECCIONE')?></option><option value="1" <? if($g6==1){ echo 'selected' ; }?>><?=_('SI')?></option><option value="0" <? if($g6=='0'){ echo 'selected' ; }?>><?=_('NO')?></option><option value="101" <? if($g6=='101'){ echo 'selected' ; }?>><?=_('TAL VEZ')?></option></select></td>
						</tr>
					</table>
				</td>
			</tr>  
		</table>

		<table align="right">
			<tbody>
				<tr>
					<td><strong><?=_("GRACIAS POR RESPONDER ESTA ENCUESTA")?></strong> <input type='button' id='btn_grabar' value='Grabar' <? if($infoasistencia["ARRSTATUSENCUESTA"] =="CERR") echo "disabled";?> class='guardar' style="font-weight:bold" onClick="EnviarEncuesta()" <? if($asis->arrstatusencuesta =="CERR") echo "disabled";?> ><input type='button' id='btn_salir' value='Salir' class='cancelar' onClick="parent.win.close();"></td>
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
			parameters:{
				prefijo: "",
				num: numero,
				ext: '<?=$idextension?>'
			},
			onSuccess: function(t){	}
		});
		
		return;
	}

	function EnviarEncuesta(){

		if(confirm("<?=_('ESTA SEGURO QUE DESEA GUARDAR LA ENCUESTA?')?>")){
		  
			document.getElementById('frmencuesta').action='grabarencuesta.php';
			document.getElementById('frmencuesta').submit();
		}	  
	}

</script>