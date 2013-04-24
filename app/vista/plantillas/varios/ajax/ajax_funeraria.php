<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
//echo $reg[IDASISTENCIA];
//echo $_POST[SUBSERVICIO];


	
	$reg[OTROS]=$_POST[OTROS];
	$reg[MOTIVO]=$_POST[MOTIVO];
	$reg[IDDESTINO]=$_POST[M_IDDESTINO];
	if ($_POST[M_IDDESTINO]!=''){
	
	$asis_varios_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_varios_funeraria_ubigeo',$asis_varios_destino," WHERE ID ='$_POST[M_IDDESTINO]'");	
	
	}
	$reg[FECHADECESO]=$_POST[FECHADECESO].' '.$_POST[cbhoradeceso].':'.$_POST[cbminutodeceso].':00';
	$reg[FECHAHORACEREMONIA]=$_POST[FECHACEREMONIA].' '.$_POST[cbhoraceremonia].':'.$_POST[cbminutoceremonia].':00';
	
	

/*
echo $reg[IDASISTENCIA];
echo $reg[SUBSERVICIO];
echo $reg[IDRETIROESTABLECIMIENTO];
echo $reg[PERSONAENTREGAREMITE];
echo $reg[IDDESTINO];
echo $reg[PERSONARECIBE];
echo $reg[OTROS];
*/
//echo 'asis'.$reg[IDASISTENCIA];
if ($con->exist($con->temporal.'.asistencia_varios_funeraria','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_varios_funeraria',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_varios_funeraria',$reg);	
}

//print_r($_POST);

?>