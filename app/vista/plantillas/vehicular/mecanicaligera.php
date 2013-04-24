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
	
?>

<legend><?=$nombreservicio?></legend>
<body>
<form id='form_mecanicaligera'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod'  value="<?=$idusuario?>">
<input type="hidden" name='IDUBIGEO' id='IDUBIGEO' value="">
 
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
  <tr>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1" >      
      <tr>
        <td width="453" colspan="2" class="style1"><?=_('DESCRIPCION DE LO OCURRIDO')?></td>
        </tr>
      <tr>
        <td colspan="2" class="style1"><textarea name="txtadescripcion"  style="text-transform:uppercase;" cols="45" rows="2" wrap="virtual" id="txtadescripcion"><?=$asis->asistencia_servicio->DESCRIPCION?></textarea></td>
        </tr>
      
      
      
    </table></td>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1">
 
        <tr>
          <td class="style1"><?=_('SOLUCION DE LA FALLA')?><span class="CamposObligatorio">**</span></td>
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
			<td>&nbsp;</td>
		</tr>
		

 
     
    </table></td>
  </tr>
</table>
  <br>
</form>

</body>
