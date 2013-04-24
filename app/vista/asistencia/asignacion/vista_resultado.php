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



if (isset($_POST))
{
	$idservicio = ($_POST[SERVICIO]=='')?0:$_POST[SERVICIO];

	if($_POST[CVEENTIDAD1]==''){ $cveentidad1=0; }else{ $cveentidad1=$_POST[CVEENTIDAD1]; }
	if($_POST[CVEENTIDAD2]==''){ $cveentidad2=0; }else{ $cveentidad2=$_POST[CVEENTIDAD2]; }
	if($_POST[CVEENTIDAD3]==''){ $cveentidad3=0; }else{ $cveentidad3=$_POST[CVEENTIDAD3]; }
	if($_POST[CVEENTIDAD4]==''){ $cveentidad4=0; }else{ $cveentidad4=$_POST[CVEENTIDAD4]; }
	if($_POST[CVEENTIDAD5]==''){ $cveentidad5=0; }else{ $cveentidad5=$_POST[CVEENTIDAD5]; }
	if($_POST[CVEENTIDAD6]==''){ $cveentidad6=0; }else{ $cveentidad6=$_POST[CVEENTIDAD6]; }
	if($_POST[CVEENTIDAD7]==''){ $cveentidad7=0; }else{ $cveentidad7=$_POST[CVEENTIDAD7]; }

	$prov =  new proveedor();
	$ubicacion->cvepais = $_POST[CVEPAIS];
	$ubicacion->cveentidad1=$cveentidad1;
	$ubicacion->cveentidad2=$cveentidad2;
	$ubicacion->cveentidad3=$cveentidad3;
	$ubicacion->cveentidad4=$cveentidad4;
	$ubicacion->cveentidad5=$cveentidad5;
	$ubicacion->cveentidad6=$cveentidad6;
	$ubicacion->cveentidad7=$cveentidad7;
}
else
{
	$prov =  new proveedor();

	$entfed1_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD1');
	$entfed2_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD2');
	$entfed3_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD3');
	$entfed4_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD4');
	$entfed5_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD5');
	$entfed6_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD6');
	$entfed7_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD7');

	$ubicacion->cvepais = $con->lee_parametro('IDPAIS');
	$ubicacion->cveentidad1=$entfed1_default;
	$ubicacion->cveentidad2=$entfed2_default;
	$ubicacion->cveentidad3=$entfed3_default;
	$ubicacion->cveentidad4=$entfed4_default;
	$ubicacion->cveentidad5=$entfed5_default;
	$ubicacion->cveentidad6=$entfed6_default;
	$ubicacion->cveentidad7=$entfed7_default;
}

$prov_interno = (isset($_POST[INTERNO]))?$_POST[INTERNO]:'';
$prov_texto = (isset($_POST[TEXTOBUSQUEDA]))?$_POST[TEXTOBUSQUEDA]:'';
$activo=1;
$idfam='';
$lista_prov = $prov->lista_proveedores($ubicacion,$idfam,$idservicio,$prov_interno,$activo,$prov_texto);
?>

<? if (count($lista_prov)):?>
<table id="table2" class="tinytable"  width="100%" >
  <thead>
      <tr>
	      <th class='nosort'><h3><?=_('ID')?></h3></th>
          <th ><h3><?=_('NOMBRECOMERCIAL')?></h3></th>
	 	  <th ><h3><?=_('INTERNO')?></h3></th>
          <th ><h3><?=_('CONTACTO')?></h3></th>
          <th ><h3><?=_('TELEF CONTACTO')?></h3></th>
          <th ><h3><?=_('TELEF PROVEEDORES')?></h3></th>
          <th ><h3><?=_('AMBITO')?></h3></th>
          <th class="nosort"><h3><?=_('ASIGNAR')?></h3></th>
          <th ><h3><?=_('JUSTIFICACION')?></h3></th>
    
       </tr>
  </thead>
