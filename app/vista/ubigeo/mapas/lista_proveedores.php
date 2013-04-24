<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$lista_servicios = $_POST['SERVICIO'];
$lista_entidades = $_POST['ENTIDADES'];

$cond_serv='';
$cond_entidades ='';

if ($lista_servicios!='') $cond_serv = " AND cps.IDSERVICIO IN ($lista_servicios) ";


if ($lista_entidades!='' && $cond_serv!='') $cond_entidades= " AND ce.ID IN ($lista_entidades) ";
else $cond_entidades= " AND ce.ID IN (0)";
$sql="
SELECT 
	cpu.IDPROVEEDOR,
	cpu.DIRECCION,
	cpu.LATITUD,
	cpu.LONGITUD,
	cp.NOMBRECOMERCIAL,
	if(cf.COLOR='','blue',cf.COLOR) COLOR
FROM 
	$con->catalogo.catalogo_proveedor cp,
	$con->catalogo.catalogo_proveedor_ubigeo cpu,
	$con->catalogo.catalogo_proveedor_servicio cps,
	$con->catalogo.catalogo_servicio cs,
	$con->catalogo.catalogo_familia cf,
	$con->catalogo.catalogo_entidad ce
WHERE 
cp.IDPROVEEDOR = cpu.IDPROVEEDOR 
AND cp.IDPROVEEDOR = cps.IDPROVEEDOR
AND cps.IDSERVICIO = cs.IDSERVICIO
AND cs.IDFAMILIA = cf.IDFAMILIA
AND cpu.CVEENTIDAD1 = ce.CVEENTIDAD1
AND cpu.CVEENTIDAD2 = ce.CVEENTIDAD2
$cond_entidades
AND cpu.LATITUD !='0'
AND cpu.LONGITUD !='0'
$cond_serv
GROUP BY cp.IDPROVEEDOR

";
//echo $sql;
$result1 = $con->query($sql);

header('content-type: text/xml');
$output = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
$output .= "<data>";
while ($reg1 = $result1->fetch_object()){
	//  obtengo los servicios del proveedor	 
	$sql="SELECT
            cs.IDSERVICIO,
	        SUBSTRING(cs.DESCRIPCION,INSTR(cs.DESCRIPCION,'-')+2 ) DESCRIPCION
          FROM 
            $con->catalogo.catalogo_proveedor_servicio cps
            LEFT JOIN $con->catalogo.catalogo_servicio cs ON cs.IDSERVICIO = cps.IDSERVICIO
          WHERE 
            cps.IDPROVEEDOR='$reg1->IDPROVEEDOR'";
    $result2=$con->query($sql);
    $servicios= null;
    while($reg2=$result2->fetch_object()){
        $servicios[]=$reg2->DESCRIPCION;
    }
    
    // obtengo los telefonos  del proveedor 
    $sql="SELECT 
    		CONCAT(CODIGOAREA,NUMEROTELEFONO) TELEFONO 
		  FROM 
			$con->catalogo.catalogo_proveedor_telefono 
		  WHERE 
			IDPROVEEDOR ='$reg1->IDPROVEEDOR'
			AND NUMEROTELEFONO!=''
    ";
 	$result2=$con->query($sql);
    $telefonos= null;
    while($reg2=$result2->fetch_object()){
        $telefonos[]=$reg2->TELEFONO;
    }
    $output .= "<proveedor>";
    $output .= "<idproveedor>".$reg1->IDPROVEEDOR."</idproveedor>";
    $output .= "<nombre>".$reg1->NOMBRECOMERCIAL."</nombre>";
    $output .= "<latitud>".$reg1->LATITUD."</latitud>";
    $output .= "<longitud>".$reg1->LONGITUD."</longitud>";
    $output .= "<direccion>".$reg1->DIRECCION."</direccion>";
    $output .= "<servicios>".implode(",\n ",$servicios)."</servicios>";
    $output .= "<telefonos>".implode(",\n ",$telefonos)."</telefonos>";
    $output .= "<color>".$reg1->COLOR."</color>";
    $output .= "</proveedor>";
}
$output .= "</data>";   
echo $output;
?>