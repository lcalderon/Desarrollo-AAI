<?
include_once('../../modelo/clase_mysqli.inc.php'); 
include_once('../../modelo/clase_proveedor.inc.php');

$proveedor = new proveedor(1);

foreach($proveedor->telefonos as $telefono)
  foreach($telefono as $indice=>$valor){
  	echo $indice .'  '.$valor."<br>";
    }
    


?>