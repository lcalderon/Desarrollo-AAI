
<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_monitortarea.inc.php');
include_once('../includes/head_prot.php');

$idusuario = $_SESSION[user];

$idusuarios = json_decode(str_replace("\\", "", $_POST[IDUSUARIOS]));
$idusuarios = (count($idusuarios))?$idusuarios:array('0'=>"$idusuario");

function conversor($s){
	$simb = ($s>0)?'':'-';
	$s=abs($s);
	$d = intval($s/86400);
	$s -= $d*86400;
	$h = intval($s/3600);
	$s -= $h*3600;
	$m = intval($s/60);
	$s -= $m*60;

	if ($d>0) $dif= "$d d $h h $m m";
	elseif ($h>0) $dif= "$h h $m m";
	else $dif="$simb $m m";
	return $dif;
}

$extension = $_SESSION[extension];
$fechaactual =  date('Y-m-d H:i:s');

$con = new DB_mysqli();
$minutos_verde = $con->lee_parametro('TIEMPO_VERDE_MONITOR');
$minutos_ambar = $con->lee_parametro('TIEMPO_AMBAR_MONITOR');
$prefijo       = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

$tareas = new monitortarea();
$result = $tareas->listar_tareas($idusuarios,'PENDIENTE',$minutos_verde);
$linea=0;
?>
<table  class="tinytable" width="100%" >
<thead>
	<tr>
		<th width="5%"><h3></h3></th>
		<th width="13%"><h3><?=_('TIEMPO')?></h3></th>
		<th width="20%"><h3><?=_('TAREA')?></h3></th>
		<th width="20%"><h3><?=_('COORDINADOR')?></h3></th>
		<th width="7%"><h3><?=_('ASIST')?></h3></th>
		<th width="25%"><h3><?=_('SERVICIO')?></h3></th>
		<th width="15%"><h3><?=_('AFILIADO')?></h3></th>
		<th width="15%"><h3><?=_('PROV')?></h3></th>
	</tr>
</thead>
<tbody>

<?while($reg = $result->fetch_object()):?>
<?
$tareas->tiempos_tarea($reg->IDTAREA);
$tolerancia=$tareas->enrojo;


$color="#008200";   // verde
if (($reg->DIFERENCIA < ($minutos_ambar*60)) && ($reg->DIFERENCIA>0)) $color='#FECA16';  //ambar
if ($reg->DIFERENCIA<0) $color="#FF0000";  //rojo


$cadena =($reg->IDUSUARIORESPONSABLE == $idusuario)?" onclick=tarea_invisible('$reg->ID')":" style='opacity:0.4'";
?>
	<tr class="<?=($linea%2)?'oddrow':''?>" style='color:<?=$color?>'>
		<td align="center" ><img src='/imagenes/iconos/eliminar.gif'  alt='10px' width="10px" title='<?=_('BORRAR TAREA')?>' <?=$cadena?> ></td>
		<td align="right"><?=conversor($reg->DIFERENCIA)?></td>
		<td onclick="window.open('/app/vista/plantillas/etapa<?=($reg->ETAPA <= $reg->IDETAPA)? $reg->ETAPA:$reg->IDETAPA;?>.php?idasistencia=<?=$reg->IDASISTENCIA?>','ASISTENCIA_<?=$reg->IDASISTENCIA?>')"><?=$reg->NOMBRETAREA?></td>
		<td align="center"><?=$reg->IDUSUARIORESPONSABLE?></td>
		<td><?=$reg->IDASISTENCIA?></td>
		<td><?=$reg->NOMBRESERVICIO?></td>
		<td><?=$reg->NUMEROTELEFONOCONTACTO?><img src='/imagenes/iconos/telefono.jpg' alt="15px" width="15px" title="<?=_('LLAMAR AL AFILIADO')?>" onclick='llamada("<?=$reg->NUMEROTELEFONOCONTACTO?>")'> </td>
		<td><?if ($reg->NUMEROTELEFONOPROVEEDORCONTACTO!='')
		{
			echo $reg->NUMEROTELEFONOPROVEEDORCONTACTO;
			echo "<img src='/imagenes/iconos/telefono.jpg' alt='15px' width='15px' title='"._('LLAMAR AL PROVEEDOR')."' onclick=llamada('$reg->NUMEROTELEFONOPROVEEDORCONTACTO')>";
		}?>
		</td>
	</tr>
<? $linea++;?>

<?endwhile;?>
</tbody>

</table> 