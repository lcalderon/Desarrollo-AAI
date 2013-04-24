<?
include_once('/app/modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$db =$con->catalogo;
$con->select_db($db);

if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}



$dist= '22';	//$_GET[dist];
$prov='01';		//$_GET[prov];
$dep='15';			//$_GET[dep];
$lat= $_GET[lat]; // -12.11890475;
$lon= $_GET[lon];   //-77.02925775;



$sql="select  
CONCAT(cp.LATITUD,',',cp.LONGITUD,',',cp.DIRECCION,',',cp.NOMBRECOMERCIAL,',',cpt.TELEFONO,',',ctt.DESCRIPCION,',',ctsp.DESCRIPCION,',',csf.TIPO,',',csf.COLOR,'/') CADENA,
distancia($lat,$lon,cp.LATITUD,cp.LONGITUD) DISTANCIA
from catalogo_proveedores cp,
catalogo_proveedores_telefono cpt,
catalogo_tipotelefono ctt,
catalogo_tsp ctsp,
catalogo_proveedores_servicios cps,
catalogo_servicios cs,
catalogo_servicios_familia csf

where
cp.CVEPROVEEDOR= cpt.CVEPROVEEDOR
and cpt.CVETIPOTELEFONO = ctt.CVETIPOTELEFONO
and cpt.CVETSP = ctsp.CVETSP
and cp.CVEPROVEEDOR = cps.CVEPROVEEDOR
and cps.CVESERVICIO = cs.CVESERVICIO
and cs.CVEFAMILIA = csf.CVEFAMILIA

AND LATITUD<>0 
AND LONGITUD<>0
GROUP BY DISTANCIA
order by csf.COLOR,DISTANCIA ASC";

$result=$con->query($sql);
while($reg=$result->fetch_object()){
	$xml.= $reg->CADENA;
}

echo $xml;