<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');

$con = new DB_mysqli();

$idproveedor = $_GET[idproveedor];
$idusuariomod = $_SESSION[user];
$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" ></link>
</head>
<body>
<fieldset>
<legend><?=_('Datos de Referencia')?></legend>
<form id='form_experiencia'>
<?=_('EMPRESA') ?> <input type='text' name='EMPRESAREFERENCIA' id='empresareferencia' value='' size='30' style="text-transform:uppercase;">
<?=_('ANIO') ?> <input type='text' name='ANIOSERVICIO' id='anioservicio' value='' size='4' onKeyPress="return validarnumero(event)">
<input type="hidden" name="IDPROVEEDOR" value="<?=$idproveedor?>">
<input type="hidden" name="IDUSUARIOMOD" value="<?=$idusuariomod?>">

<input type='button' value="<?=_('GRABAR')?>" onclick="grabar();" class="normal" <?=($edicion==1)?'':'disabled'?>>
</form>
</fieldset>
<div id='listado_empresas' style=" overflow:auto">
<? include_once('listado_proveedores_experiencia.php');?>

</div>

</body>
</html>
<script type="text/javascript">
function grabar(){
	if (trim($F('empresareferencia'))=='') alert("<?=_('INGRESE LA EMPRESA DE REFERENCIA')?>");
	else if (trim($F('anioservicio'))=='') alert("<?=_('INGRESE LOS AÑOS DE SERVICIO')?>");
	else{

		new Ajax.Request('../../../controlador/ajax/ajax_proveedor_experiencia.php?opcion=grabar',
		{
			method: 'post',
			parameters:  $('form_experiencia').serialize(true),
			onSuccess: function(){
				listado();
			}
		});
	}
	return;
}

function listado(){
	new Ajax.Updater('listado_empresas',"listado_proveedores_experiencia.php",
	{
		method : 'post',
		parameters: { 
			idproveedor : '<?=$idproveedor?>',
			edicion: '<?=$edicion?>'
		}
	}
	);
	return;
}

function borrar_empresa(idproveedorexp){
	new Ajax.Request('../../../controlador/ajax/ajax_proveedor_experiencia.php?opcion=borrar',
	{
		method: 'post',
		parameters:  { IDPROVEEDOREXP: idproveedorexp },
		onSuccess: function()
		{
			listado();
		}
	});
	return;
}


</script>


