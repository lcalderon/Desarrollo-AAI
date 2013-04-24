<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../includes/arreglos.php");
	include_once('../../../modelo/functions.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../Catalogos.class.php");
	
	$con = new DB_mysqli();	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();
	Auth::required($_SERVER['REQUEST_URI']);

// filtrando segun el numero de caso

	$Sql="SELECT
			  catalogo_afiliado_persona.IDAFILIADO,
			  catalogo_afiliado.CVEAFILIADO,
			  catalogo_afiliado.AFILIADO_SISTEMA,
			  retencion.STATUS_RETENCION_AFILIADO,
			  retencion.ARRVALIDEZ,
			  retencion.STATUS_SEGUIMIENTO,
			  CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,',', catalogo_afiliado_persona.NOMBRE) AS nombre,
			  catalogo_afiliado_persona.IDDOCUMENTO,
			  retencion.IDAFILIADO,
			  retencion.IDRETENCION,
			  LEFT(retencion.FECHARETENCION,10)         AS fecharet,
			  RIGHT(retencion.FECHARETENCION,8)         AS horaret,
			  retencion.IDCUENTA,
			  retencion.IDPROGRAMA,
			  retencion.MOTIVOLLAMADA,
			  catalogo_detallemotivollamada.DESCRIPCION,
			  retencion.MESREINTEGRO,
			  retencion.DEFENSACONSUMIDOR,
			  retencion.IDGRUPO,
			  retencion.RESPUESTAFORMAL,
			  retencion.FECHAEJECUSION,
			  retencion.MONTOSOLICITADO,			  
			  retencion.IDUSUARIO,
			  retencion.ARRPROCEDENCIA,
			  catalogo_grupo.NOMBRE AS RESPONSABLE,
			  retencion.FECHADISPOSICION,
			  retencion.IDEXPEDIENTE,
			  retencion.IDASISTENCIA,
			  retencion.IDDETMOTIVOLLAMADA,
			  retencion.COMENTARIO
			FROM $con->temporal.retencion			  
			  LEFT JOIN catalogo_detallemotivollamada
				ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA					  
			  LEFT JOIN catalogo_grupo
				ON catalogo_grupo.IDGRUPO = retencion.IDGRUPO				
			  INNER JOIN catalogo_afiliado
				ON catalogo_afiliado.IDAFILIADO = retencion.IDAFILIADO
			  INNER JOIN catalogo_afiliado_persona
				ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			WHERE retencion.IDRETENCION=".$_GET["idcaso"];
 
	$result=$con->query($Sql);
	$row = $result->fetch_object();
 
	$Sql_seg="SELECT
		  retencion_seguimiento.ARRVALIDEZ,
		  retencion.MOTIVOLLAMADA,
		  retencion.STATUS_SEGUIMIENTO,
		  retencion_seguimiento.IDGRUPO,
		  retencion_seguimiento.RESPUESTAFORMAL,		  
		  retencion_seguimiento.COMENTARIO,
		  retencion_seguimiento.IDSEGUIMIENTO,
		  retencion_seguimiento.FECHARETENCION,  
		  retencion_seguimiento.COMENTARIO,	  
		  retencion_seguimiento.FECHADISPOSICION,
		  retencion_seguimiento.DEFENSACONSUMIDOR,
		  retencion.ARRPROCEDENCIA,
		  retencion_seguimiento.IDUSUARIO,
		  catalogo_grupo.NOMBRE
		FROM $con->temporal.retencion_seguimiento
			INNER JOIN $con->temporal.retencion
				ON retencion.IDRETENCION = retencion_seguimiento.IDRETENCION	
			LEFT JOIN $con->catalogo.catalogo_grupo
				ON catalogo_grupo.IDGRUPO = retencion_seguimiento.IDGRUPO			
		WHERE retencion_seguimiento.IDRETENCION=".$_GET["idcaso"]." ORDER BY retencion_seguimiento.IDSEGUIMIENTO DESC";
 
	$rs_seg=$con->query($Sql_seg);
	
	$Sql_rec="SELECT DISTINCT
			  catalogo_detallemotivollamada.MOTIVOLLAMADA
			FROM $con->temporal.retencion
			  INNER JOIN $con->catalogo.catalogo_detallemotivollamada
				ON catalogo_detallemotivollamada.IDDETMOTIVOLLAMADA = retencion.IDDETMOTIVOLLAMADA
			WHERE retencion.IDRETENCION = '".$_GET["idcaso"]."' ";

	$reclamo=$con->consultation($Sql_rec);
	
	if($reclamo[0][0] =="QUEJASRECLAMO")	$accesocaso=acceso_casoseguimiento($_GET["idcaso"]); 	else $accesocaso=1;
	if($_GET["desafiliacionOk"])	$resp_motivoLlamada=$con->consultation("SELECT DESCRIPCION from $con->catalogo.catalogo_motivoscancelacionAON WHERE IDMOTIVOCANCELACION=$row->IDDETMOTIVOLLAMADA");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>AMERICAN ASSIST</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>			
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css"/>		
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>
		
		<style type="text/css">
			<!--
			.style1 {
				color: #FFFFFF;
				font-weight: bold;
			}
			-->
		</style>

	<script type="text/javascript">
		function validarIngreso(variable){

			if($('txtsegimiento').value==""){
                 alert('<?=_("INGRESE EL DETALLE DEL CASO.") ;?>');
                  $('txtsegimiento').focus();
                  return (false);
			} else if($('desafiliacionOk').value ==1 && $('cmbstatus').value =='CON' ){				
 
				if(confirm('<?=_("*** ESTA SEGURO DE CONCLUIR EL SEGUIMIENTO, UNA VEZ CONCLUIDO SE PROCEDERA A DESAFILIAR AL AFILIADO: STATUS AFIL.= CANCELADO ***");?>')){
					return (true);
				}				
				 return (false);
			}
			
			 return (true);
		}
	</script>
	
