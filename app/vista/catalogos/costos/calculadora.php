<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');
include_once('../../../modelo/clase_asigprov.inc.php');
include_once('../../includes/arreglos.php');
include_once("../../../vista/login/Auth.class.php");


$con = new DB_mysqli();

$con->select_db($con->catalogo);

if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

$idusuario=$_SESSION[user];

$asig = new asigprov();
$asig->carga_datos($_GET[idasigprov]);
$asig->proveedor->leer_observacion_servicio($asig->asistencia->servicio->idservicio);

$sql="
SELECT 
	csc.IDCOSTO,
	cc.DESCRIPCION, 
	cc.COSTONEGOCIADO,
	cc.ARRCARGOACUENTA,
	if('$asig->localforaneo'='L',cpscn.MONTOLOCAL,if('$asig->localforaneo'='I',cpscn.MONTOINTERMEDIO,cpscn.MONTOFORANEO)) MONTOBASE,
	if('$asig->aplicanocturno'='1',cpscn.PLUSNOCTURNO,'') PLUSNOCTURNO,
	if('$asig->aplicafestivo'='1',cpscn.PLUSFESTIVO,'') PLUSFESTIVO,		
	cpscn.UNIDAD UNIDAD_NEGOCIADO,
	cpscn.IDMEDIDA IDMEDIDA_NEGOCIADO,
	cpscn.MONTOLOCAL COSTOLOCAL,
	cpscn.MONTOINTERMEDIO COSTOINTERMEDIO,
	cpscn.MONTOFORANEO COSTOFORANEO,
	cpscn.PLUSNOCTURNO ADICIONALNOCTURNO,
	cpscn.PLUSFESTIVO ADICIONALFESTIVO
	
FROM 
	(catalogo_servicio_costo csc, 
	catalogo_costo cc)
LEFT JOIN catalogo_proveedor_servicio_costo_negociado cpscn ON (cpscn.IDSERVICIO = csc.IDSERVICIO AND cpscn.IDCOSTO=csc.IDCOSTO AND cpscn.IDPROVEEDOR ='".$asig->proveedor->idproveedor."' )

WHERE 
	 csc.IDCOSTO = cc.IDCOSTO 
	AND cc.ACTIVO=1 
	AND csc.IDSERVICIO = '".$asig->asistencia->servicio->idservicio."'  
	ORDER BY cc.COSTONEGOCIADO DESC
";

$result = $con->query($sql);
$num_reg = $result->num_rows;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

 <head>
	<title>American Assist</title>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"></script>

	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
	<link href="../../../../librerias/windows_js_1.3/themes/alert.css" rel="stylesheet" type="text/css" ></link>
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>

	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/tooltips.js"></script>

	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
<style>
	
input[type=button]{      
	border-width:2px;
	border-style:solid;
	-webkit-border-radius:3px; 
	-moz-border-radius:3px; 
	border-radius:3px; 
	height:22px;

	
	}	
input[type=button].cancelar {
	border-color:red;
	}	
input[type=button].guardar {
	border-color:green;
	}
input[type=button].normal {
	border-color:#003C74;
	}
input[type=submit]{
	border-width:2px;
	border-style:solid;
	border-color:green;
	}


</style>	
	
</head>
<body onload="calcular();">
<form name='form_calculadora' id='form_calculadora'>
<input type='hidden' name='IDASIGPROV' value='<?=$asig->idasigprov?>'>
<input type='hidden' name='IDMONEDA' value="<?=($asig->moneda->idmoneda=='')?$asig->proveedor->moneda->idmoneda:$asig->moneda->idmoneda?>" >
<input type='hidden' name='IDASISTENCIA' value="<?=$asig->asistencia->idasistencia ?>">
<input type="hidden" name='COSTOFIJO' id='costofijo' >

<?

switch ($asig->localforaneo)
{
	case 'L': $etiqueta=_('LOCAL'); break;
	case 'I': $etiqueta=_('INTERMEDIO'); break;
	case 'F': $etiqueta=_('FORANEO'); break;
}

$etiqueta .= ($asig->aplicanocturno)?'->'._('NOCTURNO'):'';
$etiqueta .= ($asig->aplicafestivo)?'->'._('FESTIVO'):'';

?>

