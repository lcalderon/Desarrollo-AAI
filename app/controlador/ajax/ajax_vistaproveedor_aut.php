<?

if (isset($_POST[IDPROVEEDOR]))
{
	include_once('../../modelo/clase_lang.inc.php');
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_ubigeo.inc.php');
	include_once('../../modelo/clase_moneda.inc.php');

	include_once('../../modelo/clase_contacto.inc.php');
	include_once('../../modelo/clase_poligono.inc.php');
	include_once('../../modelo/clase_circulo.inc.php');
	include_once('../../modelo/clase_proveedor.inc.php');
	$idproveedor = $_POST[IDPROVEEDOR];
	$prov = new proveedor();
	
}

?>
<table  cellpadding="0" cellspacing="0" border="0" id="table1" class="tinytable" width='99%'>
<thead>
      <tr>
	      <th><h3><?=_('ID')?></h3></th>
          <th><h3><?=_('NOMBRECOMERCIAL')?></h3></th>
	 	  <th><h3><?=_('INTERNO')?></h3></th>
          <th><h3><?=_('CONTACTO')?></h3></th>
          <th><h3><?=_('TELEF CONTACTO')?></h3></th>
          <th><h3><?=_('TELEF PROVEEDORES')?></h3></th>
          <th><h3><?=_('AMBITO')?></h3></th>
          <th><h3><?=_('ASIGNAR')?></h3></th>
          <th><h3><?=_('JUSTIFICACION')?></h3></th>
       </tr>
</thead>
<tbody >
<?

$colorlinea = ($linea%2)? 'par':'impar';
$prov->carga_datos($idproveedor);
$servicio_prov = $con->uparray("SELECT S.IDSERVICIO,S.DESCRIPCION from $catalogo.catalogo_servicio S INNER JOIN $con->catalogo.catalogo_proveedor_servicio PS ON S.IDSERVICIO = PS.IDSERVICIO where PS.IDPROVEEDOR = $prov->idproveedor");

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
		if($prov->interno ==1) { $interno='INTERNO'; }else { $interno='EXTERNO';} ?>
		<tr class='<?=$colorlinea?>'>
			<td width="3%"><?=$prov->idproveedor?></td>
			<td width="25%">
			<div id="observ_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC;" >
				 <textarea cols='60' rows='8'><?=$prov->observaciones?></textarea>
			</div>
			<img src='/imagenes/32x32/Tag.png' id='i_observ_<?=$linea?>' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('i_observ_<?=$linea?>','observ_<?=$linea?>');" / >			
			<?=$prov->nombrecomercial?>
			</td>
			<td width="5%"><?=$interno?></td>
			<td width="25%" align='left'><?=$contacto->appaterno.' '.$contacto->apmaterno.' '.$contacto->nombre?></td>	
			<td width="20%" align='right'>
			<? if (isset($telef_cont)):?>  
				<div id="telf_cont_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC ">
			  		<table>
			  		<th><?=_('DDN')?></th>
			  		<th><?=_('LUGAR')?></th>
			  		<th><?=_('NUMERO')?></th>
			  		<th><?=_('TIPO')?></th>
			  		<th><?=_('EMPRESA')?></th>
			  		<th><?=_('COMENTARIO')?></th>
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
  				</div>
					<?$prov->cmbselect_ar('TELEFONO_CONT',$telef_cont,'','',"id=telefono_cont_$linea",'')?>
					<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telefono_cont_'+<?=$linea?>))>
					<img src='/imagenes/32x32/Tag.png' id='cont_<?=$linea?>' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('cont_<?=$linea?>','telf_cont_<?=$linea?>');" / >
				<? endif; ?> 
			</td>
			<td width="20%" align='right'>
			  <div id="telf_prov_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC ">
			  		<table>
			  		<th><?=_('DDN')?></th>
			  		<th><?=_('LUGAR')?></th>
			  		<th><?=_('NUMERO')?></th>
			  		<th><?=_('TIPO')?></th>
			  		<th><?=_('EMPRESA')?></th>
			  		<th><?=_('COMENTARIO')?></th>
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
  				</div>
					<? $prov->cmbselect_ar('TELEFONO_PROV',$telef_prov,'','',"id=telefono_prov_$linea",'')?>
					<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telefono_prov_'+<?=$linea?>))> 
					<img src='/imagenes/32x32/Tag.png' id='prov_<?=$linea?>'  alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('prov_<?=$linea?>','telf_prov_<?=$linea?>');" / >				
					
			</td>		
			<td width="5%"><?=$datos[AMBITO]?></td>
			<td width="10%" align="center"><input type='button' id='btnAsignar' <?=$disabled?>  value="<?=_('ASIGNAR').' >>'?>" onClick="Asignar(<?=$prov->idproveedor?>,'<?=$reg->NOMBRECOMERCIAL?>','<?=($datos[AMBITO]=='LOC')?'L':'F'?>')" class='guardar'><input type='hidden' name='hid_prov' id='hid_prov' value='<?=$prov->idproveedor?>'><input type='hidden' name='hid_idasistencia' value='<?=$idasistencia?>'><input type='hidden' name='hid_servicio' value='<?=$idservicio?>'></td>
			<td align='center' ><input type="button" id="btnAgregar" name="btnAgregar" <?=$disabledgral?>  value="<?=('SGTE PROVEEDOR')?>"  class='normal'  onclick="siguiente();" /></td>
			
		</tr>
</tbody>
</table>
	