</head>

<body>
<form id="form1" name="form1" method="post" action="gseguimiento.php"  onSubmit = "return validarIngreso(this)">
<input type="hidden" name="idcaso" id="idcaso" value="<?=$_GET["idcaso"];?>"/>
<input name="chkdefensacon2" type="hidden" value="<?=$row->DEFENSACONSUMIDOR;?>" >
<input name="chkfromal2" type="hidden" value="<?=$row->RESPUESTAFORMAL;?>" >
<input name="txtfechactual" id="txtfechactual" type="hidden" value="<?=date("Y-m-d");?>" >
<input name="txtareaori" type="hidden" value="<?=$row->IDGRUPO;?>" >
<input name="idafiliado" type="hidden" value="<?=$row->IDAFILIADO;?>" >
<input name="desafiliacionOk" id="desafiliacionOk" type="hidden" value="<?=$_REQUEST["desafiliacionOk"]?>" >

	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CADBF0" style="border:1px dashed #464646">
		<tr bgcolor="#464646">
			<td colspan="4"><div align="center" class="style1"><?=_("DETALLE DEL CASO") ;?></div></td>
		</tr>
		<tr>
			<td width="120px" ><?=_("# CASO") ;?></td>
			<td bgcolor="#FFFFEA" width="30%"><STRONG><?=$row->IDRETENCION; ?></STRONG></td>
			<td width="120px"><?=_("EXPED./ASIST.") ;?></td>
			<td bgcolor="#FFFFEA" style="color:red"><strong><? if($row->IDEXPEDIENTE){ echo $row->IDEXPEDIENTE." - ".$row->IDASISTENCIA; }?></strong></td>
		</tr>
		<tr>
			<td><?=_("FECHA") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->fecharet; ?></td>
			<td><?=_("HORA") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->horaret; ?></td>
		</tr>  
		<tr>
			<td><?=_("CUENTA") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->IDCUENTA; ?></td>
			<td><?=_("PROGRAMA") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->IDPROGRAMA; ?></td>
		</tr>  
		<tr>
			<td><?=_("NOMBRE") ;?></td>
			<td bgcolor="#FFFFEA"><?=utf8_encode($row->nombre); ?></td>
			<td><?=_("USUARIO") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->IDUSUARIO; ?></td>
		</tr>  
		<tr>
			<td><?=_("GESTION") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->MOTIVOLLAMADA; ?></td>
			<td><?=_("MOTIVO") ;?></td>
			<td bgcolor="#FFFFEA"><?=($resp_motivoLlamada[0][0])?$resp_motivoLlamada[0][0]:utf8_encode($row->DESCRIPCION); ?></td>
		</tr>
