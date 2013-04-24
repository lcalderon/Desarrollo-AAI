<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$db =$con->catalogo;
$con->select_db($db);

if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
	
}


$sql="
select u1.calle, u1.cuadra, u2.calle, u2.cuadra, 
	   u1.cveentidad1,u1.cveentidad2, u1.cveentidad3,u1.cveentidad4,u1.cveentidad5,u1.cveentidad6,u1.cveentidad7,
       u1.latitud, u1.longitud, u2.latitud, u2.longitud, 
       @vmedialat:=( u1.latitud + u2.latitud ) / 2 as medialat,
       @vmedialon:=( u1.longitud + u2.longitud ) / 2 as medialon,
       abs( u1.latitud - u2.latitud ) abslat, abs( u1.longitud - u2.longitud ) abslon,
       abs( ( abs( u1.latitud - u2.latitud ) + abs( u1.longitud - u2.longitud ) ) / 2 ) absmediatotal
      
from catalogo_guiacalle u1, catalogo_guiacalle u2
where u1.calle like '%$_GET[via1]%'
  and u2.calle like '%$_GET[via2]%'
order by absmediatotal asc
limit 1
";

$result=$con->query($sql);
while ($reg=$result->fetch_object()){
	 echo "$reg->medialat".'/'."$reg->medialon".'/'.
	 "$reg->cveentidad1".'/'.
	 "$reg->cveentidad2".'/'.
	 "$reg->cveentidad3".'/'.
	 "$reg->cveentidad4".'/'.
	 "$reg->cveentidad5".'/'.
	 "$reg->cveentidad6".'/'.
	 "$reg->cveentidad7";
}
   
 
 
?>