<table class="calculadora" >
<tr>
	<th colspan="6" align="left"><h1><?=_('PROVEEDOR')?> : <?=$asig->proveedor->nombrefiscal?></h1></th>
</tr>
<tr>
	<th colspan="4" align="left"><u><h1><?=_('ASISTENCIA')?> : <?=$etiqueta; ?></h1></u></th>
</tr>
<tr>
<th colspan='4'></th>
<th colspan='3' align='center'><?=_('COSTOS CABINA')?></th>
</tr>
<tr>
<th width="5%"></th>
<th width="25%"><?=_('CONCEPTO')?></th>
<th width="30%"><?=_('UNIDADES')?></th>
<th></th>
<th width="10%" title="<?=_('AMERICAN ASSIST')?>"><?=_('AA')?></th>
<th width="10%" title="<?=_('NUESTRO AFILIADO')?>"><?=_('NA')?></th>
<th width="10%" title="<?=_('CLIENTE CORPORATIVO')?>"><?=_('CC')?></th>
<th width="5%"></th>
</tr>
<?
$i=1;
while ($reg = $result->fetch_object())
{
	if (isset($asig->costos[$reg->IDCOSTO]))
	{
		$checked ='checked';
		$condicion="class='classtexto'";
	}
	else
	{
		$checked ='';
		$condicion="class='classtextoreadonly' readonly";
	}

	echo '<tr >';
	echo "<input type='hidden' name='IDCOSTO[$i]' id='idcosto[$i]' value='$reg->IDCOSTO' >";
	echo "<input type='hidden' name='IMPORTEESTIMADO[$i]' id='importeestimado[$i]' value='".$asig->costos[$reg->IDCOSTO][IMPORTEESTIMADO] ."' >";
	echo "<input type='hidden' name='IDUSUARIOMOD' id='idusuariomod' value='$idusuario' >";
	echo "<input type='hidden' name='COSTONEGOCIADO[$i]' id='costonegociado[$i]' value='$reg->COSTONEGOCIADO'>";
	echo "<td><input type ='checkbox'  name='CHECK[$i]' id='check[$i]' onChange=act_des($i) $checked >";
	?>
	<div id='observ_<?=$i?>' style='display:none; margin: 5px; background:#90CC8F;' >
		<table>
					<tr>
						<th><?=_('CONCEPTO')?></th>
						<th><?=_('VALOR')?></th>
					</tr>
					<tr>
						<td><?=_('LOCAL')?></td>
						<td><?=$asig->proveedor->moneda->simbolo.' '.$reg->COSTOLOCAL?></td>
					</tr>
					<tr>
						<td><?=_('INTERMEDIO')?></td>
						<td><?=$asig->proveedor->moneda->simbolo.' '.$reg->COSTOINTERMEDIO?></td>
					</tr>
					<tr>
						<td><?=_('FORANEO')?></td>
						<td><?=$asig->proveedor->moneda->simbolo.' '.$reg->COSTOFORANEO?></td>
					</tr>
					<tr>
						<td><?=_('PLUSFESTIVO')?></td>
						<td><?=$asig->proveedor->moneda->simbolo.' '.$reg->ADICIONALFESTIVO?></td>
					</tr>
					<tr>
						<td><?=_('PLUSNOCTURNO')?></td>
						<td><?=$asig->proveedor->moneda->simbolo.' '.$reg->ADICIONALNOCTURNO?></td>
					</tr>
					<tr>
						<td colspan="2">
						<textarea><?=$asig->proveedor->obs_servicio?></textarea>	
						</td>
					</tr>
		</table>
	</div>
	
	<?
	echo "</td>";
	echo "<td align='left' id='nom_costo[$i]' style='cursor: pointer;' onmouseover=mostrar_comentario('nom_costo[$i]','observ_$i')>$reg->DESCRIPCION</td>";
	if ($reg->COSTONEGOCIADO)	echo "<td><input type ='text' name='UNIDAD[$i]' id='unidad[$i]' size='3' dir='rtl' value='".(isset($asig->costos[$reg->IDCOSTO][UNIDAD])?$asig->costos[$reg->IDCOSTO][UNIDAD]:1)."' size='4' onchange='importe($i);calcular();' class='classtexto'  onBlur='calcular();' onKeyPress='return validarnumero(event)' >$reg->IDMEDIDA_NEGOCIADO</td>";
	else
	echo "<td><input type ='hidden' name='UNIDAD[$i]' id='unidad[$i]' size='3' dir='rtl' value='1' size='4' onchange='importe($i);calcular();' class='classtexto'  onBlur='calcular();' onKeyPress='return validarnumero(event)' ></td>";


	echo "<input type ='hidden' name='MONTOBASE[$i]' id='montobase[$i]' value ='$reg->MONTOBASE' size='10' dir='rtl' class='classtexto'  disabled>";
	echo "<input type ='hidden' name='PLUSNOCTURNO[$i]' id='plusnocturno[$i]' value ='$reg->PLUSNOCTURNO' size='10' dir='rtl' class='classtexto' disabled>";
	echo "<input type ='hidden' name='PLUSFESTIVO[$i]' id='plusfestivo[$i]' value ='$reg->PLUSFESTIVO' size='10' dir='rtl' class='classtexto' disabled>";

	echo "<input type ='hidden' name='AA_MONTOESTIMADO[$i]' id='aa_montoestimado[$i]' value='{$asig->costos[$reg->IDCOSTO][AA_MONTOESTIMADO]}'>";
	echo "<input type ='hidden' name='NA_MONTOESTIMADO[$i]' id='na_montoestimado[$i]' value='{$asig->costos[$reg->IDCOSTO][NA_MONTOESTIMADO]}'>";
	echo "<input type ='hidden' name='CC_MONTOESTIMADO[$i]' id='cc_montoestimado[$i]' value='{$asig->costos[$reg->IDCOSTO][CC_MONTOESTIMADO]}'>";
	echo "<td>{$asig->proveedor->moneda->simbolo}</td>";

	echo "<td><input type='text' name='AA_MONTOREAL[$i]' id='aa_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][AA_MONTOREAL]}' size='10' onKeyPress='return numeroDecimal(event);'  onblur='calcular()'". (($asig->asistencia->arrcondicionservicio=='CON')?" class='classtextoreadonly' readonly ": $condicion) ."></td>";
	echo "<td><input type='text' name='NA_MONTOREAL[$i]' id='na_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][NA_MONTOREAL]}' size='10' onKeyPress='return numeroDecimal(event);'  onblur='calcular()' $condicion ></td>";
	echo "<td><input type='text' name='CC_MONTOREAL[$i]' id='cc_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][CC_MONTOREAL]}'size='10'  onKeyPress='return numeroDecimal(event);'  onblur='calcular()' $condicion ></td>";
	echo "<td></td>";
	$i++;
}
echo '</tr>';
?>
<tr>
</tr>
	<tr>
		<td></td>
		<td align='left' valign='top' ><b><?=_('OBSERVACIONES')?></b></td>
	</tr>	
	<tr>
		<td></td>
		<td>
		<textarea rows="4" name='OBSERVACION' id='observacion' class='classtexto' style="text-transform: uppercase;" cols="40" ><?=trim($asig->observacion)?></textarea>
		<br>		
		
		<input type="checkbox" name="STATUSNEGOCIACIONPOSTERIOR" id="statusnegociacionposterior"  <?=($asig->statusnegociacionposterior==1)?'checked':''?> ><?=_('NEGOCIACION POSTERIOR')?>
		</td>
		<td rowspan="3" colspan="10">
		<fieldset>
			<legend><?=_('TOTALES')?></legend>
			<table class="calculadora" align="left">
  				<tr>
					<td width='50%'><?=_('AMERICAN ASSIST')?></td>
					<td width='50%'><?=$asig->proveedor->moneda->simbolo?>
					<input type="hidden" name='AA_TOTALESTIMADO' id='aa_totalestimado' >
 					<input type='text' name='AA_TOTALREAL' id='aa_totalreal'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
				<tr>
					<td ><?=_('NUESTRO AFILIADO')?></td>
					<td ><?=$asig->proveedor->moneda->simbolo?>
					<input type="hidden" name='NA_TOTALESTIMADO' id='na_totalestimado' >
					<input type='text' name='NA_TOTALREAL' id='na_totalreal'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
				<tr>
					<td ><?=_('CLIENTE CORPORATIVO')?></td>
					<td ><?=$asig->proveedor->moneda->simbolo?>
					<input type="hidden" name='CC_TOTALESTIMADO' id='cc_totalestimado' >
					<input type='text' name='CC_TOTALREAL' id='cc_totalreal'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
  				
				<tr>
					<td><b><?=_('TOTAL ASISTENCIA').' '. $asig->proveedor->moneda->simbolo; ?></b></td>
					<td>
					<input type='hidden' name="IMPORTEESTIMADO" id='importeestimado' >
					<input type='text' name="IMPORTEREAL" id='importereal' value='0'  size='10' dir='rtl'  class='classtexto' readOnly 
					style='font-size: 20px;font-family: Arial, serif;font-weight: normal;'>		
					</td>
				</tr>
				
 			</table>
		
		</fieldset>	
