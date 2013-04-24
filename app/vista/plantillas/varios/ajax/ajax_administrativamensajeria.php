<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[SUBSERVICIO] = $_POST[SUBSERVICIO];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
//echo $reg[IDASISTENCIA];
//echo $_POST[SUBSERVICIO];

switch($_POST[SUBSERVICIO])
{
    case 'EDOC':
	$reg[IDRETIROESTABLECIMIENTO]=$_POST[D_IDRETIRO];
	
	if ($_POST[D_IDRETIRO]!=''){
	
	$asis_varios_retiro[IDASISTENCIA]=$_GET[idasistencia];
	//echo $asis_varios_retiro[IDASISTENCIA];
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis_varios_retiro," WHERE ID ='$_POST[D_IDRETIRO]'");	
	
	}

	$reg[PERSONAENTREGAREMITE]= $_POST[D_PERSONAENTREGA];
	
	$reg[IDDESTINO]=$_POST[D_IDDESTINO];

	if ($_POST[D_IDDESTINO]!=''){
	
	$asis_varios_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis_varios_destino," WHERE ID ='$_POST[D_IDDESTINO]'");	
	
	}

	$reg[PERSONARECIBE]= $_POST[D_PERSONARECIBE];
	$reg[OTROS]=$_POST[D_OTROS];break;
    case 'COVA':
	$reg[IDRETIROESTABLECIMIENTO]=$_POST[C_IDRETIRO];
	if ($_POST[C_IDRETIRO]!=''){
	
	$asis_varios_retiro[IDASISTENCIA]=$_GET[idasistencia];
	//echo $asis_varios_retiro[IDASISTENCIA];
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis_varios_retiro," WHERE ID ='$_POST[C_IDRETIRO]'");	
	
	}

	$reg[IDDESTINO]=$_POST[C_IDDESTINO];
	if ($_POST[C_IDDESTINO]!=''){
	
	$asis_varios_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis_varios_destino," WHERE ID ='$_POST[C_IDDESTINO]'");	
	
	}
	$reg[DESCRIPCIONCOMPRA]= $_POST[C_DESCRIPCION];
	$reg[PERSONARECIBE]= $_POST[C_PERSONARECIBE];
	$reg[OTROS]=$_POST[C_OTROS];break;
    case 'MERE':
	$reg[PERSONAENTREGAREMITE]= $_POST[M_REMITENTE];
	$reg[PERSONADESTINATARIO]= $_POST[M_DESTINATARIO];
	$reg[OTROS]=$_POST[M_OTROS];
	$reg[IDDESTINO]=$_POST[M_IDDESTINO];
	if ($_POST[M_IDDESTINO]!=''){
	
	$asis_varios_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeriacadeteria_ubigeo',$asis_varios_destino," WHERE ID ='$_POST[M_IDDESTINO]'");	
	
	}
	$reg[FECHAHORAENTREGA]=$_POST[FECHACOMPRA].' '.$_POST[cbhoracompra].':'.$_POST[cbminutocompra].':00';
	$reg[CONCEPTOENTREGA]=$_POST[M_CONCEPTOENTREGA];
	$reg[PERSONARECIBE]= $_POST[M_PERSONARECIBE];
    break;
}

echo $reg[IDASISTENCIA];
echo $reg[SUBSERVICIO];
echo $reg[IDRETIROESTABLECIMIENTO];
echo $reg[PERSONAENTREGAREMITE];
echo $reg[IDDESTINO];
echo $reg[PERSONARECIBE];
echo $reg[OTROS];

//echo 'asis'.$reg[IDASISTENCIA];
if ($con->exist($con->temporal.'.asistencia_varios_administrativamensajeria','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_varios_administrativamensajeria',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_varios_administrativamensajeria',$reg);	
}

//print_r($_POST);

?>