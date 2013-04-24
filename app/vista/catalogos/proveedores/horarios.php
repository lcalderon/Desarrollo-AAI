<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');

$con = new DB_mysqli();
$idusuariomod = $_SESSION[user];

$idproveedor = $_GET[idproveedor];
$prov = new proveedor();
$prov->leer_horario($idproveedor);
$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;

$semana = array('DOMINGO'=>_('DOM'),'LUNES'=>_('LUN'),'MARTES'=>_('MAR'),'MIERCOLES'=>_('MIE'),'JUEVES'=>_('JUE'),'VIERNES'=>_('VIE'),'SABADO'=>_('SAB'));

foreach ($semana as $i=>$v)	$dias_seleccionados[] = $prov->horario[$i];
include_once("../../includes/head_prot.php");
?>


<body onload="carga();">
<form id='form_horario'>
<input type="hidden" name="IDPROVEEDOR" value="<?=$idproveedor?>">
<input type="hidden" name="IDUSUARIOMOD" value="<?=$idusuariomod?>">
<fieldset>
<legend><?=_('HORAS')?></legend>
<table width="100%">
	<tr>
		<td align="left"><input type="radio" name='turno' id='rad_1' onclick="marcar_horas()"><?=_('24 HORAS')?></td>
		<td ><input type="radio" name='turno' id='rad_2' onclick="presentar_horas()"><?=_('TURNO')?></td>
	</tr>
</table>

<table width="100%" align="center" id='zona_hora'>
	<tr>
		<td align="left">
	  		<?=_('DE').' : '?>
  	  			<select name='HORAINICIO' id='horainicio' >
  	  			<? for($i=0;$i<=23;$i++) : ?>
  	  			   	<option value="<?=$i?>" <?=($prov->horario[HORAINICIO]==$i)?'selected':'';?> ><?=$i?></option>
  	  			<?endfor;?>
  	  			</select>
  		</td>
  		<td>
  	  		<?=_('HASTA').' :'?>
  				<select name='HORAFINAL' id='horafinal'>
  				<? for($i=0;$i<=23;$i++) : ?>
  				 <? $selected = ($prov->horario->HORAFINAL == $i)?'selected':''?>
  					<option value="<?=$i?>" <?=($prov->horario[HORAFINAL]==$i)?'selected':'';?> ><?=$i?></option>
  				<?endfor;?>
  				</select>
    	</td>
	</tr>
</table>
</fieldset>
<fieldset>
<legend><?=_('DIAS')?></legend>
<table width="100%">
	<tr>
		<td><input type="radio" name="DIAS" id='rad_3' onclick="marcar_dias()"><?=_('TODOS LOS DIAS')?></td>
		<td><input type="radio" name="DIAS" id='rad_4' onclick="desmarcar_dias()"><?=_('ALGUNOS DIAS')?></td>
	</tr>
</table>

<table width="100%" align="center" >
	<tr>
		<? foreach ($semana as $i=>$v):?>
			<td align='center'><?=$v?>&nbsp;</td>
		<?endforeach;?>
	</tr>
	<tr id='dias'>
		<? foreach ($semana as $i=>$v):?>
			<td align="center"><input type='checkbox' name='<?=$i?>' id='<?=$i?>'  <?=($prov->horario[$i])?'checked':'';?> ></td>
		<?endforeach;?>
	</tr>
</table>
</fieldset>

<br>
<table align="center">
	<tr>
		<td align="center">
		<input type='button' value='<?=_('GRABAR')?>' class="guardar" onclick="guardar()"  <?=($edicion==1)?'':'disabled'?>>
		<input type='button' value='<?=_('SALIR')?>' class="cancelar" onclick="parent.win.close()">
		
		</td>
	</tr>
</table>
</form>
</body>
</html>

<script type="text/javascript">
function marcar_horas(){
	if ($('rad_1').checked)
	{
		$('horainicio').value=0;
		$('horafinal').value=23;
		$('zona_hora').hide();
	}
	return;
}
function presentar_horas(){
	if ($('rad_2').checked){
		$('zona_hora').show();
	}
	return;
}
function marcar_dias(){
	if ($('rad_3').checked ){
		$('DOMINGO').checked=true;
		$('LUNES').checked=true;
		$('MARTES').checked=true;
		$('MIERCOLES').checked=true;
		$('JUEVES').checked=true;
		$('VIERNES').checked=true;
		$('SABADO').checked=true;
	}
	return;
}
function desmarcar_dias(){
	if ($('rad_4').checked ){
		$('DOMINGO').checked=false;
		$('LUNES').checked=false;
		$('MARTES').checked=false;
		$('MIERCOLES').checked=false;
		$('JUEVES').checked=false;
		$('VIERNES').checked=false;
		$('SABADO').checked=false;
	}
	return;
}

function guardar(){
	new Ajax.Request('../../../controlador/ajax/ajax_proveedor_horario.php',
	{
		method: 'post',
		parameters:  $('form_horario').serialize(true),
		onSuccess: function(){
			parent.win.close();
		}
	});
	return;
}


function carga(){
	
	if (('<?=$prov->horario[HORAINICIO]?>'==0)&&('<?=$prov->horario[HORAFINAL]?>'==23)) {
		$('rad_1').checked=true;
		$('zona_hora').hide();
	}
	else		$('rad_2').checked=true;

	if (('<?=in_array(0,$dias_seleccionados); ?>')) {
		$('rad_4').checked=true;
	}else	$('rad_3').checked=true;
	
	return;
}

</script>