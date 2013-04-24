<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
$con= new DB_mysqli();
$idusuario= $_SESSION[user];
$fechadeceso = $asis->asistencia_servicio->FECHADECESO;
$fechaceremonia = $asis->asistencia_servicio->FECHAHORACEREMONIA;

$fechad = substr($fechadeceso,0,10);
$horad = substr($fechadeceso,11,2);
$minutod = substr($fechadeceso,14,2);

$fechac = substr($fechaceremonia,0,10);
$horac = substr($fechaceremonia,11,2);
$minutoc = substr($fechaceremonia,14,2);

?>
<style type="text/css">
<!--
.Estilo1 {color: #B4BEE9}
-->
</style>

<legend><?=_('FUNERARIA')?></legend>

<form id='form_funeraria'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
<table>
	<tbody>
		<tr><td>


	   	    <div id=funeraria >
	      <table>
		<tr>
		    <td><?=_('FECHA DE DECESO')?><br><input type="text" name='FECHADECESO' id='date2' value="<?=$fechad?>" readonly>
 				<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			 <select name='cbhoradeceso' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horad==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
<select name='cbminutodeceso' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutod==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select></td>
		<?
				if (isset($asis)){
					$ubigeo5 = new  ubigeo();
					$ubigeo5->leer('ID',$asis->temporal,'asistencia_varios_funeraria_ubigeo',$asis->asistencia_servicio->IDDESTINO);
					}
					
			//	if ( $disabled=='') $act_vermapa= "onclick=mod_ubigeo($F('iddestino'),'iddestino','lugardestino','asistencia_pc_visitatecnica_destino')";	
			?>
		
			<input type='hidden' name='M_IDDESTINO' id='m_iddestino' value="<?=$ubigeo5->ID ?>">
			<td colspan="2"><?=_('LUGAR DE TRASLADO')?><br>
			
			<input type='text' name='M_LUGARDESTINO' id='m_lugardestino' value="<?=$ubigeo5->direccion.' '.$ubigeo5->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('m_iddestino'),'m_iddestino','m_lugardestino','asistencia_varios_funeraria_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?}?>
			</td></tr>
		<tr><td><?=_('MOTIVO')?><br>
			<textarea name='MOTIVO' id='motivo' cols="30"><?=$asis->asistencia_servicio->MOTIVO?></textarea></td>
		     <td><?=_('FECHA Y HORA DE CEREMONIA')?><br><input type="text" name='FECHACEREMONIA' id='date3' value="<?=$fechac?>" readonly>
 				<input type="button" id="calendarButton3" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			 <select name='cbhoraceremonia' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horac==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
			  <select name='cbminutoceremonia' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoc==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select></td>

		     <td ><?=_('OTROS')?><br>
			<textarea name='OTROS' id='otros' cols="30"><?=$asis->asistencia_servicio->OTROS?></textarea></td>
		
		
					</tr>
			<tr>
		
			
		
			
			</tr>
	
		</table>
	    </div>

		</td></tr>
			

	</tbody>
	
</table>
</form>