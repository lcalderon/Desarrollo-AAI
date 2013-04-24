<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_poligono.inc.php');

$opcion = $_GET[opcion];
$unid= new poligono();



switch($opcion){
	case 'grabar':
		{	$sql="
			insert into
			catalogo_proveedor_servicio_x_poligono
			set 
			IDPOLIGONO ='$_POST[IDPOLIGONO]',
			IDPROVEEDOR ='$_POST[IDPROVEEDOR]',
			IDSERVICIO = '$_POST[IDSERVICIO]',
			ARRAMBITO = '$_POST[ARRAMBITO]',
			IDUSUARIOMOD ='$_POST[IDUSUARIOMOD]'
			
		";
//		echo $sql;
		$unid->con->query($sql);
		break;
		}

	case 'borrar':
		{
			$sql="
			DELETE FROM
			catalogo_proveedor_servicio_x_poligono
			WHERE
			IDPOLIGONO ='$_POST[IDPOLIGONO]'
			AND IDPROVEEDOR ='$_POST[IDPROVEEDOR]'
			AND IDSERVICIO = '$_POST[IDSERVICIO]'
			AND ARRAMBITO = '$_POST[ARRAMBITO]'
			";
			$unid->con->query($sql);
			
			break;
		}
}

return;
?>

