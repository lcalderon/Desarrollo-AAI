<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
$con= new DB_mysqli();
$idusuario= $_SESSION[user];
$fechainicio = $asis->asistencia_servicio->FECHAHORAENTREGA;

$fechai = substr($fechainicio,0,10);
$horai = substr($fechainicio,11,2);
$minutoi = substr($fechainicio,14,2);

?>
<style type="text/css">
<!--
.Estilo1 {color: #B4BEE9}
-->
</style>

<legend><?=_('ASISTENCIA ADMINISTRATIVA MENSAJERIA CADETERIA')?></legend>

<form id='form_administrativamensajeria'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
<table>
	<tbody>
		<tr>
			<td  valign="top" ><?=_('SUBSERVICIO')?><br>
		<?	if($asis->asistencia_servicio->IDASISTENCIA!=''){ 
$con->cmbselect_ar('SUBSERVICIO',$varios,(isset($asis))?$asis->asistencia_servicio->SUBSERVICIO:$subservicio,'id=subservicio' ,'onchange=mostrar_subservicio(this,"documento","compra","regalo")','SELECCIONE');

}else{
$con->cmbselect_ar('SUBSERVICIO',$varios,(isset($asis))?$asis->asistencia_servicio->SUBSERVICIO:$subservicio,'id=subservicio' ,'onchange=mostrar_subservicio(this,"documento","compra","regalo")','SELECCIONE');
}
?></td></tr>
		<tr><td>

<!--ENVIO DE DOCUMENTOS-->
	    <div id=documento <? if($asis->asistencia_servicio->SUBSERVICIO=='EDOC'){ echo "style='display:block'"; }else{  echo "style='display:none'"; } ?>>
	      <table>
		<tr><?
				if (isset($asis)){
					$ubigeo = new  ubigeo();
					$ubigeo->leer('ID',$asis->temporal,'asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis->asistencia_servicio->IDRETIROESTABLECIMIENTO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='D_IDRETIRO' id='d_idretiro' value="<?=$ubigeo->ID ?>">
			<td colspan="2"><?=_('DIRECCION DE RETIRO')?><font color="red">*</font><br>
			
			<input type='text' name='D_LUGARRETIRO' id='d_lugarretiro' value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('d_idretiro'),'d_idretiro','d_lugarretiro','asistencia_varios_administrativamensajeriacadeteria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td>
			
			<td><?=_('PERSONA QUE ENTREGA')?><font color="red">*</font><br><input type='text' name="D_PERSONAENTREGA" id="d_personaentrega" size='30' value="<?=$asis->asistencia_servicio->PERSONAENTREGAREMITE?>"></td>
			<td rowspan='2'><?=_('OTROS')?><br>
			<textarea name='D_OTROS' id='d_otros' cols="30"><?=$asis->asistencia_servicio->OTROS?></textarea></td>
			</tr>
			<tr>
			 <?
				if (isset($asis)){
					$ubigeo2 = new  ubigeo();
					$ubigeo2->leer('ID',$asis->temporal,'asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis->asistencia_servicio->IDDESTINO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='D_IDDESTINO' id='d_iddestino' value="<?=$ubigeo2->ID ?>">

			<td colspan="2"><?=_('DIRECCION DE DESTINO')?><font color="red">**</font><br>
			
			<input type='text' name='D_LUGARDESTINO' id='d_lugardestino' value="<?=$ubigeo2->direccion.' '.$ubigeo2->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('d_iddestino'),'d_iddestino','d_lugardestino','asistencia_varios_administrativamensajeriacadeteria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td>
			<td><?=_('PERSONA QUE RECIBE')?><font color="red">**</font><br><input type='text' name="D_PERSONARECIBE" id="d_personarecibe" size='30' value="<?=$asis->asistencia_servicio->PERSONARECIBE?>"></td></tr>
			
			
		</table>
	    </div>


<!--COMPRAS VARIAS-->
	    <div id=compra <? if($asis->asistencia_servicio->SUBSERVICIO=='COVA'){ echo "style='display:block'"; }else{  echo "style='display:none'"; } ?>>
		   <table>
		<tr><?
				if (isset($asis)){
					$ubigeo3 = new  ubigeo();
					$ubigeo3->leer('ID',$asis->temporal,'asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis->asistencia_servicio->IDRETIROESTABLECIMIENTO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='C_IDRETIRO' id='c_idretiro' value="<?=$ubigeo3->ID ?>">
			<td><?=_('DIRECCION ESTABLECIMIENTO')?><font color="red">*</font><font color="red">*</font><br>
			
			<input type='text' name='C_LUGARRETIRO' id='c_lugarretiro' value="<?=$ubigeo3->direccion.' '.$ubigeo3->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('c_idretiro'),'c_idretiro','c_lugarretiro','asistencia_varios_administrativamensajeriacadeteria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td>
			<?
				if (isset($asis)){
					$ubigeo4 = new  ubigeo();
					$ubigeo4->leer('ID',$asis->temporal,'asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis->asistencia_servicio->IDDESTINO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='C_IDDESTINO' id='c_iddestino' value="<?=$ubigeo4->ID ?>">

			<td ><?=_('DIRECCION DE DESTINO')?><font color="red">**</font><br>
			
			<input type='text' name='C_LUGARDESTINO' id='c_lugardestino' value="<?=$ubigeo4->direccion.' '.$ubigeo4->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('c_iddestino'),'c_iddestino','c_lugardestino','asistencia_varios_administrativamensajeriacadeteria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td>
			<td rowspan="2" valign="top"><?=_('PERSONA QUE RECIBE')?><font color="red">**</font><br><input type='text' name="C_PERSONARECIBE" id="c_personarecibe" size='30' value="<?=$asis->asistencia_servicio->PERSONARECIBE?>"></td>
			</tr>
			<tr>
			<td><?=_('DESCRIPCION DE LA COMPRA')?><br><textarea name='C_DESCRIPCION' id='c_descripcion' cols="30"><?=$asis->asistencia_servicio->DESCRIPCIONCOMPRA?></textarea></td>
			<td rowspan='2' valign='top'><?=_('OTROS')?><br>
			<textarea name='C_OTROS' id='c_otros' cols="30"><?=$asis->asistencia_servicio->OTROS?></textarea></td>
			</tr>
			
			 
			
			
			
		</table>
	</div>

<!--MENSAJES Y REGALOS-->
	   	    <div id=regalo <? if($asis->asistencia_servicio->SUBSERVICIO=='MERE'){ echo "style='display:block'"; }else{  echo "style='display:none'"; } ?>>
	      <table>
		<tr>
		<td><?=_('NOMBRE DEL REMITENTE')?><br><input type='text' name="M_REMITENTE" id="m_remitente" size='30' value="<?=$asis->asistencia_servicio->PERSONAENTREGAREMITE?>"></td>
		<td><?=_('NOMBRE DEL DESTINATARIO')?><br><input type='text' name="M_DESTINATARIO" id="m_destinatario" size='30' value="<?=$asis->asistencia_servicio->PERSONADESTINATARIO?>"></td>
		
		<td rowspan='2'><?=_('OTROS')?><br>
			<textarea name='M_OTROS' id='m_otros' cols="30"><?=$asis->asistencia_servicio->OTROS?></textarea></td>
			</tr>
			<tr>
		<?
				if (isset($asis)){
					$ubigeo5 = new  ubigeo();
					$ubigeo5->leer('ID',$asis->temporal,'asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis->asistencia_servicio->IDDESTINO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='M_IDDESTINO' id='m_iddestino' value="<?=$ubigeo5->ID ?>">
			<td colspan="2"><?=_('DIRECCION DE DESTINATARIO')?><br>
			
			<input type='text' name='M_LUGARDESTINO' id='m_lugardestino' value="<?=$ubigeo5->direccion.' '.$ubigeo5->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('m_iddestino'),'m_iddestino','m_lugardestino','asistencia_varios_administrativamensajeriacadeteria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td>
			
		
			
			</tr>
			<tr>
			<td><?=_('FECHA Y HORA DE ENTREGA')?><br><input type="text" name='FECHACOMPRA' id='date2' value="<?=$fechai?>" readonly>
 				<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_admin();" class='normal'>
			 <select name='cbhoracompra' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horai==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
<select name='cbminutocompra' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoi==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select></td>
			<td ><?=_('TIPO DE ENTREGA')?><br>
			
			<?	$con->cmbselect_ar('M_CONCEPTOENTREGA',$varios_concepto_entrega,(isset($asis))?$asis->asistencia_servicio->CONCEPTOENTREGA:$varios_concepto_entrega,'id=m_conceptoentrega','','')?></td>
		
			<td><?=_('PERSONA QUE RECIBE')?><br><input type='text' name="M_PERSONARECIBE" id="m_personarecibe" size='30' value="<?=$asis->asistencia_servicio->PERSONARECIBE?>"></td></tr>
				
			
		</table>
	    </div>


		</td></tr>
			
			
			
			
			
	
		
	</tbody>
	
</table>
</form>