<?
require_once('../../../librerias/phpagi-1.24/phpagi-asmanager.php');
$prefijo = $_GET[prefijo];
$numero = $prefijo.$_GET[num];
$extension = $_GET[ext];

$asm = new AGI_AsteriskManager();
if ($asm->connect($server="192.168.0.188",$username="admin",$secret="swlagiu5"))
{
	
	/*
	verifica el protocolo de la extension
	*/
/*
	$peer = $asm->command("sip show peer $extension");
	$data = array();
	foreach(explode("\n", $peer['data']) as $line)
	{
		$a = strpos('z'.$line, ':') - 1;
		if($a >= 0) $data[trim(substr($line, 0, $a))] = trim(substr($line, $a + 1));
	}
	
	if (!isset($data['* Name'])) $protocolo= "IAX2";
	else $protocolo="SIP";
	*/	
	/*	realiza la llamada	*/
	$call = $asm->send_request('Redirect',
	array('Channel'=>"SIP/5004",  
	'ExtraChannel'=>"",  
	'Exten'=>"5004",
	'Context'=>'default',
	'Priority'=>1
	)
	);   

	$asm->disconnect();
}


?>

