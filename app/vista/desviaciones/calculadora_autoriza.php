<?
session_start();
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
include_once('../../modelo/clase_familia.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_etapa.inc.php');
include_once('../../modelo/clase_plantilla.inc.php');
include_once('../../modelo/clase_servicio.inc.php');
include_once('../../modelo/clase_programa.inc.php');
include_once('../../modelo/clase_cuenta.inc.php');
include_once('../../modelo/clase_persona.inc.php');
include_once('../../modelo/clase_afiliado.inc.php');
include_once('../../modelo/clase_contacto.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once('../../modelo/clase_asigprov.inc.php');
include_once('../includes/arreglos.php');
include_once("/app/vista/login/Auth.class.php");

$modo='AUTORIZA';

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


$sql="
SELECT 
	csc.IDCOSTO,
	cc.DESCRIPCION, 
	cc.COSTONEGOCIADO,
	cc.ARRCARGOACUENTA,
	if('$asig->localforaneo'='L',cpscn.MONTOLOCAL,cpscn.MONTOFORANEO) MONTOBASE,
	if('$asig->aplicanocturno'='1',cpscn.PLUSNOCTURNO,'') PLUSNOCTURNO,
	if('$asig->aplicafestivo'='1',cpscn.PLUSFESTIVO,'') PLUSFESTIVO,		
	cpscn.UNIDAD UNIDAD_NEGOCIADO,
	cpscn.IDMEDIDA IDMEDIDA_NEGOCIADO
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
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
	
	
</head>
<body onload="calcular();">

<form name='form_calculadora' id='form_calculadora'>

<input type='hidden' name='MODO' value='<?=$modo?>'>
<input type='hidden' name='IDASIGPROV' value='<?=$asig->idasigprov?>'>
<input type='hidden' name='IDMONEDA' value="<?=($asig->moneda->idmoneda=='')?$asig->proveedor->moneda->idmoneda:$asig->moneda->idmoneda?>" >
<input type='hidden' name='IDASISTENCIA' value="<?=$asig->asistencia->idasistencia ?>">

<?
$etiqueta = ($asig->localforaneo=='L')?_('LOCAL'):_('FORANEO');
$etiqueta .= ($asig->aplicanocturno)?'->'._('NOCTURNO'):'';
$etiqueta .= ($asig->aplicafestivo)?'->'._('FESTIVO'):'';

?>

<table class="calculadora" >
<tr>
	<th colspan="6" align="left"><h1>Proveedor: <?=$asig->proveedor->nombrefiscal?></h1></th>
</tr>
<tr>
	<th colspan="4" align="left"><u><h1>Asistencia: <?=$etiqueta; ?></h1></u></th>
</tr>
<tr>
<th colspan='4'></th>
<th colspan='3' align='center'><?=_('IMPORTES REALES')?></th>
<th colspan='3' align='center'><?=_('IMPORTES AUTORIZADOS')?></th>
</tr>
<tr>
<th width="5%"></th>
<th width="25%"><?=_('CONCEPTO')?></th>
<th width="30%"><?=_('UNIDADES')?></th>
<th></th>
<th width="10%" title="<?=_('AMERICAN ASSIST')?>"><?=_('AA')?></th>
<th width="10%" title="<?=_('NUESTRO AFILIADO')?>"><?=_('NA')?></th>
<th width="10%" title="<?=_('CLIENTE CORPORATIVO')?>"><?=_('CC')?></th>
<th width="10%" title="<?=_('AMERICAN ASSIST')?>"><?=_('AA')?></th>
<th width="10%" title="<?=_('NUESTRO AFILIADO')?>"><?=_('NA')?></th>
<th width="10%" title="<?=_('CLIENTE CORPORATIVO')?>"><?=_('CC')?></th>
<th width="5%"></th>
</tr>
<?
$i=1;
while ($reg= $result->fetch_object())
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
	echo "<input type='hidden' name='IDUSUARIOMOD' id='idusuariomod' value='$idusuario' >";

	echo "<td><input type ='checkbox'  name='CHECK[$i]' id='check[$i]'  onChange=act_des($i) $checked  ></td>";
	echo "<td align='left' id='nom_costo[$i]'>$reg->DESCRIPCION</td>";
	echo "<td><input type ='text' name='UNIDAD[$i]' id='unidad[$i]' size='3' dir='rtl' value='".(isset($asig->costos[$reg->IDCOSTO][UNIDAD])?$asig->costos[$reg->IDCOSTO][UNIDAD]:1)."' size='4' onchange='importe($i);calcular();' class='classtexto'  onBlur='calcular();' onKeyPress='return validarnumero(event)' disabled >$reg->IDMEDIDA_NEGOCIADO</td>";
	echo "<input type ='hidden' name='MONTOBASE[$i]' id='montobase[$i]' value ='$reg->MONTOBASE' size='10' dir='rtl' class='classtexto'  disabled>";
	echo "<input type ='hidden' name='PLUSNOCTURNO[$i]' id='plusnocturno[$i]' value ='$reg->PLUSNOCTURNO' size='10' dir='rtl' class='classtexto' disabled>";
	echo "<input type ='hidden' name='PLUSFESTIVO[$i]' id='plusfestivo[$i]' value ='$reg->PLUSFESTIVO' size='10' dir='rtl' class='classtexto' disabled>";


	echo "<td>{$asig->proveedor->moneda->simbolo}</td>";

	echo "<td style='background:#73C473'><input type='text' name='AA_MONTOREAL[$i]' id='aa_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][AA_MONTOREAL]}' size='10'  class='classtextoreadonly' readonly ></td>";
	echo "<td style='background:#73C473'><input type='text' name='NA_MONTOREAL[$i]' id='na_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][NA_MONTOREAL]}' size='10'  class='classtextoreadonly' readonly ></td>";
	echo "<td style='background:#73C473'><input type='text' name='CC_MONTOREAL[$i]' id='cc_montoreal[$i]' value='{$asig->costos[$reg->IDCOSTO][CC_MONTOREAL]}' size='10'  class='classtextoreadonly' readonly ></td>";

	echo "<td style='background:#E68B2C'><input type='text' name='AA_MONTOAUTORIZADO[$i]' id='aa_montoautorizado[$i]' value='{$asig->costos[$reg->IDCOSTO][AA_MONTOAUTORIZADO]}' size='10' onKeyPress='return numeroDecimal(event);'  onblur='calcular()'". (($asig->asistencia->arrcondicionservicio=='CON')?" class='classtextoreadonly' readonly ": $condicion) ."></td>";
	echo "<td style='background:#E68B2C'><input type='text' name='NA_MONTOAUTORIZADO[$i]' id='na_montoautorizado[$i]' value='{$asig->costos[$reg->IDCOSTO][NA_MONTOAUTORIZADO]}' size='10' onKeyPress='return numeroDecimal(event);'  onblur='calcular()' $condicion ></td>";
	echo "<td style='background:#E68B2C'><input type='text' name='CC_MONTOAUTORIZADO[$i]' id='cc_montoautorizado[$i]' value='{$asig->costos[$reg->IDCOSTO][CC_MONTOAUTORIZADO]}' size='10' onKeyPress='return numeroDecimal(event);'  onblur='calcular()' $condicion ></td>";

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
		<textarea rows="4" name='OBSERVACION' id='observacion' class='classtexto' cols="40" ><?=trim($asig->observacion)?></textarea>
		</td>
		<td rowspan="3" colspan="10">
		<fieldset>
			<legend><?=_('TOTALES')?></legend>
			<table class="calculadora" align="left">
  				<tr>
					<td width='50%'><?=_('AMERICAN ASSIST')?></td>
					<td width='50%'><?=$asig->proveedor->moneda->simbolo?>
					<input type='text' name='AA_TOTALAUTORIZADO' id='aa_totalautorizado'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
				<tr>
					<td ><?=_('NUESTRO AFILIADO')?></td>
					<td ><?=$asig->proveedor->moneda->simbolo?>
					<input type='text' name='NA_TOTALAUTORIZADO' id='na_totalautorizado'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
				<tr>
					<td ><?=_('CLIENTE CORPORATIVO')?></td>
					<td ><?=$asig->proveedor->moneda->simbolo?>
					<input type='text' name='CC_TOTALAUTORIZADO' id='cc_totalautorizado'  value='0'  size='10' dir='rtl'  class='classtexto' readOnly>
					</td>
				</tr>
  				
				<tr>
					<td><b><?=_('TOTAL ASISTENCIA').' '. $asig->proveedor->moneda->simbolo; ?></b></td>
					<td>
					<input type='text' name="IMPORTEAUTORIZADO" id='importeautorizado' dir="rtl" style='font-size: 20px;font-family: Arial, serif;font-weight: normal;'>		
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
	<input type="button" value="<?=_('CANCELAR')?>" onclick="cerrar();">
	<input type="button" value="<?=_('GRABAR')?>" onclick="return confirmar(); grabar();">
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
	if ($('check['+i+']').checked ) {
		importe(i);
		abrir_importe(i);
	}
	else{
		cerrar_importe(i);

	}

	calcular();
	return;
}

