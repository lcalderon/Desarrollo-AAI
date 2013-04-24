<?
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');

include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../includes/arreglos.php');
include_once('../../includes/head_prot_win.php');

$prov =  new proveedor();

$prov_interno = (isset($_POST[INTERNO]))?$_POST[INTERNO]:'';
$prov_texto = (isset($_POST[TEXTOBUSQUEDA]))?$_POST[TEXTOBUSQUEDA]:'';
$activo=1;

$lista_prov = $prov->lista_proveedores($ubigeo,$idfamilia,$asis->servicio->idservicio,$prov_interno,$activo,$prov_texto);
$linea=0;
?>

<? $justificacion = justificacion('PROV');?>
<? if (count($lista_prov)):?>
<table id="table2" class="tinytable" width="100%" >
  <thead>
      <tr id='lin_<?=$linea?>' ">
	      <th width="2%" ><?=_('ID')?></th>
          <th width="20%"><?=_('NOMBRECOMERCIAL')?></th>
          <th width="3%"><?=_('INT')?></th>
          <th width="20%"><?=_('CONTACTO')?></th>
          <th width="20%"><?=_('TELEFONO_CONTACTO')?></th>
          <th width="20%"><?=_('TELEFONO_PROVEEDORES')?></th>
          <th width="5%"><?=_('AMB')?></th>
          <th width="5%"><?=_('RANK')?></th>
          <th width="5%"><?=_('ASIGNAR')?></th>
          <th width="5%"><?=_('SIGUIENTE')?></th>
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
    	$sql_contacto="SELECT IDCONTACTO FROM $catalogo.catalogo_proveedor_contacto WHERE IDPROVEEDOR = $prov->idproveedor  ORDER BY RESPONSABLE DESC";
    	$exec_contacto=$con->query($sql_contacto);
    	if($rset_contacto=$exec_contacto->fetch_object()) $contacto->carga_datos($rset_contacto->IDCONTACTO);

    	unset($telef_cont);
    	foreach ($contacto->telefonos as $telefonos)
    	$telef_cont[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

		if($prov->interno ==1) { $interno='INT'; }else { $interno='EXT';} ?>
		
		<tr id='lin_<?=$linea?>' style='display:none'>
			<td><?=$prov->idproveedor?></td>
			<td>
			<img src='/imagenes/iconos/info.png' align='absbottom' border='0' style='cursor: pointer;' onclick="edit_proveedor('<?=$prov->idproveedor?>');" />
			<?=$prov->nombrecomercial?>
			<div id="observ_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC;" >
				 <textarea cols='60' rows='8'><?=$prov->observaciones?></textarea>
			</div>
			<? if ($prov->observaciones!=''):?>
			   <img src='/imagenes/32x32/Tag.png' id='i_observ_<?=$linea?>' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('i_observ_<?=$linea?>','observ_<?=$linea?>');" / >			
			<?endif;?>
			
			</td>
			<td><?=$interno?></td>
			<td align='left'><?=$contacto->appaterno.' '.$contacto->apmaterno.' '.$contacto->nombre?></td>	
			<td align='right'>
			<? if (isset($telef_cont)):?>  
			
		  		<table id="telf_cont_<?=$linea?>" style="display:none;  background:#ECECEC ">
			  		<tr>
			  			<th><?=_('DDN')?></th>
			  			<th><?=_('LUGAR')?></th>
			  			<th><?=_('NUMERO')?></th>
			  			<th><?=_('TIPO')?></th>
				  		<th><?=_('EMPRESA')?></th>
				  		<th><?=_('COMENTARIO')?></th>
			  		</tr>
			  		<? foreach ($contacto->telefonos as $infotel): ?>
		  			<tr>
		  				<td><?=$infotel[CODIGOAREA]?></td>
		  				<td><?=$infotel[NOMBRECODIGOAREA]?></td>
			  			<td><?=$infotel[NUMEROTELEFONO]?></td>
			  			<td><?=$infotel[NOMBRETIPOTELEFONO]?></td>
			  			<td><?=$infotel[NOMBRETSP]?></td>
			  			<td><textarea><?=$infotel[TELF_COMENTARIO]?></textarea></td>
		  			</tr>
			  		<? endforeach;?>	
			  	</table>			
				<?$prov->cmbselect_ar('TELEFONO_CONT',$telef_cont,'','',"id=telefono_cont_$linea",'')?>
				<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telefono_cont_'+<?=$linea?>))>
				<img src='/imagenes/32x32/Tag.png' id='cont_<?=$linea?>' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('cont_<?=$linea?>','telf_cont_<?=$linea?>');" / >
				<? endif; ?> 
			
			</td>
			<td align='right'>
		  		<table id="telf_prov_<?=$linea?>" style="display:none; 	background:#ECECEC ">
			  		<tr>
			  			<th><?=_('DDN')?></th>
			  			<th><?=_('LUGAR')?></th>
			  			<th><?=_('NUMERO')?></th>
			  			<th><?=_('TIPO')?></th>
			  			<th><?=_('EMPRESA')?></th>
			  			<th><?=_('COMENTARIO')?></th>
			  		</tr>
			  		<? foreach ($prov->telefonos as $infotel): ?>
			  		<tr>
			  			<td><?=$infotel[CODIGOAREA]?></td>
			  			<td><?=$infotel[NOMBRECODIGOAREA]?></td>
				  		<td><?=$infotel[NUMEROTELEFONO]?></td>
				  		<td><?=$infotel[NOMBRETIPOTELEFONO]?></td>
				  		<td><?=$infotel[NOMBRETSP]?></td>
				  		<td><textarea><?=$infotel[TELF_COMENTARIO]?></textarea></td>
			  		</tr>
			  		<? endforeach;?>	
			  	</table>			
  				<? $prov->cmbselect_ar('TELEFONO_PROV',$telef_prov,'','',"id=telefono_prov_$linea",'')?>
				<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telefono_prov_'+<?=$linea?>))> 
				<img src='/imagenes/32x32/Tag.png' id='prov_<?=$linea?>'  alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('prov_<?=$linea?>','telf_prov_<?=$linea?>');" / >				
			</td>		
			<td><?=$datos[AMBITO]?></td>
			<td align="right"><?=$lista_prov[$idproveedor][RANKING]?>%</td>
			<td align="center"><input type='button' id='btnAsignar' <?=$disabled?>  value="<?=_('ASIGNAR PROV').' >>'?>" onClick="Asignar(<?=$prov->idproveedor?>,'<?=$reg->NOMBRECOMERCIAL?>',$F('tipocostos'))" class='guardar'><input type='hidden' name='hid_prov' id='hid_prov' value='<?=$prov->idproveedor?>'><input type='hidden' name='hid_idasistencia' value='<?=$idasistencia?>'><input type='hidden' name='hid_servicio' value='<?=$idservicio?>'></td>
			<td align='center'><input type='button' id='btnSiguiente' value="<?=_('SIGUIENTE').' >>'?>"  title="<?=_('JUSTIFICACION')?>" align='absbottom' class='normal' onclick="just('<?=$prov->idproveedor?>','AUTO','<?=$linea?>');"/></td>
<!--			<td align='center'><img src='/imagenes/32x32/Paste.png' title='JUSTIFICACION' align='absbottom' border='0' alt="16px" width="16px" style='cursor: pointer;' onclick="just('<?=$prov->idproveedor?>','AUTO','<?=$linea?>');"/></td>-->
		</tr>
	<?$linea++;?>	
	<? } ?>

</table>
<?else :?>
		<span style="align:center"><b><?=_('NO HAY PROVEEDORES EN ESTA BUSQUEDA')?></b></span>
<?endif;?>
