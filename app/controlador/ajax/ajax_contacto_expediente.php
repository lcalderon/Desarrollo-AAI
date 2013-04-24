<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_expediente.inc.php');

$idexpediente = $_GET[idexpediente];
$expediente  = new expediente($idexpediente);
$contacto = $expediente->datos_persona('CON');
foreach ($contacto as $indice =>$value){
$xml.=$value.',';
}
echo $xml;
?>