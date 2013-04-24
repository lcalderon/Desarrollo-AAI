<?php 
include_once "../../modelo/clase_mysqli.inc.php";

$con = new DB_mysqli;
$sql = "select IDIMAGEN from $con->temporal.asistencia_imagenes where IDASISTENCIA ='$_GET[idasistencia]' ";

$result = $con->query($sql);
?>

<html>
<head>


</head>

<body>
<h1>Imagenes de la asistencia</h1>
<div id='formulario'>
	<form action="carga.php" method="POST" enctype="multipart/form-data"  >
		<fieldset>
		<input type='hidden' name='IDASISTENCIA' value="<?=$_GET['idasistencia']?>">
		<input name="foto"  type="file" size="30" maxlength="" />
		<input type='submit' value='Cargar'>
		</fieldset>
	</form>
</div>

<div id='imagenes'>

<table>
<tr>
<? while ($reg = $result->fetch_object()):?>
	<td><a href='imagen.php?IDIMAGEN=<?=$reg->IDIMAGEN?>' target='_blank'><img src='imagen.php?IDIMAGEN=<?=$reg->IDIMAGEN?>' width='100px' height='100px'></a></td>
	
<? endwhile;?>
</tr>
</table>

</div>
</body>
</html>
