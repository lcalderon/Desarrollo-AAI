<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$db =$con->catalogo;
$con->select_db($db);

if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}


$opcion = $_GET[opcion];



switch($opcion){
	case 'grabar':
		{
			$prov_serv_circ[IDPROVEEDOR]=$_POST[IDPROVEEDOR];
			$prov_serv_circ[IDSERVICIO]=$_POST[IDSERVICIO];

			$prov_serv_circ[ARRAMBITO] = $_POST[ARRAMBITO];
			$prov_serv_circ[IDUSUARIOMOD] = $_POST[IDUSUARIOMOD];

			$circulo[IDCIRCULO]= $_POST[IDCIRCULO];
			$circulo[LATITUD]= $_POST[LATITUD];
			$circulo[LONGITUD]= $_POST[LONGITUD];
			$circulo[RADIO]= $_POST[RADIO];
			$circulo[IDMEDIDA]= $_POST[IDMEDIDA];
			$circulo[ZOOMMAP]= $_POST[ZOOMMAP];
			$circulo[IDUSUARIOMOD]= $_POST[IDUSUARIOMOD];

			if ($con->exist('catalogo_circulo','IDCIRCULO'," where IDCIRCULO = '$circulo[IDCIRCULO]' "))
			{
				$con->update('catalogo_circulo',$circulo," WHERE IDCIRCULO = '$circulo[IDCIRCULO]'");
				unset($prov_serv_circ[IDPROVEEDOR]);
				unset($prov_serv_circ[IDSERVICIO]);
				$con->update('catalogo_proveedor_servicio_x_circulo',$prov_serv_circ," WHERE IDCIRCULO = '$circulo[IDCIRCULO]'");
			}
			else
			{
				$con->insert_reg('catalogo_proveedor_servicio_x_circulo',$prov_serv_circ);
				$circulo[IDCIRCULO] = $con->insert_id;
				$con->insert_reg('catalogo_circulo',$circulo);

			}
			break;
		}
	case 'borrar':
		{
			$idcirculo = $_POST[IDCIRCULO];
			$sql="
				DELETE FROM
					catalogo_circulo
				WHERE
					IDCIRCULO = '$idcirculo'
				";
			$con->query($sql);

			$sql="
				DELETE FROM
					catalogo_proveedor_servicio_x_circulo
				WHERE
					IDCIRCULO = '$idcirculo'
				";
			$con->query($sql);
			
			break;

		}

}

return;
?>

