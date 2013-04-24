<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_unidadfederativa.inc.php');

$opcion = $_GET[opcion];
$unid= new unidadfederativa();

switch($opcion){
	case 'grabar':	
	{
		$unid->grabar($_POST);
		break;
	}

	case 'borrar':	
	{
		$unid->borrar($_POST[IDUNIDADFEDERATIVA]);
		$sql="
			DELETE FROM
			catalogo_proveedor_servicio_x_unidad_federativa
			WHERE
			IDUNIDADFEDERATIVA = '$_POST[IDUNIDADFEDERATIVA]'
			";	
			$unid->con->query($sql);
		break;
	}
}

return;
?>

