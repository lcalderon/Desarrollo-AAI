<?
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');


include_once('../../includes/head_prot_win.php');

$prov =  new proveedor();


$idasistencia= $_GET[idasistencia];
$asis = new asistencia();
$asis->carga_datos($idasistencia);


$ubigeo = $asis->lugardelevento;
$idfamilia= $asis->familia->idfamilia;


$prov_interno = (isset($_POST[INTERNO]))?$_POST[INTERNO]:'';
$prov_texto = (isset($_POST[TEXTOBUSQUEDA]))?$_POST[TEXTOBUSQUEDA]:'';
$activo=1;

$lista_prov = $prov->lista_proveedores($ubigeo,$idfamilia,$asis->servicio->idservicio,$prov_interno,$activo,$prov_texto);
$linea=0;
?>

<? if (count($lista_prov)):?>
<table id="table2" class="tinytable" width="100%" >
  <thead>
      <tr id='lin_<?=$linea?>' ">
	      <th width="2%" ><?=_('ID')?></th>
          <th width="20%"><?=_('NOMBRECOMERCIAL')?></th>
          <th width="3%"><?=_('INT/EXT')?></th>
          <th width="5%"><?=_('AMBITO')?></th>
          <th width="5%"><?=_('RANKING')?></th>
          
       </tr>
  </thead>       
    <?	
    $linea++;
    foreach ($lista_prov as $idproveedor=>$datos)
    {
    	$colorlinea = ($linea%2)? 'par':'impar';
    	$prov->carga_datos($idproveedor);
    	$servicio_prov = $con->uparray("SELECT S.IDSERVICIO,S.DESCRIPCION from $catalogo.catalogo_servicio S INNER JOIN $catalogo.catalogo_proveedor_servicio PS ON S.IDSERVICIO = PS.IDSERVICIO where PS.IDPROVEEDOR = $prov->idproveedor");

    	unset($telef_prov);
    	foreach ($prov->telefonos as $telefonos)
    	$telef_prov[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

    	$contacto = new contacto();
    	$sql_contacto="SELECT IDCONTACTO FROM $catalogo.catalogo_proveedor_contacto WHERE IDPROVEEDOR = $prov->idproveedor";
    	$exec_contacto=$con->query($sql_contacto);
    	if($rset_contacto=$exec_contacto->fetch_object()) $contacto->carga_datos($rset_contacto->IDCONTACTO);

    	unset($telef_cont);
    	foreach ($contacto->telefonos as $telefonos)
    	$telef_cont[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

		if($prov->interno ==1) { $interno='INT'; }else { $interno='EXT';} ?>
		
		<tr id='lin_<?=$linea?>' >
			<td>
				<?=$prov->idproveedor?>
			</td>
			<td>
				<?=$prov->nombrecomercial?>
			</td>
			<td align="center">
				<?=$interno?>
			</td>
			<td align="center">
				<?=$datos[AMBITO]?>
			</td>
			<td align="center">
				<?=$lista_prov[$idproveedor][RANKING]?>%
			</td>
			
		</tr>
	<?$linea++;?>	
	<? } ?>

</table>
<?else :?>
		<span style="align:center"><b><?=_('NO HAY PROVEEDORES EN ESTA BUSQUEDA')?></b></span>
<?endif;?>



