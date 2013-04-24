<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');

$opcion = $_GET[opcion];
$prov = new proveedor();

switch($opcion){
	case 'grabar':
		{
			$prov->grabar_contacto($_POST);
			break;
		}

	case 'borrar':
		{
			$prov->borrar_contacto($_POST[IDCONTACTO]);
			break;
		}
}

return;
?>

