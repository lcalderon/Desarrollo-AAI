<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$result=$con->query("SELECT CURRENT_TIMESTAMP() FECHAHORA");
while ($reg = $result->fetch_object())	$fechahora = $reg->FECHAHORA;



switch($_POST[IDETAPA])
{
	case '1':
		{
			$asis_bitacora[FECHAMANUAL]=$fechahora;
			break;
		}
	case '2' : case '4':
		{
			$asis_bitacora[IDPROVEEDOR]=$_POST[IDPROVEEDOR];
			break;
		}
	case '7':
		{
			$asis_bitacora[IDASIGPROV]=$_POST[IDASIGPROV];
			break;
		}
}


$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];


$con->insert_reg("$con->temporal.asistencia_bitacora_etapa$_POST[IDETAPA]",$asis_bitacora);



$fecha=getdate($fechahora);

//echo 'ok';
?>