<?
if (isset($_GET[IDCONTACTO]))
{
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_contacto.inc.php');
	$contacto = new contacto();
	$contacto->carga_datos($_GET[IDCONTACTO]);
	
	$idetapa=$_GET[IDETAPA];
	$idasistencia=$_GET[IDASISTENCIA];
	$idextension=$_GET[IDEXTENSION];
}

foreach ($contacto->telefonos as $telefonos)
$telef_cont[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];


foreach ($telef_cont as $indice =>$telefono):?>
<?	if ($telefono!=''):?>
	<input type='text' size='20' readonly value="<?=$telefono?>">
	<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=
'llamada_asistencia("<?=$telefono?>","<?=$idextension?>","<?=$idetapa?>","<?=$idasistencia?>")'></img><br>
<?endif;?>
<?endforeach;?>
 
