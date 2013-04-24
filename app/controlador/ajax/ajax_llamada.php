<?

//require_once('../../../librerias/phpagi-1.24/phpagi-asmanager.php');
//require_once('../../../librerias/phpagi-1.24/phpagi.php');
require_once('../../modelo/clase_mysqli.inc.php');
require_once('../../modelo/functions.php');



/* DATOS QUE SE PASAN COMO PARAMETROS */


$con = new DB_mysqli();
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');
$server=$con->lee_parametro('IPSERVIDOR_ASTERISK');
$username= $con->lee_parametro('USUARIO_MANAGER');
$secret = $con->lee_parametro('USUARIO_PASSWORD_MANAGER');
$numero = trim($prefijo.$_GET[num]);
$idetapa=$_GET[idetapa];
$idasistencia=$_GET[idasistencia];
$extension = $_GET[ext];

//$asm = new AGI_AsteriskManager();
/*
if ($asm->connect($server,$username,$secret))
{
	
	$peer = $asm->command("sip show peer $extension");
	$data = array();
	foreach(explode("\n", $peer['data']) as $line)
	{
		$a = strpos('z'.$line, ':') - 1;
		if($a >= 0) $data[trim(substr($line, 0, $a))] = trim(substr($line, $a + 1));
	}

	if (!isset($data['* Name'])) $protocolo= "IAX2";
	else 
*/	
	$protocolo="SIP";




	


//}

$channel=$protocolo.'/'.$extension;
$timeout = 5;

$socket2 = @fsockopen($server,'5038',$errno,$errstr,$timeout);

originateCall($socket2,$username,$secret,$channel,$numero,'catperu',$idetapa,$idasistencia,$extension);
//echo $username.' '.$channel.' '.$numero.' '.$extension.' '.$secret.' '.$server;
fclose($socket2);

//$asm->disconnect();


?>