<html>
<head>
<title>Disponibilidad Afiliado</title>
<?
//I don't know the author of the Monthcalendar, but ...  Thanks for the Code !! you can find it in http://foros.cristalab.com/formato-de-fecha-en-calendario-t76780
$anoInicial = '2000';
$anoFinal = date('Y')+3;
$funcionTratarFecha = 'document.location = "?dia="+dia+"&mes="+mes+"&ano="+ano;';
?>
<script type="text/javascript" src="../../../../librerias/jquery-flipv/js/jquery.js"></script>
<script type="text/javascript" src="../../../../librerias/jquery-flipv/js/cvi_text_lib.js"></script>
<script type="text/javascript" src="../../../../librerias/jquery-flipv/js/jquery.flipv.js"></script>
<script>
function tratarFecha(dia,mes,ano){
  <?=$funcionTratarFecha?>
}
</script>
<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<style>
.m1 {
   font-family:MS Sans Serif;
   font-size:8pt
}
a {
   text-decoration:none;
   color:#000000;
}

td {
font-size:0.6em;
}
</style>

</head>

<body>
<!--CALENDARIO-->
<!--I don't now the author of the Monthcalendar, but ...  Thanks for the Code !! you can find it in http://foros.cristalab.com/formato-de-fecha-en-calendario-t76780-->
<table width='99%'><tr><td>
<form>
<table border="0" cellpadding="5" cellspacing="0" bgcolor="#D4D0C8">
  <tr>
    <td width="100%">&nbsp;&nbsp;&nbsp;
<?
$idasistencia = $_GET['idasistencia'];

$fecha = getdate(time());
if(isset($_GET["dia"]))$dia = $_GET["dia"];
else $dia = $fecha['mday'];
if(isset($_GET["mes"]))$mes = $_GET["mes"];
else $mes = $fecha['mon'];
if(isset($_GET["ano"]))$ano = $_GET["ano"];
else $ano = $fecha['year'];
$fecha = mktime(0,0,0,$mes,$dia,$ano);
$fechaInicioMes = mktime(0,0,0,$mes,1,$ano);
$fechaInicioMes = date("w",$fechaInicioMes);
?>
    <select size="1" name="mes" class="m1" onChange="document.location = '?dia=<?=$dia?>&mes=' + document.forms[0].mes.value + '&ano=<?=$ano?>&idasistencia=<?=$idasistencia?>';">
