<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli('','pe');

$fecha_hoy = date("Y-m-d");

if ($_POST[nombreProveedor]!='') $cond1 = "AND NOMBRECOMERCIAL ='$_POST[nombreProveedor]'";


$sql="
SELECT 
    cp.IDPROVEEDOR,
    cp.NOMBREFISCAL,
    cp.NOMBRECOMERCIAL,
    cp.ACTIVO,
    cp.INTERNO,
    cpt.NUMEROTELEFONO,
    cpt.IDTSP,
   (SELECT LATITUD FROM $con->temporal.posicionGPS WHERE receptor_id= cpt.NUMEROTELEFONO AND fecha_gps>=DATE(NOW())  ORDER BY fecha_gps DESC LIMIT 1) LATITUD,
   (SELECT LONGITUD FROM $con->temporal.posicionGPS WHERE receptor_id= cpt.NUMEROTELEFONO AND fecha_gps>=DATE(NOW())  ORDER BY fecha_gps DESC LIMIT 1) LONGITUD,
   (SELECT fecha_gps FROM $con->temporal.posicionGPS WHERE receptor_id= cpt.NUMEROTELEFONO AND fecha_gps>=DATE(NOW())  ORDER BY fecha_gps DESC LIMIT 1) FECHA_GPS
FROM
   $con->catalogo.catalogo_proveedor cp
    LEFT JOIN $con->catalogo.catalogo_proveedor_telefono cpt ON cpt.IDPROVEEDOR = cp.IDPROVEEDOR 
WHERE
cp.ACTIVO = 1
AND cp.INTERNO = 1
AND cpt.IDTSP = 3
GROUP BY cpt.NUMEROTELEFONO
HAVING 
LATITUD IS NOT NULL
AND LONGITUD IS NOT NULL
$cond1
";



$con->query($sql);

$result = $con->query($sql);

header('content-type: text/xml');
$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
$output .= "<data>";
while ($row = $result->fetch_object()){
    $sql="SELECT
            cs.IDSERVICIO,
	        SUBSTRING(cs.DESCRIPCION,INSTR(cs.DESCRIPCION,'-')+2 ) DESCRIPCION
          FROM 
            $con->catalogo.catalogo_proveedor_servicio cps
            LEFT JOIN $con->catalogo.catalogo_servicio cs ON cs.IDSERVICIO = cps.IDSERVICIO
          WHERE 
            cps.IDPROVEEDOR='$row->IDPROVEEDOR'";

    $result1=$con->query($sql);
    $servicios= null;
    $sw=0;
    while($reg=$result1->fetch_object()){
        $servicios[]=$reg->DESCRIPCION;
        if ($reg->IDSERVICIO == $_POST[idservicio]) $sw=1;
    }

    if ($sw or ($_POST[idservicio]=='') ){
    $output .= "<proveedor>";
    $output .= "<id>".$row->IDPROVEEDOR."</id>";
    $output .= "<nc>".$row->NOMBRECOMERCIAL."</nc>";
    $output .= "<telf>".$row->NUMEROTELEFONO."</telf>";
    $output .= "<lat>".$row->LATITUD."</lat>";
    $output .= "<lng>".$row->LONGITUD."</lng>";
    $output .= "<fgps>".$row->FECHA_GPS."</fgps>";
    $output .= "<serv>".implode(",\n ",$servicios)."</serv>";
    $output .= "</proveedor>";
    }
}
unset($con);
unset($result);
unset($result1);

$output .= "</data>";

echo $output;


?>