// ACTIVA LA EDICION DEL CAMPO IMPORTE Y JUSTIFICACION
function abrir_importe(i){

	if ('<?=$asig->asistencia->arrcondicionservicio?>'=='CON')
	{
		$('na_montoautorizado['+i+']').className='classtexto';
		$('cc_montoautorizado['+i+']').className='classtexto';
		$('na_montoautorizado['+i+']').readOnly = false;
		$('cc_montoautorizado['+i+']').readOnly = false;
	}
	else
	{
		$('aa_montoautorizado['+i+']').className='classtexto';
		$('na_montoautorizado['+i+']').className='classtexto';
		$('cc_montoautorizado['+i+']').className='classtexto';
		$('aa_montoautorizado['+i+']').readOnly = false;
		$('na_montoautorizado['+i+']').readOnly = false;
		$('cc_montoautorizado['+i+']').readOnly = false;
	}
	calcular();
	return;
}


function cerrar_importe(i){
	$('check['+i+']').checked = false;
	$('aa_montoautorizado['+i+']').clear();
	$('aa_montoautorizado['+i+']').readOnly = true;
	$('aa_montoautorizado['+i+']').className='classtextoreadonly';
	$('na_montoautorizado['+i+']').clear();
	$('na_montoautorizado['+i+']').readOnly = true;
	$('na_montoautorizado['+i+']').className='classtextoreadonly';
	$('cc_montoautorizado['+i+']').clear();
	$('cc_montoautorizado['+i+']').readOnly = true;
	$('cc_montoautorizado['+i+']').className='classtextoreadonly';
	calcular();
	return;
}

