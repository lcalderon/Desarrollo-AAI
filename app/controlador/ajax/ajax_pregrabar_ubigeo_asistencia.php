<?
include_once('../../modelo/clase_mysqli.inc.php');

$con =  new DB_mysqli();
$idexpediente=$_POST[idexpediente];

$sql="
INSERT INTO $con->temporal.asistencia_lugardelevento (IDEXPEDIENTE,CVEPAIS,CVEENTIDAD1,CVEENTIDAD2,CVEENTIDAD3,CVEENTIDAD4,CVEENTIDAD5,CVEENTIDAD6, CVEENTIDAD7,
DESCRIPCION,DIRECCION,NUMERO,LATITUD,LONGITUD,REFERENCIA1,REFERENCIA2
) SELECT 
IDEXPEDIENTE,CVEPAIS,CVEENTIDAD1,CVEENTIDAD2,CVEENTIDAD3,CVEENTIDAD4,CVEENTIDAD5,CVEENTIDAD6, CVEENTIDAD7,
DESCRIPCION,DIRECCION,NUMERO,LATITUD,LONGITUD,REFERENCIA1,REFERENCIA2
FROM $con->temporal.expediente_ubigeo WHERE IDEXPEDIENTE='$idexpediente'
";
$con->query($sql);
$id_lugardelevento = $con->reg_id();



$sql="select * from $con->temporal.asistencia_lugardelevento where id='$id_lugardelevento' ";
$result=$con->query($sql);
while($reg=$result->fetch_object()){
$direccion = $reg->DIRECCION.' '. $reg->NUMERO;
}

echo $id_lugardelevento.'/'.$direccion;

?>