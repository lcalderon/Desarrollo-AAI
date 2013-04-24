<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');

$idusuario= $_SESSION[user];

if (isset($asis)) {
	
	$idprogramaservicio=$asis->idprogramaservicio;
	$idservicio=$asis->servicio->idservicio;

}
else 
{
	$idprogramaservicio=$_POST[IDPROGRAMASERVICIO];
	$idservicio=$_POST[IDSERVICIO];
	
}

if ($idprogramaservicio!=0 OR $_POST[IDPROGRAMASERVICIO]!=''){
	$prog = new programa_servicio();
	$prog->carga_datos($idprogramaservicio);
	$nombreservicio =$prog->etiqueta;
}
else
{
	$serv= new servicio();
	$serv->carga_datos($idservicio);
	$nombreservicio = $serv->descripcion;

}

	$con= new DB_mysqli();
	$con->select_db($con->catalogo);
	
$sql_taller="select IDTALLER,NOMBRE from catalogo_taller where IDCUENTA='".$_GET["idcuenta"]."' order by NOMBRE";
?>

<legend><?=$nombreservicio?></legend>
<form id='form_remolque'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$_SESSION[user];?>">
<input type="hidden" name='idvehiculo' id='idvehiculo' value="">
<input type="hidden" name='IDUBIGEO' id='IDUBIGEO' value="">

<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
  <tr>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1" bordercolor="#ECE9D8">
      <tr>
        <td colspan="2" class="style1"><?=_('DESCRIPCION DE LO OCURRIDO')?><span class="CamposObligatorio">*</span></td>
        </tr>
      <tr>
        <td colspan="2" class="style1"><textarea name="txtadescripcion"  style="text-transform:uppercase;" cols="45" rows="2"  id="txtadescripcion"><?=$asis->asistencia_servicio->DESCRIPCION?></textarea></td>
        </tr>
      <tr>
         
        <td width="345">
        <?=_('TALLER')?><br>
		<?				
			$con->cmbselectdata($sql_taller,"cmbtaller",$asis->asistencia_servicio->IDTALLER,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?><!--input type="button" name="txtconsultar" value="..." onclick="window.open('vista_form_talleres.php?idafiliado=<?=$reg->IDAFILIADO?>','lifecare','height=450, width=800,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes')"/-->
		</td>
		<td width="345">
        <?//=_('GRUA')?><br>
		<?
			// $sql ="select IDTIPOGRUA,DESCRIPCION from $catalogo.catalogo_tipogrua ";				
			// $con->cmbselect_db('IDTIPOGRUA',$sql,$asis->asistencia_servicio->IDTIPOGRUA,'','','');
		?>
		</td>
		
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>      
    </table></td>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1">
 
        <tr>
          <td class="style1"><?=_('COMENTARIO')?></td>
          </tr>
        <tr>
          <td class="style1"><textarea name="txtacomentario"  style="text-transform:uppercase;" cols="45" rows="2" wrap="virtual" id="txtacomentario"><?=$asis->asistencia_servicio->COMENTARIO?></textarea></td>
          </tr>
		<tr>
		<?
				if (isset($asis)){
					$ubigeo = new  ubigeo();
					$ubigeo->leer('ID',$asis->temporal,'asistencia_vehicular_remolque_destino',$asis->asistencia_servicio->IDUBIGEODESTINO);
					}
		?>
		
			<input type='hidden' name='IDDESTINO' id='iddestino' value="<?=$ubigeo->ID ?>">
			<td class="style1"><?=_('LUGAR DE DESTINO')?><br>
			
			<input type='text' name='direcciondestino' id='direcciondestino' value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('iddestino'),'iddestino','direcciondestino','asistencia_vehicular_remolque_destino')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<?} else{?>
				
				<img src='/imagenes/32x32/Tag.png' align='absbottom' border='0' style='cursor: pointer;' alt="16px" width="16px" onclick='ver_ubigeo("<?=$ubigeo->ID?>","asistencia_vehicular_remolque_destino","ID");' />
				<? } ?>
			<span class="CamposObligatorio">*</span>
			</td>
		</tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr><td>&nbsp;</td></tr>
    </table></td>
  </tr>
</table>
 
<br>
</form>
<? 
	// unset($_SESSION['tmpcuenta']);
	// unset($_SESSION['tmpidubigeo']);
	// unset($_SESSION['tmpdireccion']);
	// session_destroy();
 
?>
<script type="text/javascript">
var win = null;

function ver_ubigeo(idubigeo,tabla,campo){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("UBICACION")?>',
			width: 500,
			height: 450,
			showEffect: Element.show,
			hideEffect: Element.hide,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			resizable: true,
			opacity: 0.95,
			url: "../ubigeo/ver_localizacion.php?idubigeo="+idubigeo+'&tabla='+tabla+'&campo='+campo
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
