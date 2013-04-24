<?php

include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once("../../../vista/login/Auth.class.php");

$con = new DB_mysqli();


if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

$con->select_db($con->catalogo);

	session_start();
 	Auth::required($_SERVER['REQUEST_URI']);

$idservicio=$_GET['codigo'];


$ii=1;
$rs_servicosto=$con->query("select IDCOSTO,IDSERVICIO,MONTOLOCAL,IDMONEDA,UNIDAD,IDMEDIDA,MONTOFORANEO,PLUSNOCTURNO,PLUSFESTIVO from catalogo_servicio_costo where IDSERVICIO='$idservicio' /*AND PRIORIDAD=1 */ order by IDCOSTO ");
while($regp = $rs_servicosto->fetch_object())
{
	$rowcosto[]=$regp->IDCOSTO;
	$rowmonto[$regp->IDCOSTO]=$regp->MONTOLOCAL;
	$rowmoneda[$regp->IDCOSTO]=$regp->IDMONEDA;
	$rowunidad[$regp->IDCOSTO]=$regp->UNIDAD;
	$rowmedida[$regp->IDCOSTO]=$regp->IDMEDIDA;
	$rowforaneo[$regp->IDCOSTO]=$regp->MONTOFORANEO;
	$rownocturno[$regp->IDCOSTO]=$regp->PLUSNOCTURNO;
	$rowfestivo[$regp->IDCOSTO]=$regp->PLUSFESTIVO;
	$ii=$ii+1;
}
 
$rscosto=$con->query("select IDCOSTO,DESCRIPCION,APLICAFORANEO,APLICANOCTURNO,APLICAFESTIVO,COSTONEGOCIADO from catalogo_costo where ACTIVO=1 order by DESCRIPCION ");

$Sql_moneda="SELECT
				  catalogo_parametro_moneda.IDMONEDA,
				  catalogo_moneda.DESCRIPCION
				FROM catalogo_parametro_moneda
				  INNER JOIN catalogo_moneda
					ON catalogo_moneda.IDMONEDA = catalogo_parametro_moneda.IDMONEDA
				ORDER BY catalogo_parametro_moneda.PRIORIDAD ASC";

$lista_medida = $con->uparray("select IDMEDIDA,DESCRIPCION from catalogo_medida where DESCRIPCION!='' order by DESCRIPCION ");

//consulta todos los servicios
$sql="select 
		catalogo_servicio.DURACIONESTIMADA,
		catalogo_servicio.IDCOBERTURA,
		catalogo_servicio.IDFAMILIA,
		catalogo_servicio.IDUSUARIOMOD,
		catalogo_servicio.FECHAMOD,
		catalogo_servicio.IDSERVICIO,
		catalogo_servicio.IDPLANTILLA,
		catalogo_servicio.DESCRIPCION,
		catalogo_servicio.FECHAINIVIGENCIA,
		catalogo_servicio.FECHAFINVIGENCIA,
		catalogo_servicio.VALIDACIONTIEMPO,
		catalogo_servicio.CONCLUCIONTEMPRANA,
		catalogo_servicio.PAGER_SERV,
		catalogo_servicio.MATNR,
		catalogo_servicio.CONCLUCIONCONPROVEEDOR,
		catalogo_plantilla.DESCRIPCION as nomplatlla 
	  from 
		catalogo_servicio 
	   inner join catalogo_plantilla on catalogo_plantilla.IDPLANTILLA=catalogo_servicio.IDPLANTILLA 
	WHERE IDSERVICIO='$idservicio'";
//echo $sql;
$result=$con->query($sql);
$row = $result->fetch_object();

$rsplantilla=$con->query("select IDPLANTILLA,DESCRIPCION from catalogo_plantilla order by DESCRIPCION ");
$rsfamilia=$con->query("select IDFAMILIA,DESCRIPCION from catalogo_familia order by DESCRIPCION ");

$rsinfra=$con->query("select IDINFRAESTRUCTURA,DESCRIPCION from catalogo_infraestructura where ACTIVO=1 order by DESCRIPCION ");