<? if($row->MOTIVOLLAMADA=="REINTEGRO" || $row->MOTIVOLLAMADA=="QUEJASRECLAMO"){ ?> 		
		<tr>
			<td><?=_("AREA RESP.") ;?></td>
			<td bgcolor="#FFFFEA"><?=($row->RESPONSABLE)?$row->RESPONSABLE:"S/D"; ?></td>
			<td><?=_("PROCEDENCIA") ;?></td>
			<td bgcolor="#FFFFEA"><?=$procedencia_mediogestion[$row->ARRPROCEDENCIA]; ?></td>
		</tr> 
 <? }?>		
 <? if($row->MOTIVOLLAMADA=="REINTEGRO"){ ?> 
		<tr bgcolor="#ffb3b3">
			<td><?=_("MES SOLICITADO");?></td>
			<td><label><input name="txtmessolicitado" type="text" class='classtexto' id="txtmessolicitado" onFocus='coloronFocus(this);' onBlur="colorOffFocus(this);document.getElementById('txtmontoefectuado').value=Math.round(document.getElementById('txtmessolicitado').value*document.getElementById('txtmontosolicitado').value*100)/100;" size="3" maxlength="1" style="text-align:center" onKeyPress="return validarnum(event)" value="<?=$row->MESREINTEGRO;?>" <?=($row->STATUS_SEGUIMIENTO!="CON" and $row->STATUS_SEGUIMIENTO!="ANU")?"":"readonly" ?> /></label></td>
			<td><?=_("MONTO SOLICITADO");?></td>
			<td><input name="txtmontosolicitado" type="text" class='classtexto' id="txtmontosolicitado" onKeyPress="return numeroDecimal(event)" onFocus='coloronFocus(this);' onBlur="colorOffFocus(this);document.getElementById('txtmontoefectuado').value=Math.round(document.getElementById('txtmessolicitado').value*document.getElementById('txtmontosolicitado').value*100)/100" size="7" maxlength="6" value="<?=$row->MONTOSOLICITADO;?>" <?=($row->STATUS_SEGUIMIENTO!="CON" and $row->STATUS_SEGUIMIENTO!="ANU")?"":"readonly" ?> /></td>
		</tr>
		<tr bgcolor="#ffb3b3">
			<td><?=_("FECHA EJE. REITEGRO");?></td>
			<td><input name="txtfechaejecuta" id="f_date_b" type="text" size="14" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=$row->FECHAEJECUSION; ?>" readonly="readonly"><button type="reset" id="f_trigger_b">...</button></td>
			<td><?=_("MONTO EFECTUADO") ;?></td>
			<td><input name="txtmontoefectuado" type="text" class='classtexto' id="txtmontoefectuado" onKeyPress="return numeroDecimal(event)" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' size="8" maxlength="6" value="<?=number_format($row->MESREINTEGRO*$row->MONTOSOLICITADO,2);?>" readonly="readonly" /></td>
		</tr>
 <? }?>
		<tr>
			<td><?=_("CODIGO ID") ;?></td>
			<td bgcolor="#FFFFEA"><?=$row->CVEAFILIADO; ?></td>
			<td><?=_("COMENTARIO") ;?></td>
			<td bgcolor="#FFDFDF"><strong><?=$row->COMENTARIO; ?></strong></td>
		</tr> 
	</table>
