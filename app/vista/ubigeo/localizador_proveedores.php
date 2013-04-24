<?
/*
Programa de localizaci�n de proveedores

Input: 1.- Ubicaci�n del afiliado (latitud,longitud)
	   2.- Servicio (idservicio)
	   
	   
Autor:  Frank S�nchez Moreyra


*/
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');

include_once('../../modelo/clase_poligono.inc.php');
include_once('../../modelo/clase_circulo.inc.php');


$idservicio = $_GET[idservicio];

$prov =  new proveedor();

$ubicacion->cvepais='PE';
$ubicacion->cveentidad1='15';
$ubicacion->cveentidad2='0';
$ubicacion->cveentidad3='0';
$ubicacion->cveentidad4='0';
$ubicacion->cveentidad5='0';
$ubicacion->cveentidad6='0';
$ubicacion->cveentidad7='0';

//$ubicacion->latitud='';
//$ubicacion->longitud='';

$lista_prov = $prov->lista_proveedores($ubicacion,'80');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=_('Localizacion de proveedores')?></title>
	
</head>
<body>
<h1><?=_('Localizacion de proveedores')?></h1>
<table class='catalogos'>
<tr>
<th>ID</th>
<th>PROVEEDOR</th>
<th>AMBITO</th>
<th>DISTANCIA</th>

</tr>
<?
foreach ($lista_prov as $idproveedor=>$datos)
{
	
	$prov->carga_datos($idproveedor);
	
	echo "<tr>";
	echo "<td>$prov->idproveedor</td>";
	echo "<td>$prov->nombrecomercial</td>";
	echo "<td>$datos[AMBITO]</td>";
	echo "<td>".number_format($datos[DISTANCIA],2)." Km</td>";
	echo "</tr>";
}

$prov->carga_datos(1);


?>
</table>


<div id='zona_proveedores'>




</div>

</body>
</html>

