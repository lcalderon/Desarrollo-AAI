<?php

include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once("../../../vista/login/Auth.class.php");
include_once("../../includes/arreglos.php");
include_once("../Catalogos.class.php");

$con = new DB_mysqli();

if ($con->Errno)
{
    printf("Fallo de conexion: %s\n", $con->Error);
    exit();
}

session_start();
Auth::required($_SERVER['REQUEST_URI']);

$sql="
	select 
		catalogo_programa.VALIDACIONEXTERNA,
		catalogo_programa.PILOTO, 
		catalogo_programa.PROGRAMAVIP,
		catalogo_programa.IDUSUARIOMOD,
		catalogo_programa.FECHAMOD, 
		catalogo_programa.ACTIVO, 
		catalogo_cuenta.NOMBRE, 
		catalogo_programa.IDCUENTA,
		catalogo_programa.IDPROGRAMA,
		catalogo_programa.NOMBRE as nomprodcuto,
		catalogo_programa.FECHAINIVIGENCIA,
		catalogo_programa.FECHAFINVIGENCIA,
		catalogo_programa.VALIDA_ACTIVACION,
		catalogo_programa.CONTENIDOARCHIVO
    from 
        $con->catalogo.catalogo_programa 
        INNER JOIN $con->catalogo.catalogo_cuenta on catalogo_cuenta.IDCUENTA=catalogo_programa.IDCUENTA 
    where 
        catalogo_programa.IDPROGRAMA='$_GET[codigo]'";

$result=$con->query($sql);
$row = $result->fetch_object();

$rscuentas=$con->query("select IDCUENTA,NOMBRE from $con->catalogo.catalogo_cuenta");

$dataconformidad=$con->consultation("select count(*) from $con->catalogo.catalogo_programa_conformidad where IDPROGRAMA='".$_GET['codigo']."' ");

$data=$con->consultation("select count(*) from $con->catalogo.catalogo_programa_servicio_beneficiario where IDPROGRAMA='".$_GET['idprograma']."' ");

if($data[0][0] > 0)    $_GET["benef"]="ok";

$Sql_servicio="SELECT
	catalogo_programa_servicio.ACTIVO AS ACTIVOSERV,
	catalogo_programa_servicio.IDPROGRAMASERVICIO,
	catalogo_moneda.DESCRIPCION AS nombremoneda,
	catalogo_programa_servicio.ETIQUETA,
	catalogo_programa_servicio.TIPOCOBERTURA,
	catalogo_programa_servicio.MONTOXSERV,
	catalogo_programa_servicio.IDPROGRAMA,
	catalogo_programa_servicio.IDSERVICIO,
	catalogo_programa.IDPROGRAMA,
	catalogo_programa.NOMBRE,
	catalogo_servicio.DESCRIPCION,
	catalogo_programa_servicio.IDMONEDA,
	catalogo_tipofrecuencia.NOMBRE,
	catalogo_programa_servicio.EVENTOS,
	catalogo_programa.IDCUENTA,
	catalogo_servicio.IDSERVICIO
FROM
	$con->catalogo.catalogo_programa_servicio
INNER JOIN $con->catalogo.catalogo_servicio ON catalogo_servicio.IDSERVICIO = catalogo_programa_servicio.IDSERVICIO
INNER JOIN $con->catalogo.catalogo_programa ON catalogo_programa.IDPROGRAMA = catalogo_programa_servicio.IDPROGRAMA
LEFT JOIN $con->catalogo.catalogo_tipofrecuencia ON catalogo_tipofrecuencia.IDTIPOFRECUENCIA = catalogo_programa_servicio.IDTIPOFRECUENCIA
LEFT JOIN $con->catalogo.catalogo_moneda ON catalogo_moneda.IDMONEDA = catalogo_programa_servicio.IDMONEDA
WHERE
	catalogo_programa.IDPROGRAMA = '".$row->IDPROGRAMA."'
GROUP BY
	catalogo_programa_servicio.IDPROGRAMASERVICIO
ORDER BY
	catalogo_servicio.DESCRIPCION,
	catalogo_programa_servicio.ACTIVO";
	
