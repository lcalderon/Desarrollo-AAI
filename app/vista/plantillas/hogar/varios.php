<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
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
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');
$con= new DB_mysqli();
$idusuario= $_SESSION[user];
$con->select_db($con->catalogo);
$temporal = $con->temporal;

$idasistencia = $_GET[idasistencia];
$idservicio=$_GET[idservicio];
$programa=$_GET[idprograma];

if($idasistencia!=''){
	$sql_asistencia="SELECT IDSERVICIO,IDPROGRAMA FROM $temporal.asistencia WHERE IDASISTENCIA=$idasistencia";
	//echo $sql_asistencia;
	$exec_asistencia = $con->query($sql_asistencia);

	if($rset_asistencia=$exec_asistencia->fetch_object()){
		$idservicio=$rset_asistencia->IDSERVICIO;
		$programa=$rset_asistencia->IDPROGRAMA;
	}
}
/*
$asis = new asistencia();
$asis->carga_datos($idasistencia);
$idservicio = $asis->asistencia_servicio->idservicio;
*/
$sql_servicio = "SELECT ETIQUETA FROM catalogo_programa_servicio WHERE IDSERVICIO=$idservicio AND IDPROGRAMA = '$programa'";
//echo $sql_servicio;
$exec_servicio = $con->query($sql_servicio);

if($rset_servicio = $exec_servicio->fetch_object()){ $descservicio = $rset_servicio->ETIQUETA; }


$ubicaciondaniotext = "<textarea name='UBICACIONDANIOTEXT' id='ubicaciondanio' cols='40' rows='0' style='text-transform:uppercase;'>".$asis->asistencia_servicio->UBICACIONDANIO."</textarea>";
switch($idservicio){
	case 13: $ubicaciondanio = $ubicacion_danio;
	$detallepiezadanio = $detallepiezadanio;
	$ubicaciondaniodetalle = $ubicacion_danio_parcial;
	$subservicio = $desc_electricidad_subservicio;break;
	case 16: $ubicaciondanio = $ubicacion_danio;
	$detallepiezadanio = $detallepiezadanio;
	$ubicaciondaniodetalle = $ubicacion_danio_parcial;
	$subservicio = $desc_plomeriagas_subservicio;break;

	case 1: $ubicaciondanio = $ubicacion_danio;
	$detallepiezadanio = $detallepiezadanio;
	$ubicaciondaniodetalle = $ubicacion_danio_parcial;
	$subservicio = $desc_plomeriagas_subservicio;break;
	case 14: $ubicaciondanio = $ubicacion_danio_cerrajeria;
	$detallepiezadanio = $detallepiezadanio;
	$ubicaciondaniodetalle = $ubicacion_danio_cerrajeria_auxiliar;
	$subservicio = $desc_cerrajeria_subservicio;break;
	case 15: $ubicaciondanio = $ubicacion_danio_vidrieria;
	$subservicio = $desc_vidrieria_subservicio;break;
	case 70: $ubicaciondanio = $ubicaciondaniotext;
	$ubicaciondaniodetalle = $ubicacion_danio_parcial;
	$subservicio = $desc_varios_subservicio;break;
	default:
		$ubicaciondanio = $ubicacion_danio;
		$detallepiezadanio = $detallepiezadanio;
		$ubicaciondaniodetalle = $ubicacion_danio_parcial;
		$subservicio = $desc_plomeriagas_subservicio;break;

}

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


?>


<legend><?=$nombreservicio?></legend>


<form id='form_varios'>
	<input type='hidden' name='IDUBIGEO' id='idubigeo' value=''>
    <input type="hidden" name='IDASISTENCIA' id="idasistencia" value="">
	<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
	