</td>
</tr>
<tr></tr>
<tr></tr>
<tr>
	<td colspan="8" align="center">
	<input type="button" value="<?=_('CANCELAR')?>"  class="cancelar" onclick="cerrar();">
	&nbsp;
	<input type="button" value="<?=_('GRABAR')?>" class="guardar" onclick="return confirmar(); grabar();">
	</td>
</tr>

</table>
</form>
</body>
</html>

<script type="text/javascript">
var fila = new Array();
var filas=null;
function act_des(i){
	if ($('check['+i+']').checked )
	{
		importe(i);
		abrir_importe(i);
	}
	else
	cerrar_importe(i);
	calcular();
	return;
}

// ACTIVA LA EDICION DEL CAMPO IMPORTE Y JUSTIFICACION
function abrir_importe(i){

	if ('<?=$asig->asistencia->arrcondicionservicio?>'=='CON')
	{
		$('na_montoreal['+i+']').className='classtexto';
		$('cc_montoreal['+i+']').className='classtexto';
		$('na_montoreal['+i+']').readOnly = false;
		$('cc_montoreal['+i+']').readOnly = false;
	}
	else
	{
		$('aa_montoreal['+i+']').className='classtexto';
		$('na_montoreal['+i+']').className='classtexto';
		$('cc_montoreal['+i+']').className='classtexto';
		$('aa_montoreal['+i+']').readOnly = false;
		$('na_montoreal['+i+']').readOnly = false;
		$('cc_montoreal['+i+']').readOnly = false;
	}
	calcular();
	return;
}


