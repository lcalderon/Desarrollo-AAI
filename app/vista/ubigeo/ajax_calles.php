<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

if (strlen($_GET[calle]))
{
	if ($_GET[cveentidad7]!='') $ext =" AND CVEENTIDAD7 ='$_GET[cveentidad7]'";
	if ($_GET[cveentidad6]!='') $ext.=" AND CVEENTIDAD6 ='$_GET[cveentidad6]'";
	if ($_GET[cveentidad5]!='') $ext.=" AND CVEENTIDAD5 ='$_GET[cveentidad5]'";
	if ($_GET[cveentidad4]!='') $ext.=" AND CVEENTIDAD4 ='$_GET[cveentidad4]'";
	if ($_GET[cveentidad3]!='') $ext.=" AND CVEENTIDAD3 ='$_GET[cveentidad3]'";
	if ($_GET[cveentidad2]!='') $ext.=" AND CVEENTIDAD2 ='$_GET[cveentidad2]'";
	if ($_GET[cveentidad1]!='') $ext.=" AND CVEENTIDAD1 ='$_GET[cveentidad1]'";
	
	if ($_GET[tv]!='') $ext.=" AND IDTIPOVIA ='$_GET[tv]'";
	
	$ext.='';

	$sql="
	select
		ID,substring(CALLE,1,30) CALLE, LATITUD,LONGITUD
	from 
		$con->catalogo.catalogo_guiacalle
	where 
	CALLE like '%$_GET[calle]%' $ext LIMIT $_GET[nreg] ;";
	
	$resul=$con->query($sql);

	$indice=0;
	?>
	<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" align="left">
 	 <thead>
	  <tr>
    	<th class="nosort">Puntos de interes</th>
      </tr>
  	</thead>
 	<tbody>
 	
  	<?while($reg=$resul->fetch_object()):?>
  	
  	<?$puntos[++$indice]= array($reg->CALLE,$reg->LATITUD,$reg->LONGITUD);?>
  		<tr>
  			<td>
  			<?=$indice.' '.$reg->CALLE?>
  			</td>
  			<td>
  			<img src='/imagenes/iconos/delete.gif' onclick="eliminar_punto('<?=$reg->ID?>')"/>
  			</td>
  		</tr>
  	<?endwhile;?>
	
<?	
	
	
}
?>