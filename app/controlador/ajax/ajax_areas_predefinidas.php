<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_poligono.inc.php');

$unid= new poligono();

$_POST[LATITUD]= explode(';',$_POST[LATITUD]);
$_POST[LONGITUD]= explode(';',$_POST[LONGITUD]);

array_pop($_POST[LATITUD]);
array_pop($_POST[LONGITUD]);


switch($_GET[opcion]){
	case 'grabar':
		{
			// graba el poligono
			$unid->grabar($_POST);
			break;
		}

	case 'borrar':
		{
			$unid->borrar_poligono($_POST[IDPOLIGONO]);
			break;
		}
	case 'contar':
		{
			$sql="
			SELECT
				COUNT(IDPOLIGONO) contador
			FROM
				catalogo_proveedor_servicio_x_poligono
			WHERE 
				IDPOLIGONO='$_POST[IDPOLIGONO]'
			";
			$result=$unid->con->query($sql);
			$reg = $result->fetch_object();
			echo $reg->contador;
			break;
		}
}

return;
?>