<table width="100%">
		<tr>
		  <td><?=_('UBICACION DEL DAÑO')?> <font color="red">*</font></td>
		  <td><?=_('SUBSERVICIO')?></td>
		  <td></td>
	    <td>	    </tr>
		<tr>
		  <td valign="top">
		  <? if($idservicio==70)   echo $ubicaciondaniotext; 
		  else { ?>
 				<table width="80%">
				<tr>
					<td valign="top">
						<? if($asis->asistencia_servicio->ARRALCANCEDANIO==''){
						$default_danio ='PAR';
						}else{
						$default_danio =$asis->asistencia_servicio->ARRALCANCEDANIO;
						}
 						$con->cmbselect_ar('UBICACIONDANIO',$ubicaciondanio,$default_danio,'id=ubicaciondanio','onclick=mostrar(this,"divubicaciondanioparcial")','') ;
 						?>
					</td>
					<td>
					<div id='divubicaciondanioparcial' 
						<? if($idasistencia==''){
								if($idservicio==15){
									echo " style='display: none'";
								}else {
									echo " style='display: block'";
								}
							
							}else {
								if($default_danio =='PAR'){
									echo " style='display: block'";
								}elseif($default_danio=='TOT'){
									echo " style='display: none'";
								} else { echo " style='display: none'"; }
							
							} ?> >
						<? 
					
						$con->cmbselect_ar('UBICACIONDANIOPARCIAL',$ubicaciondaniodetalle,$asis->asistencia_servicio->UBICACIONDANIOPARCIAL,'id=ubicaciondanioparcial','onclick=mostrar(this,"otros")',' ') ?>
					</div>
					<div id="otros" <? if($idasistencia !='' && $asis->asistencia_servicio->UBICACIONDANIOPARCIAL =='OTRO')	{ echo "style='display: block'"; }else{  echo "style='display: none'"; } ?>>
								<?=_('ESPECIFICAR')?><br><input type="text" name="txtotro" id="txtotro" value="<?=$asis->asistencia_servicio->OTROS?>">
					</div>
					</td>
				</tr>
				</table>		
			<? } ?>
		
		</td>
		<td valign="top"><? $con->cmbselect_ar('SUBSERVICIO',$subservicio,$asis->asistencia_servicio->SUBSERVICIO,'id=subservicio','onclick=mostrar_subservicio(this,"divvariossubserviciotec","divvariossubserviciovar")','') ?>
			<div id='divvariossubserviciotec' <? if(isset($asis) && $idservicio==70 && $asis->asistencia_servicio->SUBSERVICIO=='TECV'){ echo "style='display: block'"; } else { echo "style='display: none'"; }?>>
				<? $con->cmbselect_ar('SUBSERVICIODETALLETEC',$desc_varios_subservicio_tec,$asis->asistencia_servicio->SUBSERVICIODETALLE,'id=subserviciodetalle','','') ?>
			</div>
			<div id='divvariossubserviciovar' <? if(isset($asis) && $idservicio==70 && $asis->asistencia_servicio->SUBSERVICIO=='SERV'){ echo "style='display: block'"; } else { echo "style='display: none'"; }?>>
				<? $con->cmbselect_ar('SUBSERVICIODETALLEVAR',$desc_varios_subservicio_var,$asis->asistencia_servicio->SUBSERVICIODETALLE,'id=subserviciodetalle','','') ?>
			</div>
		</td>
   	    </tr>
		<tr>
		  <td><?=_('DESCRIPCION DE LO OCURRIDO')?> <font color="red">*</font> </td>
		  <td><?=_('DETALLE DE LA PIEZA DANADA')?> <font color="red">**</font></td>
		  <td><?=_('RECOMENDACIONES')?> <font color="red">**</font></td>
		  <td></td>
	    </tr>
		<tr>
			<td><textarea name='DESCRIPCIONSERVICIO' id='descripcionservicio' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->DESCRIPCIONSERVICIO?></textarea></td>
			<td valign="top"><? if($idservicio==15){ $con->cmbselect_ar('DETALLEPIEZADANIO',$pieza_danio_vidrieria,$asi->asistencia_servicio->ARRPIEZADANIO,'id=detallepiezadanio','',''); }else{ ?> <textarea name='DETALLEPIEZADANIOTEXT' id='detallepiezadanio' cols='40' rows='0' style='text-transform:uppercase;'><?=$asis->asistencia_servicio->PIEZADANIO?></textarea><? }?></td>
			<td><textarea name='RECOMENDACIONES' id='recomendaciones' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->RECOMENDACION?></textarea></td>
		</tr>
		<tr>
		  <td><?=_('DIAGNOSTICO')?> <font color="red">**</font></td>
		  <td><?=_('SOLUCION DE LA FALLA')?> <font color="red">**</font></td>
		  <td><?=_('MATERIALES UTILIZADOS')?></td>
		  <td></td>
	    </tr>
		<tr>
			<td>
			<textarea name='DIAGNOSTICO' id='diagnostico' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->DIAGNOSTICO?></textarea></td>
		
			<td>
			<textarea name='REPARACION' id='reparacion' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->REPARACION?></textarea></td>
		
			<td>
			<textarea name='MATERIAL' id='material' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->MATERIAL?></textarea></td>
			</td>
		</tr>

	</table>
	</form>