function cerrar_importe(i){
	$('check['+i+']').checked = false;
	$('aa_montoreal['+i+']').clear();
	$('aa_montoreal['+i+']').readOnly = true;
	$('aa_montoreal['+i+']').className='classtextoreadonly';
	$('na_montoreal['+i+']').clear();
	$('na_montoreal['+i+']').readOnly = true;
	$('na_montoreal['+i+']').className='classtextoreadonly';
	$('cc_montoreal['+i+']').clear();
	$('cc_montoreal['+i+']').readOnly = true;
	$('cc_montoreal['+i+']').className='classtextoreadonly';
	calcular();
	return;
}

// CALCULA EL IMPORTE EN AUTOMATICO
function importe(i){
	var uni = $F('unidad['+i+']')*1;
	var mbase = $F('montobase['+i+']')*1;
	var pnocturno = $F('plusnocturno['+i+']')*1;
	var pfestivo = $F('plusfestivo['+i+']')*1;

	if ('<?=$asig->asistencia->arrcondicionservicio?>'=='CON')
	{
		//		if ('<?=$asig->asistencia->arrcondicionservicio?>'==){
		$('na_montoreal['+i+']').value = uni*(mbase+pnocturno+pfestivo);
		$('na_montoestimado['+i+']').value = uni*(mbase+pnocturno+pfestivo);
		//		}
	}
	else
	{
		$('aa_montoreal['+i+']').value = uni*(mbase+pnocturno+pfestivo);
		$('aa_montoestimado['+i+']').value = uni*(mbase+pnocturno+pfestivo);
	}
	return;
}


