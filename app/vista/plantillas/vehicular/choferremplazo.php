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
<form id='form_choferremplazo'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod'  value="<?=$idusuario?>">
<input type="hidden" name='IDUBIGEO' id='IDUBIGEO' value="">

 
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
  <tr>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1" >
      <tr>
        <td class="style1"><?=_('MOTIVO DEL SERVICIO')?><span class="CamposObligatorio">*</span></td>
        </tr>
      <tr>
        <td class="style1"><textarea name="txtadescripcion"  style="text-transform:uppercase;" cols="45" rows="2" wrap="virtual" id="txtadescripcion"><?=$asis->asistencia_servicio->DESCRIPCION?></textarea></td>
        </tr>
      <tr>
        <td class="style1"><?=_('NOM. CONDUCTOR')?></td>
         </tr>
      <tr>
        <td class="style1"><input name="txtconductor" type="text" id="txtconductor" size="51" value="<?=$asis->asistencia_servicio->NOMBRECONDUCTOR?>" /></td>
        </tr>
      <tr>
        <td class="style1">&nbsp;</td>
      </tr>	  
      
      
    </table></td>
             <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1">   
        <tr>
          <td class="style1"><?=_('VINCULO AFILIADO')?></td>
          </tr>  
		          
		<tr> 
			<td class="style1">
			<?
				$con->cmbselectdata("SELECT IDPARENTESCO,DESCRIPCION from $con->catalogo.catalogo_parentesco ORDER BY DESCRIPCION","cmbparentesco",$asis->asistencia_servicio->IDPARENTESCO,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
			?><br></td>
		</tr>
        <tr>
            <?
                if (isset($asis)){
                    $ubigeo = new  ubigeo();
                    $ubigeo->leer('ID',$asis->temporal,'asistencia_vehicular_choferemplazo_destino',$asis->asistencia_servicio->IDUBIGEODESTINO);
                    }
            ?>
        
            <input type='hidden' name='IDDESTINO' id='iddestino' value="<?=$ubigeo->ID ?>">
            <td class="style1"><?=_('LUGAR DE DESTINO')?><br>
            
            <input type='text' name='direcciondestino' id='direcciondestino' value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="60" readonly>
            <? if ($desactivado==''){?>
            <img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('iddestino'),'iddestino','direcciondestino','asistencia_vehicular_choferemplazo_destino')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
                <?}?>
            <span class="CamposObligatorio">*</span>
            </td>
        </tr>
                
		<tr>
		  <td class="style1">&nbsp;</td>
		  </tr>
		
    </table></td>
  </tr>
</table>
 <br>
</form>

</body>