<?
$meses = Array ('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
for($i = 1; $i <= 12; $i++){
  echo '      <option ';
  if($mes == $i)echo 'selected ';
  echo 'value="'.$i.'">'.$meses[$i-1]."\n";
}
?>
    </select>&nbsp;&nbsp;&nbsp;<select size="1" name="ano" class="m1" onChange="document.location = '?dia=<?=$dia?>&mes=<?=$mes?>&idasistencia=<?=$idasistencia?>&ano=' + document.forms[0].ano.value;">
<?
for ($i = $anoInicial; $i <= $anoFinal; $i++){
  echo '      <option ';
  if($ano == $i)echo 'selected ';
  echo 'value="'.$i.'">'.$i."\n";
}
?>
    </select>
    </td>
  </tr>
</table>
</form>
</td>
<!--CALENDARIO-->


<?php
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

	//echo $idasistencia;
	
	$con->select_db($con->temporal);
	 
	//variables POST
 
$idasistencia=$_GET['idasistencia'];
//$idasistencia = 20;
//echo $idasistencia;
	

 if($_POST['btnGrabar']=='GUARDAR DISPONIBILIDAD'){
$date = date("YmdHi");

$fecha = $_POST["programDay"];

$sql_verifica_disponibilidad="SELECT count(*) VARDISPO from asistencia_disponibilidad_afiliado WHERE IDASISTENCIA =$idasistencia";
//echo $sql_verifica_disponibilidad;
$exec_verifica_disponibilidad = $con->query($sql_verifica_disponibilidad);
if($rset_verifica_disponibilidad=$exec_verifica_disponibilidad->fetch_object()){
	$vardisponibilidad=$rset_verifica_disponibilidad->VARDISPO;
	//echo $vardisponibilidad;
}

if($vardisponibilidad==0){
	for($i=0;$i<count($fecha);$i++){
		//echo $fecha[$i];
		list($dias[])=split('&',$fecha[$i]);
		$hora = substr($dias[$i],0,2);
		$hora2 = substr($dias[$i],0,2)+1;
		$dia = substr($dias[$i],2,2);
		$mes = substr($dias[$i],4,2);
		$anio = substr($dias[$i],6,4);
		$fechahora = $anio.'-'.$mes.'-'.$dia.' '.$hora.':00:00';
		//echo $hora.' '.$dia.' '.$mes.' '.$anio.'<br>';
		$sql_insert_tmp = "INSERT INTO asistencia_disponibilidad_afiliado(IDASISTENCIA,FECHAHORA) values(".$idasistencia.",'".$fechahora."')";		
	
		$exec_insert_tmp = $con->query($sql_insert_tmp);	
	}
}
else{
	$sql_elimina_disponiblidad="DELETE FROM asistencia_disponibilidad_afiliado WHERE IDASISTENCIA = $idasistencia";
	//echo $sql_elimina_disponibilidad;
	$exec_elimina_disponibilidad =$con->query($sql_elimina_disponiblidad);
		for($i=0;$i<count($fecha);$i++){
		//echo $fecha[$i];
		list($dias[])=split('&',$fecha[$i]);
		$hora = substr($dias[$i],0,2);
		$hora2 = substr($dias[$i],0,2)+1;
		$dia = substr($dias[$i],2,2);
		$mes = substr($dias[$i],4,2);
		$anio = substr($dias[$i],6,4);
		$fechahora = $anio.'-'.$mes.'-'.$dia.' '.$hora.':00:00';
		//echo $hora.' '.$dia.' '.$mes.' '.$anio.'<br>';
		$sql_insert_tmp = "INSERT INTO asistencia_disponibilidad_afiliado(IDASISTENCIA,FECHAHORA) values(".$idasistencia.",'".$fechahora."')";		
	
		$exec_insert_tmp = $con->query($sql_insert_tmp);
		}
	
}	
	echo "<script language='javascript'>alert('Datos Grabados con Exito!!'); </script>";
	$rows["IDETAPA"]=5;
	//actualiza los datos
 	$resultado=$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
	echo "<script>";
	if(!$resultado)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";	 
    echo "</script>";
}	
	
if ($_GET["dia"] && $_GET["mes"] && $_GET["ano"]) {

$dia = $_GET["dia"];
$mes = $_GET["mes"];
$ano = $_GET["ano"];
} else {
$dia = date ("d");
$mes = date ("n");
$ano = date ("Y");
}
//echo $dia.' '.$mes.' '.$ano.' '.$idasistencia;
echo "<form method='post' name='f1'>";
echo "<td align='right'>";
?>
<p><input type='submit' name='btnGrabar' value="<?=_('GUARDAR DISPONIBILIDAD')?>"  /></p>
<?
echo "</td></tr><tr><td colspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//include the WeeklyCalClass and create the object !!
include ("CalendarioDisponibilidad.php");
$calendar = new EasyWeeklyCalClass ($dia, $mes, $ano);
echo $calendar->showCalendar ($idasistencia);

echo "</td></tr></table>";
echo "</form>";

?>
<script language="javascript">
function habilitachkhora(elemento){ 
	if(elemento<=9){
		check = '0'+elemento;
	}
	else{
		check = elemento;
	}
	//alert(check);
	if(document.getElementById(check).checked){	
		document.getElementById(check+'0').checked = true;  
		document.getElementById(check+'1').checked = true;  
		document.getElementById(check+'2').checked = true; 
		document.getElementById(check+'3').checked = true;  
		document.getElementById(check+'4').checked = true;  
		document.getElementById(check+'5').checked = true;  
		document.getElementById(check+'6').checked = true;  
	}else{
		document.getElementById(check+'0').checked = false;  
		document.getElementById(check+'1').checked = false;  
		document.getElementById(check+'2').checked = false; 
		document.getElementById(check+'3').checked = false;  
		document.getElementById(check+'4').checked = false;  
		document.getElementById(check+'5').checked = false;  
		document.getElementById(check+'6').checked = false;  
	}
}

function marcartodo(elemento){

	if(document.getElementById(elemento).checked){	
		for (i=0; i<=23; i++)
		{
			if(i<=9){
				check = '0'+i;
			}
			else{
				check = ''+i;
			}
			//alert(check+elemento);
			document.getElementById(check+elemento).checked=true;
		}
	}
	else{
		for (i=0; i<=23; i++)
		{
			if(i<=9){
				check = '0'+i;
			}
			else{
				check = ''+i;
			}
			document.getElementById(check+elemento).checked=false;
		}

	}

}
	

</script>
</body>
</html>