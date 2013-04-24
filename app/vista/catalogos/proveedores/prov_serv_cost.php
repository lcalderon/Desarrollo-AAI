<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once("../../includes/head_prot_win_zap.php");
//include_once("../../../vista/login/Auth.class.php");

$con = new DB_mysqli();
$db= $con->catalogo;
$con->select_db($db);

if ($con->Errno)
{
    printf("Fallo de conexion: %s\n", $con->Error);
    exit();
}

$idservicio=$_GET[idservicio];
$idproveedor=$_GET[idproveedor];
$idmoneda = $_GET[idmoneda];

$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;

$prov = new proveedor();
$prov->carga_datos($idproveedor);
$prov->leer_prov_serv_cost($idproveedor,$idservicio);
//var_dump($prov);
$prov->leer_observacion_servicio($idservicio);

//echo $prov->obs_servicio;

$moneda = new moneda();
$moneda->carga_datos($idmoneda);


$idusuariomod= $_SESSION[user];

$sql="
SELECT 
	csc.IDCOSTO,cc.DESCRIPCION, 
	csc.MONTOLOCAL, csc.MONTOINTERMEDIO,csc.MONTOFORANEO, csc.PLUSNOCTURNO, csc.PLUSFESTIVO,csc.UNIDAD,csc.IDMEDIDA,
	cc.APLICAFORANEO,cc.APLICANOCTURNO,cc.APLICAFESTIVO,cc.COSTONEGOCIADO
FROM 
	catalogo_servicio_costo csc, 
	catalogo_costo cc
	
WHERE 
	csc.IDSERVICIO = '$idservicio'  
	AND csc.IDCOSTO = cc.IDCOSTO 
	AND cc.ACTIVO=1 
	
	ORDER BY cc.IDCOSTO ASC
	

";


$rscosto=$con->query($sql);
$lista_medida = $con->uparray("select IDMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ");

?>
<html>
 <head>
	<title>American Assist</title>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />		
</head>
<body>
 <form id="frmaddregistro"  >
  <table border="0" cellpadding="1" cellspacing="1" width="99%" class="catalogos">
  <input type="hidden" name="IDPROVEEDOR" value="<?=$idproveedor;?>">
  <input type="hidden" name="IDSERVICIO" value="<?=$idservicio;?>">
  <input type="hidden" name="IDUSUARIOMOD" value="<?=$idusuariomod;?>">
	<tr class='modo1'>
	<td colspan="2">
		<!--<div Style="overflow:auto;padding-top:1px; padding-Left:1px; padding-bottom:15px;height:168px; width:840px;">-->
		<table style="width:100%" border="0" align="center" cellpadding="1" cellspacing="1" class="costos">
		  <tr>
			  <td>&nbsp;</td>
			  <td align="center"><i><?=_("UNIDAD");?></i></td>
			  <td align="center"><i><?=_("MEDIDA");?></i></td>
			  <td align="center"><i><?=_("MONEDA");?></i></td>
			  <td style="text-align:center"><i><?=_("LOCAL");?></i></td>
			  <td style="text-align:center"><i><?=_("INTERMEDIO");?></i></td>			  
			  <td style="text-align:center"><i><?=_("FORANEO");?></i></td>
			  <td style="text-align:center"><i><?=_("NOCTURNO");?></i></td>
			  <td style="text-align:center"><i><?=_("FESTIVO");?></i></td>
		  </tr>
			<?	
			$i=0;
			while($reg = $rscosto->fetch_object())
			{
			    $aplicaforaneo='';
			    $aplicaforaneo='';
			    $aplicanocturno='';
			    $indice=$reg->IDCOSTO;
			    if (!$reg->APLICAFORANEO) $aplicaforaneo='disabled';
			    if (!$reg->APLICANOCTURNO) $aplicanocturno='disabled';
			    if (!$reg->APLICAFESTIVO) $aplicafestivo='disabled';

			?>							
				<tr class='modo'>
				  <input type="hidden" name='IDCOSTO[<?=$i?>]' value="<?=$indice;?>" >
				  <td style="text-align:left"><input type='text' size='35' value="<?=$reg->DESCRIPCION; ?>" readonly></td>
			<?if ($reg->COSTONEGOCIADO):?>
				  <td><input name="UNIDAD[<?=$i?>]" type="text"  value="1" size="4" maxlength="2" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-align:center;" onKeyPress="return validarnum(event)" readonly>  <!--<?=$prov->costos[$indice][UNIDAD]?>-->
				  <?=$reg->UNIDAD?>
				  </td>
				  <td><? $con->cmbselect_ar("IDMEDIDA[$i]",$lista_medida,$prov->costos[$indice][IDMEDIDA] ," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ",'','Seleccione'); ?>
				  <?=$reg->IDMEDIDA?>
				  </td>
			  	  <td>
					  <?=$moneda->simbolo?>
  	              </td>
  	               <td><input type="text" name="MONTOLOCAL[<?=$i?>]"  value="<?=$prov->costos[$indice][MONTOLOCAL] ?>" size="8" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);">
				  <?=$reg->MONTOLOCAL?></td>
				   <td><input type="text" name="MONTOINTERMEDIO[<?=$i?>]"  value="<?=$prov->costos[$indice][MONTOINTERMEDIO] ?>" size="8"  class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);">
				  <?=$reg->MONTOINTERMEDIO?></td>
				   <td><input type="text" name="MONTOFORANEO[<?=$i?>]"  value="<?=$prov->costos[$indice][MONTOFORANEO] ?>" size="8"  class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" <?=$aplicaforaneo?>>
				  <?=$reg->MONTOFORANEO?></td>
				   <td><input type="text" name="PLUSNOCTURNO[<?=$i?>]"  value="<?=$prov->costos[$indice][PLUSNOCTURNO] ?>" size="6"  class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" <?=$aplicanocturno?>>
				  <?=$reg->PLUSNOCTURNO?></td>
				   <td><input type="text" name="PLUSFESTIVO[<?=$i?>]"  value="<?=$prov->costos[$indice][PLUSFESTIVO] ?>" size="6"  class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" <?=$aplicafestivo?>>
				  <?=$reg->PLUSFESTIVO?></td>
				  
			<?endif;?>	
				</tr>
			<? 
			$i++;
			}
			?>
					
		  </table>
<!--		 </div>		  -->
	 </td>
	</tr>
	<tr class='modo1'>
		<td valign="top"><?=_('OBSERVACIONES')?></td>
		<td><textarea name='OBSERVACIONES' id='observaciones' cols="60" ><?=$prov->obs_servicio?></textarea></td>
	
	</tr>
	<tr class='modo1'>
	  <td colspan="2" align="right">
	  	<div align="center">
		  	 <input type="button" class="guardar"  value="<?=_('GUARDAR');?>" title="<?=_('GRABAR SERVICIO');?>" onclick="grabar();" <?=($edicion==1)?'':'disabled'?>  >
    		 <input type="button" class="cancelar" value="<?=_('CANCELAR')?>" title="<?=_('CANCELAR')?>" onclick="parent.win.close();" >				  
		 	 
		</div>
	  </td>
	</tr>
   </table>
  </form> 
</body>
</html>

<script type="text/javascript">

function grabar(){
    new Ajax.Request('../../../controlador/ajax/ajax_grabar_prov_serv_cost.php',
    {
        method : 'post'	,
        parameters:  $('frmaddregistro').serialize(true),
        onSuccess: function(t){
            if (t.responseText!='')	alert(t.responseText);
            else
            {
                parent.win.close();
            }
        }

    });
    return;
}


</script>	