<?
	if($row->STATUS_SEGUIMIENTO!="CON"){ 
	
		if($row->MOTIVOLLAMADA=="REINTEGRO"){ 		
?>
	<script type="text/javascript">
			Calendar.setup({
			inputField     :    "f_date_b",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
	</script> 
<?	} } ?>
<?
	if($row->STATUS_SEGUIMIENTO!="CON" and $row->STATUS_SEGUIMIENTO!="ANU" ){
	
		if($row->MOTIVOLLAMADA=="QUEJASRECLAMO"){	
?>
 
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#D2FFD2" style="border:1px solid #80ff80">
		<tr>
			<td width="15%"><?=_("VALIDEZ") ;?></td>
			<td width="35%"><?							
						$con->cmb_array("cmbvalidez",$validez_sac,$row->ARRVALIDEZ," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
					?></td>
			<td width="13%"><?=_("ASIG. CASO") ;?></td>
			<td width="37%">
				<?							
					$sql="select IDGRUPO,NOMBRE from catalogo_grupo order by NOMBRE";
					$con->cmbselectdata("select IDGRUPO,NOMBRE from catalogo_grupo order by NOMBRE","cmbasignacionc",$row->IDGRUPO,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
				?>
			</td>
		</tr>
		<tr>
			<td><?=_("FECHA DISPOSICION") ;?></td>
			<td><input name="txtfecha" id="f_date_b" type="text" size="14" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=$row->FECHADISPOSICION; ?>" ><button type="reset" id="f_trigger_b">...</button></td>
			<td colspan="2">
				<input name="chkdefensacon" type="checkbox" id="chkdefensacon" value="1" <?=($row->DEFENSACONSUMIDOR)?"CHECKED disabled":""; ?> />
				<?=_("DEFENSA AL CONSUMIDOR") ;?>
				<input name="chkfromal" type="checkbox" id="chkfromal" value="1" <?=($row->RESPUESTAFORMAL)?"CHECKED disabled":""; ?> /><?=_("RESP. FORMAL") ;?>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
			Calendar.setup({
			inputField     :    "f_date_b",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
	</script>
<? } ?>
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#DFDFFF" style="border:1px solid #8080c0">
		<tr>
			<td width="112"><?=_("ACCION INMEDIATA/DISPOSICION") ;?></td>
			<td width="450" rowspan="4"><textarea name="txtsegimiento" id="txtsegimiento" cols="100" rows="4"  style="text-transform:uppercase;"></textarea></td>
			<td width="503">
				<?							
					$con->cmb_array("cmbstatus",$statusproceso_sac,"PRO","id='cmbstatus' class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","","REC");
				?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>    
		<tr>
			<td colspan="3">
				<div align="center">
					<input type="button" name="btncerrar" id="btncerrar" value="<?=_("SALIR") ;?>" onClick="actualizaPadre()" />
					<input type="submit" name="btngrabar" id="btngrabar" value="<?=_("GRABAR") ;?>" <?=($accesocaso >0)?"":"disabled" ;?>/>		  
				</div>
			</td>
		</tr>
	</table> 	
  <?	} else{ ?>
	<table width="672" border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td colspan="3">
				<div align="left"><input type="button" name="btncerrar" id="btncerrar" value="<?=_(">>> CERRAR") ;?>" onclick="actualizaPadre()"/></div>
			</td>
		</tr>
	</table>
    <?	} ?>
</form>
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #af7b03" bgcolor="#fddc8e">       
		<tr>
			<td width="50" bgcolor="#333333"><div align="center"><span class="style1"><?=_("ID");?></span></div></td>
			<td width="129" bgcolor="#333333"><div align="center"><span class="style1"><?=_("FECHAHORA") ;?></span></div></td>
			<td width="129" bgcolor="#333333"><div align="center"><span class="style1"><?=_("VALIDEZ") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("ASIG.CASO") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("FECHADISP.") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("DEFEN.CONSUMIDOR") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("RESP.FORMAL") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("USUARIO") ;?></span></div></td>			
			<td width="20%" bgcolor="#333333" ><div align="center"><span class="style1"><?=_("COMENTARIO") ;?></span></div></td>
		</tr>
		<?
			$i=0;
			while($regs=$rs_seg->fetch_object()){
				if($i%2==0) $fondo='#CCCCCC'; else $fondo='#ffeaff';
				$n++;
		?>
		<tr>
			<td bgcolor="<?=$fondo;?>" style="text-align:center"><?=$n;?></td>
			<td bgcolor="<?=$fondo;?>" style="text-align:center"><?=$regs->FECHARETENCION; ?></td>
			<td bgcolor="<?=$fondo;?>" style="text-align:center"><?=($regs->ARRVALIDEZ)?$validez_sac[$regs->ARRVALIDEZ]:"S/D"; ?></td>
			<td bgcolor="<?=$fondo;?>" style="text-align:center"><?=$regs->NOMBRE; ?></td>
			<td bgcolor="<?=$fondo;?>"	style="text-align:center"><?=$regs->FECHADISPOSICION; ?></td>
			<td bgcolor="<?=$fondo;?>"	style="text-align:center"><?=($regs->DEFENSACONSUMIDOR==1)?"SI":"NO"; ?></td>
			<td bgcolor="<?=$fondo;?>"	style="text-align:center"><?=($regs->RESPUESTAFORMAL==1)?"SI":"NO"; ?></td>
			<td bgcolor="<?=$fondo;?>"	style="text-align:center"><?=$regs->IDUSUARIO; ?></td>			
			<td bgcolor="<?=$fondo;?>" width="20%"><pre style="font-size:11px; font-family:'Courier New'"><?=$regs->COMENTARIO; ?></pre></td>
		</tr>                  
        <?
				$i=$i+1;
			}
		?>
	</table>
</body>
</html>