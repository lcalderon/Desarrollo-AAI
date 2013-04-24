<?
if (isset($_GET[arrcondicionservicio]))
{
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_ubigeo.inc.php');
	include_once('../../modelo/clase_moneda.inc.php');
	include_once('../../modelo/clase_plantilla.inc.php');
	include_once('../../modelo/clase_familia.inc.php');
	include_once('../../modelo/clase_servicio.inc.php');
	include_once('../../modelo/clase_cuenta.inc.php');
	include_once('../../modelo/clase_programa_servicio.inc.php');
	include_once('../../modelo/clase_programa.inc.php');
	$arrcondicionservicio = $_GET[arrcondicionservicio];
	$idprograma = $_GET[idprograma];
	$idfamilia = $_GET[idfamilia];

}

if ($arrcondicionservicio=='COB')
{
	$prog = new programa();
	$prog->carga_datos($idprograma);
	$servicios = $prog->carga_servicios($idprograma,$idfamilia);
}
else
{
	$fam1= new familia();
	$fam1->carga_servicios($idfamilia);
	$servicios = $fam1->servicios;
}
$cuenta= $exp->cuenta->idcuenta;
?>
<div style="height:<?=($idfamilia==12)?'50px':'200px'?>;overflow-y:auto; overflow-x:hidden;">
<form id='form_listado'  >
<table  id="table" class="tinytable">
			<thead>
				<tr>
					<th width="5%" class="nosort"><h3><?=_('PDF')?></h3></th>
					<th width="30%" ><h3><?=_('SERVICIO AA')?></h3></th>
					<? if ($arrcondicionservicio=='COB') : ?>
						<th width="30%" ><h3><?=_('DESCRIPCION')?></h3></th>
						<th width="10%" class="nosort" ><h3><?=_('LIMITE')?></h3></th>
						<th width="10%" class="nosort" ><h3><?=_('USADO')?></h3></th>
						<th width="10%" class="nosort" ><h3><?=_('DISP.')?></h3></th>
					<?endif;?>
					<th width="5%" class="nosort" ><h3><?=_('ASIGNAR')?></h3></th>
				</tr>
			</thead>
			<tbody >
			
			<?	$linea=0;
			foreach ($servicios as $idprogramaservicio => $idservicio)
			{
				$serv= new servicio();
				$serv->carga_datos($idservicio);
			
				$url_plantilla =strtolower($serv->familia->descripcion).'/'.$serv->plantilla->vista;
				$nom_plantilla = substr(strtolower($serv->plantilla->vista),0,-4);
				$nombre_comercial_servicio=$serv->descripcion;
				
				echo "<tr>";
				echo "<td align='center'><img src='../../../imagenes/iconos/pdf.gif' alt='15' width='15' style='cursor: pointer;' title='"._('CONTRATO')."' onclick=ver_detalle('$idprograma')></img></td>";
				echo "<td>$nombre_comercial_servicio </td>";
				if ($arrcondicionservicio=='COB') {
					$prog_serv=new programa_servicio();
					$prog_serv->carga_datos($idprogramaservicio);
					$nombre_comercial_servicio=$prog_serv->etiqueta;
					echo "<td>$nombre_comercial_servicio </td>";
					echo "<td align='center'>$prog_serv->eventos</td>";
					echo "<td align='center'></td>";  //$serv_utilizados
					echo "<td align='center'></td>"; //".($prog_serv->eventos-$serv_utilizados)."<
				}
				echo "<td align='center'><input type='button' value='Asignar' class='normal' onclick=plantilla('$url_plantilla?idservicio=$serv->idservicio&idprograma=$idprograma&idcuenta=$cuenta','$nom_plantilla','$serv->idservicio','$prog_serv->idprogramaservicio','$serv->concluciontemprana','$serv->conclucionconproveedor')></td>";
				echo "</tr>";
				$linea++;
			}
			?>
		</tbody>
</table>
</form>
</div>

<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">


var sorter = new TINY.table.sorter('sorter','table',{
	headclass:'head',
	ascclass:'asc',
	descclass:'desc',
	evenclass:'evenrow',
	oddclass:'oddrow',
	evenselclass:'evenselected',
	oddselclass:'oddselected',
	paginate:false,
	size:10,
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	sortcolumn:1,
	sortdir:1,
	init:true
});
function ver_detalle(idprograma){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Detalle")?>',
			width: 800 ,
			heigth: 500 ,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "contrato.php?idprograma="+idprograma
		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
	return;
}


</script>