<tbody>
    <?	
    $linea=0;
    
    
    foreach ($lista_prov as $idproveedor=>$datos)
    {
    	unset($telef_prov);
    	unset($telef_cont);

    	$colorlinea = ($linea%2)? 'par':'impar';
    	$prov->carga_datos($idproveedor);
    	$servicio_prov = $con->uparray("SELECT S.IDSERVICIO,S.DESCRIPCION from $catalogo.catalogo_servicio S INNER JOIN $catalogo.catalogo_proveedor_servicio PS ON S.IDSERVICIO = PS.IDSERVICIO where PS.IDPROVEEDOR = $prov->idproveedor");

    	unset($telef_prov);
    	foreach ($prov->telefonos as $telefonos)
    	$telef_prov[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

    	$contacto = new contacto();
    	$sql_contacto="SELECT IDCONTACTO FROM $catalogo.catalogo_proveedor_contacto WHERE IDPROVEEDOR = $prov->idproveedor ORDER BY RESPONSABLE DESC";
    	$exec_contacto=$con->query($sql_contacto);
    	if($rset_contacto=$exec_contacto->fetch_object()) $contacto->carga_datos($rset_contacto->IDCONTACTO);

    	unset($telef_cont);
    	foreach ($contacto->telefonos as $telefonos)
    	$telef_cont[trim($telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO])]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

		if($prov->interno ==1) { $interno='INTERNO'; }else { $interno='EXTERNO';} ?>
		
		
		<tr class='<?=$colorlinea?>'>
			<td width="3%"><?=$prov->idproveedor?></td>
			<td width="25%">
			<img src='/imagenes/iconos/info.png' align='absbottom' border='0' style='cursor: pointer;' onclick="edit_proveedor('<?=$prov->idproveedor?>');" />
			<?=$prov->nombrecomercial?>
			<div id="observ_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC;" >
				 <textarea cols='60' rows='8'><?=$prov->observaciones?></textarea>
			</div>
			<?if ($prov->observaciones!=''):?>
			<img src='/imagenes/32x32/Tag.png' id='i_observ_<?=$linea?>' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onmouseover="mostrar_comentario('i_observ_<?=$linea?>','observ_<?=$linea?>');" 	 / >			
			<?endif;?>
			
			</td>
			<td width="5%"><?=$interno?></td>
			<td width="25%" align='left'><?=$contacto->appaterno.' '.$contacto->apmaterno.' '.$contacto->nombre?></td>	
			<td width="20%" align='right'>
			<? if (isset($telef_cont)):?>  
		  		<table id="telf_cont_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC ">
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
			<td width="20%" align='right'>
		  		<table id="telf_prov_<?=$linea?>" style="display:none; margin: 5px; background:#ECECEC ">
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
			<td width="5%"><?=$datos[AMBITO]?></td>
			<td width="10%" align="center"><input type='button' id='btnAsignar' <?=$disabled?>  value="<?=_('ASIGNAR').' >>'?>" onClick="Asignar(<?=$prov->idproveedor?>,'<?=$reg->NOMBRECOMERCIAL?>',$F('tipocostos'))" class='guardar'><input type='hidden' name='hid_prov' id='hid_prov' value='<?=$prov->idproveedor?>'><input type='hidden' name='hid_idasistencia' value='<?=$idasistencia?>'><input type='hidden' name='hid_servicio' value='<?=$idservicio?>'></td>
			<td align='center'><input type='button' id='btnSiguiente' value="<?=_('JUSTIFICAR').' >>'?>"  title="<?=_('JUSTIFICACION')?>" align='absbottom' class='normal' onclick="just('<?=$prov->idproveedor?>','MANUAL','<?=$linea?>');"/></td>
<!--			<td width="5%" align='center'><img src='/imagenes/32x32/Paste.png' title='JUSTIFICACION' align='absbottom' border='0' alt="16px" width="16px" style='cursor: pointer;' onclick="just('<?=$prov->idproveedor?>','MANUAL');"/></td>-->
		</tr>
	<?$linea++;?>	
	<? } ?>
</tbody>
</table>
<?else :?>
		<span style="align:center"><b><?=_('NO HAY PROVEEDORES EN ESTA BUSQUEDA')?></b></span>
<?endif;?>