$rsinfraes=$con->query("select IDINFRAESTRUCTURA from catalogo_infraestructura order by DESCRIPCION ");
$rs_servinfra=$con->query("select IDINFRAESTRUCTURA from catalogo_servicio_infraestructura where IDSERVICIO='$idservicio' order by PRIORIDAD ");
while($regi = $rs_servinfra->fetch_object())
{
	$rowi[]=$regi->IDINFRAESTRUCTURA;
}

if(count($rowi)== 0)	$rowi=$rsinfraes->num_rows;
?>
<html>
	<head>
		<title>American Assist</title> 

		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
			
			<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
			<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
			<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>			
			<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
			<script type="text/javascript" src="/librerias/scriptaculous/scriptaculous.js"></script>
			
			<link href="../../../../estilos/fronter_css/jquery.windows-engine.css"	rel="stylesheet" type="text/css" />
			<script src="../../../../estilos/fronter_js/jquery.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/jquery.validate.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/jquery.windows-engine.js" type="text/javascript"></script>
			<script src="../../../../estilos/fronter_js/index.js" type="text/javascript"></script>	
			
			
			<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>
			<style type="text/css">		
				.style1 {
					color: #FFFFFF;		
				}
			</style>
			<script language="JavaScript">

			function validarCampo(variable){

				document.frmeditar.nombre.value=document.frmeditar.nombre.value.replace(/^\s*|\s*$/g,"");
				if(document.frmeditar.nombre.value =='' ){
					alert('<?=_("INGRESE LA DESCRIPCION DEL SERVICIO.") ;?>');
					document.frmeditar.nombre.focus();
					return (false);
				}

				return (true);
			}

			function adicionarFila(){

				var tabla = document.getElementById("infra").tBodies[0];
				var fila = document.createElement("TR");
				fila.setAttribute("align","left");

				var celda1 = document.createElement("TD");

				var celda2 = document.createElement("TD");
				var sel = document.createElement("SELECT");
				sel.setAttribute("size","1");
				sel.setAttribute("onFocus","coloronFocus(this)");
				sel.setAttribute("onBlur","colorOffFocus(this)");
				sel.setAttribute("class","classtexto");
				sel.setAttribute("name","cmbinfra[]" );


				opcion0 = document.createElement('OPTION');
				opcion0.innerHTML = 'SELECCIONE';
				opcion0.value = '';
				//if($reg->IDINFRAESTRUCTURA == 1)	echo "opcion$i.selected = '".$reg->IDINFRAESTRUCTURA."';\n";
				sel.appendChild(opcion0);

				<?php

				$i=1;
				while($reg= $rsinfra->fetch_object())
				{
					echo "opcion$i = document.createElement('OPTION');\n";
					echo "opcion$i.innerHTML = '".utf8_decode($reg->DESCRIPCION)."';\n";
					echo "opcion$i.value = '".$reg->IDINFRAESTRUCTURA."';\n";
					//if($reg->IDINFRAESTRUCTURA == 1)	echo "opcion$i.selected = '".$reg->IDINFRAESTRUCTURA."';\n";
					echo "sel.appendChild(opcion$i);\n";
					$i=$i+1;

				}
				?>

				celda2.appendChild(sel);

				//var celda3 = document.createElement('TD');
				var boton = document.createElement('IMG');

				boton.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
				boton.setAttribute('title','ELIMINAR');
				boton.setAttribute('border','0');
				boton.setAttribute('height','14');
				boton.setAttribute('style','cursor:pointer');
				boton.onclick=function(){borrarFila(this);}
				celda2.appendChild(boton);

				fila.appendChild(celda1);
				fila.appendChild(celda2);

				tabla.appendChild(fila);

			}

			function borrarFila(button){
				var fila = button.parentNode.parentNode;
				var tabla = document.getElementById('infra').getElementsByTagName('tbody')[0];
				tabla.removeChild(fila);
			}

			</script>			
	</head>
	<body onLoad="document.frmeditar.nombre.focus();">
	<form name="frmeditar" id="frmeditar" action="actualizar_servicio.php" method="POST" onSubmit = "return validarCampo(this)"  >
		<input name="idservicio" type="hidden" value="<?=$idservicio; ?>" />
		<input name="txturl" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>
			<input name="pag" type="hidden" value="<?=$pag; ?>" />
		
		<table border="0" cellpadding="1" cellspacing="1" width="70%"  class="catalogos">
			<tr>
				<th style="text-align:left">EDITAR SERVICIO</th>
			</tr>
			<tr class='modo1'>
				<td><?=_("DESCRIPCION");?></td>
				<td><input name="nombre" type="text" value="<?=$row->DESCRIPCION; ?>" size="80" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" ></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("PLANTILLA");?></td>
				<td><select name="cmbplantilla" id="cmbplantilla" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				  <?
				  while($reg = $rsplantilla->fetch_object())
				  {
				  	if($reg->IDPLANTILLA == $row->IDPLANTILLA)
				  	{
					?>
							<option value="<?=$reg->IDPLANTILLA; ?>" selected><?=$reg->DESCRIPCION; ?></option>
				  <?
				  	}
				  	else
				  	{
					?>
							<option value="<?=$reg->IDPLANTILLA; ?>"><?=$reg->DESCRIPCION; ?></option>
				  <?	 
				  	}
				  }
					?>
			    </select></td>
			</tr>
			<tr class='modo1'>
				  <td><?=_("FAMILIA");?></td>
				  <td><select name="cmbfamilia" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto">
				  <?
				  while($reg = $rsfamilia->fetch_object())
				  {
				  	if($reg->IDFAMILIA == $row->IDFAMILIA)
				  	{
					?>
							<option value="<?=$reg->IDFAMILIA; ?>" selected><?=$reg->DESCRIPCION; ?></option>
				  <?
				  	}
				  	else
				  	{
					?>
							<option value="<?=$reg->IDFAMILIA; ?>"><?=$reg->DESCRIPCION; ?></option>
				  <?	 
				  	}
				  }
				  ?>
			    </select></td>
		  </tr>			  
			<tr class='modo1'>
				<td><?=_("FECHA INI. VIGENCIA");?></td>
				<td><input type="text" readonly id="fechainiserv" class="classtexto" maxlength="10" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" name="fechainiserv" value="<?=$row->FECHAINIVIGENCIA; ?>"/>
					<button   id="cal-button-1">...</button>
					<script type="text/javascript">
					Calendar.setup({
						inputField    : "fechainiserv",
						button        : "cal-button-1",
						align         : "Tr"
					});
					</script>&nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA');?>" onClick="document.frmeditar.fechainiserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
				<td><?=_("FECHA FIN VIGENCIA");?></td>
				<td><input type="text" readonly maxlength="10" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" id="fechafinserv" name="fechafinserv" value="<?=$row->FECHAFINVIGENCIA; ?>" />
					<button  id="cal-button-2">...</button>
					<script type="text/javascript">
					Calendar.setup({
						inputField    : "fechafinserv",
						button        : "cal-button-2",
						align         : "Tr"
					});
					</script>&nbsp;<img src="../../../../imagenes/iconos/limpiar.jpg" title="<?=_('LIMPIAR FECHA');?>" onClick="document.frmeditar.fechafinserv.value='0000-00-00' " style="cursor:pointer" /></td>
			</tr>
			<tr class='modo1'>
			  <td><?=_("DURACION ESTIMADA");?> </td>
			  <td><input name="txtduracionestimada" type="text" size="10" value="<?=$row->DURACIONESTIMADA; ?>" class="classtexto" maxlength="4" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" onKeyPress="return validarnumero(event)"  > <strong><?=_('MINUTOS');?></strong></td>
			</tr>			
			<tr class='modo1'>
			   <td><?=_("VALIDACION DE TIEMPOS ");?></td>
			   <td><input type="checkbox" name='validaciontiempo' <?=($row->VALIDACIONTIEMPO)?'checked':'';?>></td>
			</tr>	
			
			<tr class='modo1'>
			   <td><?=_("CONCLUIR TEMPRANO");?></td>
			   <td>
			   <span style='float:left;'>
			   	 <input type="checkbox" name='concluciontemprana'   onchange="act_zona_con();" <?=($row->CONCLUCIONTEMPRANA)?'checked':'';?> >
			   </span>
			   <span id='zona_con' style='float:center;display:<?=($row->CONCLUCIONTEMPRANA)?'block':'none'?>'>
			   	 <input type="radio" name='conclucionconproveedor' value='1' <?=($row->CONCLUCIONCONPROVEEDOR)?'checked':'';?>><?=_('CON PROVEEDOR')?>
  			     <input type="radio" name='conclucionconproveedor' value='0' <?=($row->CONCLUCIONCONPROVEEDOR)?'':'checked';?>><?=_('SIN PROVEEDOR')?>
			   </span>
				</td>
			</tr>
			<tr class='modo1'>
			   <td><?=_("SAP PAGER_SERV");?></td>
			   <td><? $con->cmbselect_db('PAGER_SERV',"SELECT PAGER_SERV,PAGER_SERV FROM $con->catalogo.catalogo_sap_pager_serv","$row->PAGER_SERV",'class="classtexto" id="pager_serv"','','SELECCIONE')?></td>
			</tr>
			<tr class='modo1'>
			   <td><?=_("SAP MATNR");?></td>
			   <td><? $con->cmbselect_db('MATNR',"SELECT MATNR,MATNR FROM $con->catalogo.catalogo_sap_matnr;","$row->MATNR",'class="classtexto" id="matnr"','','SELECCIONE')?></td>
			</tr>		
					
			<tr class='modo1'>
				<td><?=_("ULTIMO CAMBIO");?></td>
				<td><?=_("USUARIO");?>:&nbsp;<b><?=$row->IDUSUARIOMOD; ?></b>&nbsp;<?=_("FECHA");?>:&nbsp;<b><?=$row->FECHAMOD; ?></b>&nbsp;<img style="color:#CC0000; cursor:pointer" width="67" height="26" src="../../../../imagenes/iconos/historial.gif" title="<?=_('HISTORIAL DE CAMBIOS');?>" onClick="reDirigir_ventana('../../../../app/vista/catalogos/servicios/historial.php?idservicio=<?=$idservicio; ?>','window','650','350','VENTANASERVICIO');" ></img></td>
			</tr>			
			<tr class='modo1'>
			  <td><?=_("TIPO COSTOS");?></td>
			  <td><label></label></td>
			</tr>
			<tr class='modo1'>
				<td colspan="2">
					