// CALCULA LA SUMA DE LOS IMPORTES QUE TIENEN TIENEN ACTIVADO EL CHECKBOX
function calcular(){

	var AA_totalreal = 0;
	var NA_totalreal = 0;
	var CC_totalreal = 0;
	var AA_totalestimado = 0;
	var NA_totalestimado = 0;
	var CC_totalestimado = 0;
	var costofijo=0;
	filas='';

	for(var i=1; i<=<?=$num_reg?>;i++)
	{
		fila[i]='';
		if ($F('check['+i+']'))
		{
			AA_totalreal = AA_totalreal + parseFloat($F('aa_montoreal['+i+']')*1);
			NA_totalreal = NA_totalreal + parseFloat($F('na_montoreal['+i+']')*1);
			CC_totalreal = CC_totalreal + parseFloat($F('cc_montoreal['+i+']')*1);

			if ($F('costonegociado['+i+']')=='1'){
				costofijo = costofijo + parseFloat($F('aa_montoreal['+i+']')*1)+ parseFloat($F('na_montoreal['+i+']')*1) + parseFloat($F('cc_montoreal['+i+']')*1);

			}
			AA_totalestimado = AA_totalestimado + parseFloat($F('aa_montoestimado['+i+']')*1);
			NA_totalestimado = NA_totalestimado + parseFloat($F('na_montoestimado['+i+']')*1);
			CC_totalestimado = CC_totalestimado + parseFloat($F('cc_montoestimado['+i+']')*1);


			fila[i]=$('nom_costo['+i+']').innerHTML+" AA =" + $F('aa_montoreal['+i+']') + " NA="+ $F('na_montoreal['+i+']')+ " CC=" + $F('cc_montoreal['+i+']')+"\n";
		}

		filas=filas +fila[i];
	}
	$('aa_totalreal').value = AA_totalreal;
	$('na_totalreal').value = NA_totalreal;
	$('cc_totalreal').value = CC_totalreal;
	$('importereal').value  = AA_totalreal + NA_totalreal + CC_totalreal;

	$('aa_totalestimado').value = AA_totalestimado;
	$('na_totalestimado').value = NA_totalestimado;
	$('cc_totalestimado').value = CC_totalestimado;
	$('importeestimado').value = AA_totalestimado + NA_totalestimado + CC_totalestimado;
	$('costofijo').value = costofijo;


	//	if ("<?=in_array($asig->asistencia->arrstatusasistencia,array('CON','CP','CM'))?>") $('form_calculadora').disable();

	if ("<?=$asig->asistencia->expediente->arrstatusexpediente?>"=='CER') $('form_calculadora').disable();
	return;
}



function confirmar(){

	var mtotal = new oNumero($F('importereal'));
	Dialog.confirm("<?=_('UD. ESTA GRABANDO UN COSTO DE : ')?>"+"<br><center><h1>"+"<?=($asig->moneda->idmoneda=='')?$asig->proveedor->moneda->simbolo:$asig->moneda->simbolo?>"+' '+mtotal.formato(2,true),
	{
		top: 10,
		width:250,
		showEffect: Element.show,
		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "Si",
		cancelLabel:"No",
		buttonClass: "normal",
		onOk: function(dlg){
			grabar();
			return true;
		}
	});

	return;
}

function grabar()
{
	new Ajax.Request('../../../controlador/ajax/ajax_asistencia_prov_costo.php',
	{
		method: 'post',
		parameters:  $('form_calculadora').serialize(true),
		onSuccess: function(t){

			comentario ="SE GRABARON COSTOS A LA ASISTENCIA:\n";
			comentario +="PROVEEDOR = <?=$asig->proveedor->nombrefiscal?>\t CONDICION = <?=$asig->statusproveedor?>\n";
			comentario += "MONEDA = <?=$asig->proveedor->moneda->simbolo?>\n";
			comentario += filas;
			comentario += "TOTAL  AA ="+ $F('aa_totalreal')+" NA ="+ $F('na_totalreal')+" CC ="+ $F('cc_totalreal')+"\n";
			comentario += "TOTAL PROVEEDOR = "+ $F('importereal')+"\n";


			new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
			{
				method : 'post',
				parameters :{
					IDASISTENCIA:"<?=$asig->asistencia->idasistencia?>" ,
					IDETAPA:"7", // etapa de costeo
					ARRCLASIFICACION:'BIT',
					IDUSUARIOMOD:"<?=$idusuario?>",
					COMENTARIO: comentario
				},
				onSuccess: function()
				{
					parent.win.close();
				}
			});
		}
	});
	return;
}

function cerrar(){
	parent.win.close();
	return;
}





function mostrar_comentario(campo,zona)
{
	var my_tooltip = new Tooltip(campo, zona);
	return;
}






</script>