$rservicios=$con->query($Sql_servicio);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
<script type="text/javascript" src="/librerias/scriptaculous/scriptaculous.js"></script>
    
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/effects.js"></script>
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window.js"></script>
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window_ext.js"></script> 
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window_effects.js"></script>
<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/debug.js"></script>

<link href="/librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
<link href="/librerias/windows_js_1.3/themes/spread.css" rel="stylesheet" type="text/css" ></link>
<link href="/librerias/windows_js_1.3/themes/alert.css" rel="stylesheet" type="text/css" ></link>
<link href="/librerias/windows_js_1.3/themes/alert_lite.css" rel="stylesheet" type="text/css" ></link>
<link href="/librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
<link href="/librerias/windows_js_1.3/themes/debug.css" rel="stylesheet" type="text/css" ></link>
    

<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>

<link href="../../../../estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
<script src="../../../../estilos/fronter_js/jquery.js" type="text/javascript"></script>
<script src="../../../../estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
<script src="../../../../estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
<script src="../../../../estilos/fronter_js/index.js" type="text/javascript"></script>	

<style type="text/css">
    @import url("../../../../librerias/jscalendar-1.0/calendar-system.css");.style4 {color: #FFFFFF}
</style>



<script language="JavaScript">



	function verifica_cuenta(valor,idprogserv,fila){
		
		new Ajax.Request('activar_desactivarServicios.php',{
			method: 'post',
			asynchronous: true,
			postBody: 'idprogserv='+idprogserv+'&valor='+valor,
			onSuccess: function(resp){

				var elemento= resp.responseText.split(',');

				if(resp.responseText==1){
					alert('SE PROCESO CORRECTAMENTE.');
					    
						if (!valor)	document.getElementById(fila).style.backgroundColor='ff0000'; else document.getElementById(fila).style.backgroundColor='9fc6d5';
		
				} else{
					alert('NO SE PROCESO.');									
				}
			}
		});
	}
	
function validarCampo(variable){

    document.frmeditregistro.nombre.value=document.frmeditregistro.nombre.value.replace(/^\s*|\s*$/g,"");
    if(document.frmeditregistro.nombre.value =='' ){
        alert('<?=_("INGRESE LA DESCRIPCION DEL PLAN.") ;?>');
        document.frmeditregistro.nombre.focus();
        return (false);
    }
    else if(document.frmeditregistro.cmbcuenta.value =='' ){
        alert('<?=_("SELECCIONE LA CUENTA.") ;?>');
        document.frmeditregistro.cmbcuenta.focus();
        return (false);
    }

    return (true);
}


function validarCampoAgrega(variable){

    if(document.frmeditregistro.cmbservicio.value =='' ){
        alert('<?=_("SELECCIONE EL SERVICIO.") ;?>');
        document.frmeditregistro.cmbservicio.focus();
        return (false);
    }
    else if(document.frmeditregistro.txtmonto.value =='' ){
        alert('<?=_("INGRESE EL MONTO") ;?>');
        document.frmeditregistro.txtmonto.focus();
        return (false);
    }
    else if(document.frmeditregistro.txtevento.value =='' ){
        alert('<?=_("INGRESE EL NUMERO DE EVENTO") ;?>');
        document.frmeditregistro.txtevento.focus();
        return (false);
    }
    else if(document.frmeditregistro.cbmtfrecuencia.value =='' ){
        alert('<?=_("SELECCIONE EL TIPO DE FRECUENCIA") ;?>');
        document.frmeditregistro.cbmtfrecuencia.focus();
        return (false);
    }

    return (true);
}

function enviaremail(){
    if(confirm('<?=_("ESTA SEGURO QUE DESEA ENVIAR LOS EMAILS DE APROBACION?.") ;?>'))
    {
        enviarEmail('<?=$_GET['codigo'];?>');
    }

}

function validarCampoEdita(variable){

    if(document.frmeditar.txtmonto.value =='' ){
        alert('Ingrese el Monto');
        document.frmeditar.txtmonto.focus();
        return (false);
    }
    else if(document.frmeditar.txtevento.value =='' ){
        alert('Ingrese el numero de evento.');
        document.frmeditar.txtevento.focus();
        return (false);
    }

    return (true);
}


function restricciontxt(){

    if(document.getElementById('cbmtcobertura').value =='SL' || document.getElementById('cbmtcobertura').value =='CX' ){

        document.getElementById('txtevento').value=0;
        document.getElementById('txtmonto').value=0;
        document.getElementById('cmbmoneda').selectedIndex=0;
        document.getElementById('cbmtfrecuencia').selectedIndex=1;
        document.getElementById('cmbmoneda').disabled=true;
        document.getElementById("txtmonto").readOnly = true;
        document.getElementById("txtevento").readOnly = true;
    }
    else
    {
        document.getElementById('cmbmoneda').selectedIndex=2;
        document.getElementById('cbmtfrecuencia').selectedIndex=1;
        document.getElementById("txtmonto").readOnly = false;
        document.getElementById("txtevento").readOnly = false;
        document.getElementById('cmbmoneda').disabled=false;

    }
}

</script>


</head>
<body>
<form name="frmeditregistro" action="actualizar_programa.php" method="POST" enctype="multipart/form-data" onSubmit = "return validarCampo(this)" >
    <input name="opc" type="hidden" value="<?=$_GET["opc"]; ?>">    
    <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
  <table border="0" cellpadding="1" cellspacing="1" width="85%" class="catalogos">
    <tr bgcolor="#333333">
      <th style="text-align:left">EDITAR PLAN</th>
    </tr>
    <tr class='modo1'>
      <td><?=_("CODIG") ;?></td>
      <td colspan="2"><input name="txtcodigo" readonly type="text" value="<?=$_GET['codigo']; ?>" size="14" style="text-transform:uppercase;"  maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></td>
    </tr>
    <tr class='modo1'>
      <td><?=_("DESCRIPCION") ;?></td>
      <td colspan="2"><input name="nombre" type="text" value="<?=$row->nomprodcuto; ?>" style="text-transform:uppercase;" size="45" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto"></td>
    </tr>
    <tr class='modo1'>
      <td><?=_("CUENTA") ;?></td>
      <td colspan="2"><select name="cmbcuenta" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
          <?
          while($reg = $rscuentas->fetch_object())
          {
              if($reg->IDCUENTA == $row->IDCUENTA)
              {
          ?>
          <option value="<?=$reg->IDCUENTA; ?>" selected>
          <?=$reg->NOMBRE; ?>
          </option>
          <?
              }
              else
              {					
                  continue;
              }
          }
                    ?>
      </select></td>
    </tr>
	<tr class='modo1'>
		<td><?=_("PILOTO") ;?></td>
		<td colspan="2"><input name="piloto" id="piloto" type="text" size="20" maxlength="20" value="<?=$row->PILOTO; ?>" onFocus="coloronFocus(this);" onKeyPress="return validarnum(event)" onBlur="colorOffFocus(this);" class="classtexto"  style="text-transform:uppercase;"></td>
	</tr>
    <tr class='modo1'>
        <td><?=_("PLAN VIP") ;?></td>
        <td colspan="2"><input type="checkbox" name="chkvip" id="chkvip" value="1" <?=($row->PROGRAMAVIP==1?'checked':''); ?> ></td>
    </tr>
    <tr class='modo1'>
		<td><?=_("VALIDACION EXTERNA") ;?></td>
		<td colspan="2"><input type="checkbox" name="ckbvalidacion" id="ckbvalidacion" value="1" <?=($row->VALIDACIONEXTERNA==1?'checked':''); ?> ></td>
    </tr>        
    <tr class='modo1'>
		<td><?=_("FECHA INI. VIGENCIA") ;?></td>
		<td colspan="2"><input type="text" readonly  id="fechainiserv" name="fechainiserv"  value="<?=$row->FECHAINIVIGENCIA; ?>"  class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
          <button   id="cal-button-1">...</button>
        <script type="text/javascript">
        Calendar.setup({
            inputField    : "fechainiserv",
            button        : "cal-button-1",
            align         : "Tr"
        });
                  </script>
        &nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA') ;?>" onClick="document.frmeditregistro.fechainiserv.value='0000-00-00' " style="cursor:pointer" /></td>
    </tr>
    <tr class='modo1'>
      <td><?=_("FECHA FIN VIGENCIA") ;?></td>
      <td colspan="2"><input type="text" readonly id="fechafinserv" name="fechafinserv" value="<?=$row->FECHAFINVIGENCIA; ?>"  class="classtexto" maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" />
          <button   id="cal-button-2">...</button>
        <script type="text/javascript">
        Calendar.setup({
            inputField    : "fechafinserv",
            button        : "cal-button-2",
            align         : "Tr"
        });
                     </script>
        &nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA') ;?>" onClick="document.frmeditregistro.fechafinserv.value='0000-00-00' " style="cursor:pointer" /></td>
    </tr>
    <!--tr class='modo1'>
      <td><?=_("GESTIONAR PLAN") ;?></td>
      <td colspan="2"><input type="checkbox" name="chkgestionar" value="1" <?=($row->VALIDA_ACTIVACION==1?'checked':''); ?> /></td>
    </tr-->  
	<tr class='modo1'>
      <td><?=_("ACTIVADO") ;?></td>
      <td colspan="2"><input type="checkbox" name="chkactivo" value="1" <?=($row->ACTIVO==1?'checked':''); ?> /></td>
    </tr>
    <tr class='modo1'>
      <td><?=_("CONTRATO") ;?></td>
      <td><input name="userfile"  type="file" size="30" maxlength="" /></td>
      <td>
      <? if ($row->CONTENIDOARCHIVO!=''):?>
            <img src='/imagenes/iconos/pdf.gif' alt='<?=_('VER DETALLE')?>' width='15' style='cursor: pointer;' title='<?=_('VER CONTRATO');?>' onclick="ver_detalle('<?=$_GET['codigo'];?>')"></img>
            
      <?endif;?>      
       </td>            
    </tr>
    <tr class='modo1'>
      <td><?=_("ULTIMA MODIFICACION") ;?></td>
      <td><?=_("USUARIO") ;?>:&nbsp;<b>
        <?=$row->IDUSUARIOMOD; ?>
        </b>&nbsp;<?=_("FECHA") ;?>:&nbsp; <b>
        <?=$row->FECHAMOD; ?>
      </b></td>
      <td><img style="cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="reDirigir_ventana('../../../../app/vista/catalogos/programas/historial.php?idprograma=<?=$row->IDPROGRAMA; ?>','window','650','350','VENTANAPAIS');"></td>
    </tr>
    <tr class='modo1'>
      <?php
      if($_GET["opc"])
      {
                    ?>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('CERRAR') ;?>" onClick="parent.MochaUI.closeWindow(parent.$('youtube'));" title="<?=_('CERRAR') ;?>"></td>
      <?php
      }
      else
      {
                    ?>
      <td align="right"><input  type="button" class="botonstandar" value="<?=_('REGRESAR') ;?>" onClick="reDirigir('general.php')" title="<?=_('REGRESAR') ;?>"></td>
      <?php
      }
      ?>
      <td align="left"><input name="Submit"  class="botonstandar" type="submit" value="<?=_('GRABAR') ;?>" title="<?=_('GRABAR PLAN') ;?>" > &nbsp; &nbsp; &nbsp;<!--a href="#" style="color:#000066" onClick="new parent.MochaUI.Window({id: 'containertest',title: '<?=_("LOG DE CONFORMIDAD");?>',loadMethod: 'xhr',contentURL: '../../app/vista/catalogos/programas/logconfirmacion.php?idprograma=<?=$row->IDPROGRAMA; ?>',container: 'pageWrapper',width: 620,height: 250,x: 100,y: 150});" ><?//=_("LOG DE CONFORMIDAD") ;?></a--></td>
      <td align="right"> >>
        &nbsp;&nbsp;&nbsp;<?=_('Agregar Servicio') ;?> <input name="agregars"  type="button" value="+" onClick="showFormulario('<?=$row->IDPROGRAMA; ?>','','<?=$_GET["opc"]; ?>');" title="<?=_('AGREGAR SERVICIO') ;?>" >
        &nbsp;</td>
    </tr>
  </table>
    <input name="idprograma" type="hidden" value="<?=$row->IDPROGRAMA; ?>">

</form> 
        <br>
        <div id="beneficiario" style="margin:1px;padding:1px;float:left;position:absolute;top:26px;left:425px;width:200px;height:50px;display:none" ></div>
        
        
        <table border="0" cellpadding="1" cellspacing="1" width="90%"  class="catalogos" id="resultado">
            <tr>                
                <th><?=_('SERVICIO') ;?></th>
                <th><?=_('NOMBRECOMERCIAL') ;?></th>
                <th><?=_('MONTO') ;?></th>
                <th><?=_('#EVENTOS') ;?></th>
                <th><?=_('FRECUENCIA') ;?></th>
                <th colspan="3" style="Background:#FFFFFF"></th>
            </tr>                            
            <?
            //servicios plan
            while($rowserv = $rservicios->fetch_object()){
                                              
                $Sql_exisid=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDPROGRAMASERVICIO='$rowserv->IDPROGRAMASERVICIO' 
												AND IDPROGRAMASERVICIO!='0'  AND IDPROGRAMASERVICIO!=''  AND IDPROGRAMA = '$rowserv->IDPROGRAMA' AND IDCUENTA = '$rowserv->IDCUENTA' AND IDSERVICIO = '$rowserv->IDSERVICIO' ");	

                $exisid=$Sql_exisid[0][0];

                if($rowserv->MONTOXSERV == "0.00")    $rowserv->MONTOXSERV="";
                if($rowserv->EVENTOS == "0.00")    $rowserv->EVENTOS="Ilimitado";				

                $monto=$rowserv->MONTOXSERV." ".$rowserv->nombremoneda." / ".$desc_tipo_cobertura[$rowserv->TIPOCOBERTURA];
				$i++;
            ?>          
            <tr bgcolor="<?=($rowserv->ACTIVOSERV==1)?"#9fc6d5":"#ae202a"?>" id="fila0<?=$i?>">
				<td align="left"><?=utf8_encode($rowserv->DESCRIPCION); ?></td>
				<td align="left"><?=utf8_encode($rowserv->ETIQUETA); ?></td>
                <td align="left"><?=$monto; ?></td>
                <td><?=$rowserv->EVENTOS; ?></td>                
                <td><?=$rowserv->NOMBRE; ?></td>                
                <td align="center"><img src="../../../../imagenes/iconos/editars.gif" width="14" height="14" title="<?=_('EDITAR SERVICIO') ;?>" style="cursor:pointer" onClick="showFormulario('<?=$rowserv->IDPROGRAMASERVICIO; ?>','<?=$rowserv->IDSERVICIO; ?>','<?=$_GET["opc"]; ?>','<?=$exisid?>');" ></td>            
                <td align="center"><? if($exisid <1){?><img src="../../../../imagenes/iconos/eliminar.gif" title="<?=_('ELIMINAR SERVICIO') ;?>" style="cursor:pointer" onClick="confirmaRespuesta('<?=_('ESTAS SEGURO DE ELIMINAR ESTE REGISTRO') ;?> [<?=$rowserv->ETIQUETA; ?>]?','eliminaserv.php?idprogramaserv=<?=$rowserv->IDPROGRAMASERVICIO; ?>&idprograma=<?=$rowserv->IDPROGRAMA?>&idcuenta=<?=$rowserv->IDCUENTA?>&pag=editar&opc=<?=$_GET["opc"]; ?>')" ><?}else{?><img src="../../../../imagenes/iconos/eliminar_inactivo.gif" title="<?=_('YA EXISTE EL SERVICIO ASIGNADO') ;?>" ><?}?></td>
                <td align="center"><input type="checkbox" name="chkstatus" id="chkstatus" value="1" <?=($rowserv->ACTIVOSERV==1?'checked':''); ?> title="Activar/Desactivar Servicio Asignado" onclick="verifica_cuenta(this.checked,'<?=$rowserv->IDPROGRAMASERVICIO?>','fila0<?=$i?>')"/></td>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
			</tr>
             <?                             
            }
             ?>
        </table>
    
        <div id="bloque"></div>
</body>
</html>

<script type="text/javascript">
var win = null;
function ver_detalle(idprograma)
{
    
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("DETALLE DEL CONTRATO")?>',
            width: 600,
            height: 300,
            showEffect: Element.show,
            hideEffect: Element.hide,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            resizable: true,
            opacity: 0.95,
            url: "../../plantillas/contrato.php?idprograma="+idprograma
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