// CALCULA EL IMPORTE EN AUTOMATICO
function importe(i){
	var uni = $F('unidad['+i+']')*1;
	var mbase = $F('montobase['+i+']')*1;
	var pnocturno = $F('plusnocturno['+i+']')*1;
	var pfestivo = $F('plusfestivo['+i+']')*1;

	$('aa_montoautorizado['+i+']').value = uni*(mbase+pnocturno+pfestivo);
	//	$('aa_montoestimado['+i+']').value = uni*(mbase+pnocturno+pfestivo);

	return;
}


// CALCULA LA SUMA DE LOS IMPORTES QUE TIENEN TIENEN ACTIVADO EL CHECKBOX
function calcular(){

	var AA_totalreal = 0;
	var NA_totalreal = 0;
	var CC_totalreal = 0;
	var AA_totalautorizado = 0;
	var NA_totalautorizado = 0;
	var CC_totalautorizado = 0;

	filas='';

	for(var i=1; i<=<?=$num_reg?>;i++)
	{
		fila[i]='';
		if ($F('check['+i+']'))
		{
			AA_totalreal = AA_totalreal + parseFloat($F('aa_montoreal['+i+']')*1);
			NA_totalreal = NA_totalreal + parseFloat($F('na_montoreal['+i+']')*1);
			CC_totalreal = CC_totalreal + parseFloat($F('cc_montoreal['+i+']')*1);

			AA_totalautorizado = AA_totalautorizado + parseFloat($F('aa_montoautorizado['+i+']')*1);
			NA_totalautorizado = NA_totalautorizado + parseFloat($F('na_montoautorizado['+i+']')*1);
			CC_totalautorizado = CC_totalautorizado + parseFloat($F('cc_montoautorizado['+i+']')*1);

			fila[i]=$('nom_costo['+i+']').innerHTML+" AA =" + $F('aa_montoautorizado['+i+']') + " NA="+ $F('na_montoautorizado['+i+']')+ " CC=" + $F('cc_montoautorizado['+i+']')+"\n";
		}

		filas=filas +fila[i];
	}

	$('aa_totalautorizado').value = AA_totalautorizado;
	$('na_totalautorizado').value = NA_totalautorizado;
	$('cc_totalautorizado').value = CC_totalautorizado;
	$('importeautorizado').value  = AA_totalautorizado + NA_totalautorizado + CC_totalautorizado;



	if ('<?=$asig->asistencia->statusautorizaciondesvio?>'=='1') $('form_calculadora').disable();
	return;
}



function confirmar(){

	var mtotal = new oNumero($F('importeautorizado'));
	Dialog.confirm("<?=_('UD. ESTA GRABANDO UN COSTO DE ')?>"+": <br><center><h1>"+"<?=($asig->moneda->idmoneda=='')?$asig->proveedor->moneda->simbolo:$asig->moneda->simbolo?>"+' '+mtotal.formato(2,true),
	{
		top: 10,
		width:250,
		showEffect: Element.show,
		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "Si",
		cancelLabel:"No",
		onOk: function(dlg){
			grabar();
			return true;
		}
	});

	return;
}

function grabar()
{
	new Ajax.Request('/app/controlador/ajax/ajax_asistencia_prov_costo.php',
	{
		method: 'post',
		parameters:  $('form_calculadora').serialize(true),
		onSuccess: function(t){

			comentario ="SE MODIFICARON COSTOS AUTORIZADOS A LA ASISTENCIA \n";
			comentario +="PROVEEDOR = <?=$asig->proveedor->nombrefiscal?>\t CONDICION = <?=$asig->statusproveedor?>\n";
			comentario += "MONEDA = <?=$asig->proveedor->moneda->simbolo?>\n";
			comentario += filas;
			comentario += "TOTAL  AA ="+ $F('aa_totalautorizado')+" NA ="+ $F('na_totalautorizado')+" CC ="+ $F('cc_totalautorizado')+"\n";
			comentario += "TOTAL PROVEEDOR = "+ $F('importeautorizado')+"\n\n";
			comentario += "AUTORIZADO POR : <?=$idusuario?>\n\n";
			comentario += "OBSERVACION : "+ $F('observacion')+"\n\n";

			new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
			{
				method : 'post',
				parameters :{
					IDASISTENCIA:"<?=$asig->asistencia->idasistencia?>" ,
					IDETAPA:"7", // etapa de costeo
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
</script>