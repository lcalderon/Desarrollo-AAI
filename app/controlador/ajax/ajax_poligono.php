<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_poligono.inc.php');

$opcion = $_GET[opcion];
$unid= new poligono();

$_POST[LATITUD]= explode(';',$_POST[LATITUD]);
$_POST[LONGITUD]= explode(';',$_POST[LONGITUD]);

array_pop($_POST[LATITUD]);
array_pop($_POST[LONGITUD]);



switch($opcion){
	case 'grabar':
		{
			// graba el poligono
			$unid->grabar($_POST);

			if (($_POST[IDPROVEEDOR]!='') AND ($_POST[IDSERVICIO]!=''))
			{
				$prov_serv_poli[IDPROVEEDOR]=$_POST[IDPROVEEDOR];
				$prov_serv_poli[IDSERVICIO]=$_POST[IDSERVICIO];
			}

			$prov_serv_poli[IDPOLIGONO]= $unid->idpoligono;
			$prov_serv_poli[ARRAMBITO]=$_POST[ARRAMBITO];
			$prov_serv_poli[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

			// graba en la tabla catalogo_proveedor_servicio_x_poligono
			if ($unid->con->exist('catalogo_proveedor_servicio_x_poligono',"IDPOLIGONO","  where IDPOLIGONO = '$prov_serv_poli[IDPOLIGONO]'"))
			{
				$unid->con->update('catalogo_proveedor_servicio_x_poligono',$prov_serv_poli," where IDPOLIGONO ='$prov_serv_poli[IDPOLIGONO]'");
			}
			else
			{
				$unid->con->insert_reg('catalogo_proveedor_servicio_x_poligono',$prov_serv_poli);
			
			}

			break;
		}

	case 'borrar':
		{
			
		 $unid->borrar_poligono($_POST[IDPOLIGONO]);
			$sql="
			DELETE FROM
			catalogo_proveedor_servicio_x_poligono
			WHERE
			IDPOLIGONO = '$_POST[IDPOLIGONO]'
			";	
			$unid->con->query($sql);
			break;
		}
}

return;
?>

