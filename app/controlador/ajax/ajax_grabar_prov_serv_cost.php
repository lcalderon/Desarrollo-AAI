<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');


for($i=0;$i<=count($_POST[MONTO]);$i++ )
{
	if ($_POST[MONTO][$i]!='')
	{
		
		if ($_POST[UNIDAD][$i]=='') {
			echo "Seleccione UNIDAD";
			exit;
		}
		if ($_POST[IDMEDIDA][$i]=='') {
			echo "Seleccione MEDIDA";
			exit;
		}
	}	
}
$prov = new proveedor();
$prov->grabar_prov_serv_cost($_POST);
return;

?>