<table width="100%" border="0">
  <tr>
    <td><table style="width:100%" border="0" align="center" cellpadding="1" cellspacing="1" class="costos">
      <tr>
        <td colspan="4" bgcolor="#E2EBEF"></td>
        <td align="center" colspan="2" bgcolor="#1951BD"><strong><span class="style1">
          <?=_("P-BASE");?>
        </span></strong></td>
        <td align="center" colspan="2" bgcolor="#1951BD"><strong><span class="style1">
          <?=_("P-PLUS");?>
        </span></strong></td>
      </tr>
      <tr>
        <td width="21%">&nbsp;</td>
        <td width="19%" style="text-align:center"><i><strong>
          <?=_("MONEDA");?>
        </strong></i></td>
        <td width="8%" align="center"><i><strong>
          <?=_("UNIDAD");?>
        </strong></i></td>
        <td width="14%" align="center"><i><strong>
          <?=_("MEDIDA");?>
        </strong></i></td>
		
        <td width="9%" align="center" bgcolor="#B7B7B7"><strong>
          <?=_("LOCAL");?>
        </strong></td>
        <td width="9%" align="center" bgcolor="#B7B7B7"><strong>
          <?=_("FORANEO");?>
        </strong></td>
        <td width="9%" align="center" bgcolor="#B7B7B7"><strong>
          <?=_("NOCTURNO");?>
        </strong></td>
        <td width="11%" align="center" bgcolor="#B7B7B7"><strong>
          <?=_("FESTIVO");?>
        </strong></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><div id="resultado" Style="overflow:auto;padding-top:1px; padding-Left:1px; padding-bottom:15px;height:175px; width:100%;">
      <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" class="costos" style="width:100%">
        <?		

        while($reg = $rscosto->fetch_object())
        {
        	$indice=$reg->IDCOSTO;
        	if(in_array($reg->IDCOSTO,$rowcosto))
        	{
        		$marcar="checked";
        		$c=$c+1;
        		$monto=$rowmonto[$indice];
        		$moneda=$rowmoneda[$indice];
        		$unidad=$rowunidad[$indice];
        		$medida=$rowmedida[$indice];
        		$foraneo=$rowforaneo[$indice];
        		$nocturno=$rownocturno[$indice];
        		$festivo=$rowfestivo[$indice];
        	}
					?>
        <tr class='modo'>
          <td width="30%" style="text-align:left"><input type="checkbox" name="chkdesc[]" value="<?=$reg->IDCOSTO; ?>"  <?=$marcar;?> >
                <?=$reg->DESCRIPCION; ?></td>
          <?	
          if($reg->COSTONEGOCIADO==1)
          {
						?>
          <td>
			<?
				$con->cmbselectdata($Sql_moneda,"cmbmoneda$indice",$moneda,"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ","2")
			?>
          </td>
          <td width="10%" style="text-align:center"><input name="txtunidad<?=$indice; ?>" type="text" id="txtunidad" size="4" maxlength="2" class="classtexto" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" style="text-align:center;" onKeyPress="return validarnum(event)" value="<?=$unidad; ?>"></td>
          <td><?
          $con->cmbselect_ar("cmbmedida$indice",$lista_medida,$medida," class='classtexto' onfocus='coloronFocus(this);' onBlur='colorOffFocus(this);' ");
								?></td>
          <td width="10%" style="text-align:center"><input name="txtmonto<?=$indice; ?>" type="text" id="txtmonto" size="8" maxlength="7" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$monto; ?>"></td>
          <td width="10%" style="text-align:center"><? if($reg->APLICAFORANEO==1){ ?>
                <input name="txtforaneo<?=$indice; ?>" type="text" id="txtforaneo" size="8" maxlength="7" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$foraneo; ?>">
            <? } ?></td>
          <td width="10%" style="text-align:center"><? if($reg->APLICANOCTURNO==1){ ?>
                <input name="txtnocturno<?=$indice; ?>" type="text" id="txtnocturno" size="8" maxlength="7" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$nocturno; ?>">
            <? } ?></td>
          <td width="10%" style="text-align:center"><? if($reg->APLICAFESTIVO==1){ ?>
                <input name="txtfestivo<?=$indice; ?>" type="text" id="txtfestivo" size="8" maxlength="7" class="classtexto" onKeyPress="return numeroDecimal(event)" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$festivo; ?>">
            <? } ?></td>
          <?	 							
          }
          else
          {
						?>
          <td colspan="7" style="color:#FF4848"><strong>
            <?=_("COSTO NO NEGOCIADO");?>
          </strong></td>
          <?	 
          }
						?>
        </tr>
        <?	 
        $monto="";
        $moneda="";
        $unidad="";
        $medida="";
        $marcar="";
        $foraneo="";
        $nocturno="";
        $festivo="";
        }
					?>
      </table>
    </div></td>
  </tr>
</table>
		
				</td>
			</tr>		  
			<tr class='modo1'>
				<td colspan="2" align="right"><div align="center">
				  <input type="button" class="botonstandar" value="<?=_('CANCELAR');?>" onClick="reDirigir('general.php')" title="<?=_('CANCELAR');?>">				  
				  <input name="Submit" type="submit" class="botonactualizar" value="<?=_('GRABAR');?>" title="<?=_('GRABAR SERVICIO');?>" >
			    </div></td>
		  </tr>
        </table>
			<input name="pag" type="hidden" value="<?=$_GET['pag'];?>">
	</form>
	</body>
 </html>
 
 <script type="text/javascript">
 // ACTIVA / DESACTIVA LA ZONA DE CONCLUCION
	function act_zona_con(){
				$('zona_con').toggle();
			return;
	}
 </script>
 