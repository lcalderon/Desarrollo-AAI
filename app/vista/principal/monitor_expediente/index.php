<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/validar_permisos.php');
include_once('../../../modelo/clase_asistencia.inc.php');
include_once('../../includes/arreglos.php');
include_once("../../../vista/login/Auth.class.php");

Auth::required();
include_once('../../includes/head_prot_win.php');

$asis = new asistencia();


$fecha_act =getdate(); // fecha actual

/* datos por default  */
$etapass = $asis->uparray("SELECT IDETAPA,DESCRIPCION FROM $asis->catalogo.catalogo_etapa ");

$idasistencia= (isset($_POST[IDASISTENCIA]))?$_POST[IDASISTENCIA]:'';

$coordinador_logeado= $_SESSION[user];

$user= (isset($_POST[IDUSUARIO]))?$_POST[IDUSUARIO]:$_SESSION[user];
$arrstatusexpediente = 'PRO';// (isset($_POST[ARRSTATUSEXPEDIENTE])?$_POST[ARRSTATUSEXPEDIENTE]:'PRO');
$arrstatusasistencia = (isset($_POST[ARRSTATUSASISTENCIA])?$_POST[ARRSTATUSASISTENCIA]:'');
$modo='';
$statusautorizaciondesvio=0;
/* FECHA 1 */
$annio=(isset($_POST[ANNIO]))?$_POST[ANNIO]:$fecha_act[year];
$mes = (isset($_POST[MES]))?$_POST[MES]:'';
$dia = (isset($_POST[DIA]))?$_POST[DIA]:'';
$hora = (isset($_POST[HORA]))?$_POST[HORA]:'';

/* FECHA 2 */
$annio2=(isset($_POST[ANNIO2]))?$_POST[ANNIO2]:'';
$mes2 = (isset($_POST[MES2]))?$_POST[MES2]:'';
$dia2 = (isset($_POST[DIA2]))?$_POST[DIA2]:'';
$hora2 = (isset($_POST[HORA2]))?$_POST[HORA2]:'';


if ($_POST[IDETAPA]){
	foreach ($_POST[IDETAPA] as $etapa) if ($etapa!='') $etapas[] ="'$etapa'";
	$idetapas =(isset($etapas))?implode(',',$etapas):'';
}
else{
	foreach ($etapass as $indice=>$etapa) if ($indice!='') $etapas_todas[] ="'$indice'";
	$idetapas =(isset($etapas_todas))?implode(',',$etapas_todas):'';
}

/* determinar las cuentas permitidas al usuario logeado */
$sql="SELECT IDUSUARIO, TODOCUENTAS FROM $asis->catalogo.catalogo_usuario WHERE IDUSUARIO='$coordinador_logeado';";
$result=$asis->query($sql);
$sw=0;
while($reg=$result->fetch_object()){
	if ($reg->TODOCUENTAS==1) $sw=1;
}
if ($sw==0) $cuentas_permitidas=$asis->uparray("SELECT sac.IDCUENTA,cc.NOMBRE FROM $asis->temporal.seguridad_acceso_cuenta sac, $asis->catalogo.catalogo_cuenta cc WHERE sac.IDCUENTA = cc.IDCUENTA AND sac.IDUSUARIO='$coordinador_logeado'");
else
$cuentas_permitidas=$asis->uparray("SELECT IDCUENTA,NOMBRE FROM $asis->catalogo.catalogo_cuenta");

if ($_POST[IDCUENTA])
{
	foreach ($_POST[IDCUENTA] as $cuenta) if ($cuenta!='')$cuentas[] ="'$cuenta'";
	$idcuentas = (isset($cuentas))?implode(',',$cuentas):'';
}
else
{
	foreach ($cuentas_permitidas as $indice=>$nombre) if ( $indice!='') $cta_per[]="'$indice'";
	$idcuentas =(isset($cta_per))?implode(',',$cta_per):'';
}

/* determina los usuarios que deben */

if ($_POST[IDUSUARIO])
{
	foreach ($_POST[IDUSUARIO] as $usuario)
	if ($usuario!=''){
		$usuarios[]="'$usuario'";
		$usuarios_selec[] =$usuario;
	}
	
	//var_dump($_POST[IDUSUARIO]);
	$idusuarios = (isset($usuarios))?implode(',',$usuarios):'';
	
}
else
{
	$idusuarios="'$user'";
	$usuarios_selec[]=$user;

}
$sql="
SELECT 
	cu.IDUSUARIO,
	SUBSTR(CONCAT(cu.NOMBRES,' ',cu.APELLIDOS),1,25) AS NOM
FROM 
	$asis->catalogo.catalogo_usuario cu,
	$asis->temporal.grupo_usuario cgu
WHERE
 cu.IDUSUARIO = cgu.IDUSUARIO
 AND cgu.IDGRUPO='CORD'
 AND cu.ACTIVO=1
";


$coordinadores = $asis->uparray($sql);

$servicio = $asis->uparray("SELECT IDSERVICIO,DESCRIPCION from catalogo_servicio");
//$proveedor = $asis->uparray("SELECT IDPROVEEDOR,NOMBRECOMERCIAL from catalogo_proveedor");

if ($_POST[COLUMNAGEN]!='' && $_POST[TEXTCOLUMNAGEN]!='')
{
	$campo=$_POST[COLUMNAGEN];
	$dato= $_POST[TEXTCOLUMNAGEN];
}
else
{
	$campo=$_POST[COLUMNA];
	$dato= $_POST[TEXTCOLUMNA];
}


/*  lista de asistencias  a presentar en el grid */
$lista_asis = $asis->listar_asistencias($idusuarios,$arrstatusexpediente,$arrstatusasistencia,$idetapas,$idcuentas,$modo,$statusautorizaciondesvio,$annio,$mes,$dia,$hora,$annio2,$mes2,$dia2,$hora2,$idasistencia,'',$campo,$dato,$_POST["TODASCUENTAS"]);

if(!validar_permisos("ACCESOTODOS_ASISTENCIAS"))	$opcs=2;	else  $opcs="";

/*****    Consultar asistencias pendientes por aceptar-delegacion *****/

$Sql_asist_respo="SELECT
                  COUNT(*)
                FROM ($asis->temporal.asistencia a,
                   $asis->temporal.expediente e)
                WHERE a.IDEXPEDIENTE = e.IDEXPEDIENTE
                    AND e.ARRSTATUSEXPEDIENTE = 'PRO'
                    AND a.ARRSTATUSASISTENCIA = 'PRO'
                    AND a.USUARIODELEGAHACIA = '".$_SESSION["user"]."'
                    AND a.DELEGAR = 1
                    AND a.STATUSAUTORIZACIONDESVIO = 0";

$asis_pendientes= $con->consultation($Sql_asist_respo);

?>
<body onload="inicio();"  >
<div style="width:100%" >
<form  id='datos_busqueda' method='post'>
 <span><font size="4px"><?=_("ASISTENCIAS BRINDADAS Y EN PROCESO")?></font>
	 <span style="float:right;">
 		<select name="COLUMNAGEN" id='columnagen'>
 			<option value='a.IDASISTENCIA' <?=($_POST[COLUMNAGEN]=='a.IDASISTENCIA')?'selected':'';?>><?=_('ASISTENCIA')?></option>
			<option value='a.IDEXPEDIENTE' <?=($_POST[COLUMNAGEN]=='a.IDEXPEDIENTE')?'selected':'';?>><?=_('EXPEDIENTE')?></option>
 		</select>
 		<input type='text' name='TEXTCOLUMNAGEN' id='textcolumnagen' size="15" >
 		<img src="/imagenes/32x32/Search.png" alt='16px' height="16px" align='absbottom' style='cursor: pointer;' onclick="buscar_gen();"/>
 	</span>
 </span>
 <fieldset>
   
       	<table style="width:100%" >
        		<tr>
        			<td ><?=_('BUSCAR EN COLUMNAS')?></td>
    	    		<td style="display:none"><?=_('EXPEDIENTE')?></td>
    	    		<td ><?=_('ASISTENCIA')?></td>
    	    		<td ><?=_('FECHA')?></td>
    	    		<td colspan="2"></td>
    	    		<td rowspan="2" valign="middle">
    	    		<? if(validar_permisos("EXPED_NUEVO")) {?> 
		             	<img src='/imagenes/iconos/nuevo_expediente.gif' title="<?=_('NUEVO EXPEDIENTE')?>" align='absbottom' border='0' style='cursor: pointer;' onClick="window.open('../../expediente/entrada/expediente_frmexpediente.php','EXPEDIENTE')" />
        				<?} ?>
    	    		</td>
    	   		</tr>	
    	   		<tr>
	    	   		<td valign="top" >
						<select name='COLUMNA' id="columna">
							<option value='NOM_PROVEEDOR' <?=($_POST[COLUMNA]=='NOM_PROVEEDOR')?'selected':'';?>><?=_('PROVEEDOR')?></option>
							<option value='NOM_AFILIADO' <?=($_POST[COLUMNA]=='NOM_AFILIADO')?'selected':'';?>><?=_('AFILIADO')?></option>
							<option value='NOM_SERVICIO' <?=($_POST[COLUMNA]=='NOM_SERVICIO')?'selected':'';?>><?=_('SERVICIO')?></option>
						</select>
    	    			<input type="text"  name="TEXTCOLUMNA" id="textcolumna" size='40'  value="<?=$_POST[TEXTCOLUMNA]?>"/>
    	       		</td>
               		<td style="display:none" valign="top"><? $asis->cmbselect_ar('ARRSTATUSEXPEDIENTE',$desc_status_expeduiente,"$arrstatusexpediente",'id="arrstatusexpediente"',"onchange=cambio_option()","" ); ?></td>
              		<td valign="top">
               		<select name="ARRSTATUSASISTENCIA" id="arrstatusasistencia" >
               		  <option value='' id='todos'><?=_('TODOS')?></option>
               		  <?foreach($desc_status_asistencia as $indice=>$statusasistencia):?>
               		   <? if ($arrstatusasistencia==$indice) $selected='selected';	else $selected='';?>
              			<option value="<?=$indice?>" id="<?=$indice?>" <?=$selected?>><?=$statusasistencia ?></option>
              		  <?endforeach;?>
               		</select>
               		</td>
               		<td valign="top">
           		
               		<?
               		$asis->cmbselect_anio('ANNIO',$fecha_act[year],6,$annio,"id='annio'",'');
               		$con->cmbselect_ar('MES',$mes_del_anio,$mes,'',"id='mes'",'MES');
               		?>
               		<select name="DIA">
               			<option value=""><?=_('DIA')?></option>
               			<?for($d=1;$d<=31;$d++):?>
               				<? if ($dia==$d) $selected='selected'; else  $selected='';?>
               				<option value="<?=$d?>" <?=$selected?>><?=$d?></option>
               			<?endfor;?>
               		</select>
               		<select name="HORA">
               			<option value=""><?=_('HORA')?></option>
               			<?for($h=0;$h<=23;$h++):?>
               				<? if ($hora=="$h") $selected='selected'; else  $selected='';?>
               				<option value="<?=$h?>" <?=$selected?>><?=$h?></option>
               			<?endfor;?>
               		</select>
               		<img src="/imagenes/iconos/collapse-expand2.GIF" onclick="$('zona_fecha2').toggle();"></img>
               		<div id='zona_fecha2' style="display:<?=($annio2!='' or $mes2!='' or $dia2!='' or $mes2!='' or $hora2!='' )?'block':'none'?>">
               		<?=_('HASTA')?><br>
               		<?
               		$asis->cmbselect_anio('ANNIO2',$fecha_act[year],6,$annio2,"id='annio2'",'');
               		$con->cmbselect_ar('MES2',$mes_del_anio,$mes2,'',"id='mes2'",'MES');
               		?>
               		<select name="DIA2">
               			<option value=""><?=_('DIA')?></option>
               			<?for($d=1;$d<=31;$d++):?>
               				<? if ($dia2==$d) $selected='selected'; else  $selected='';?>
               				<option value="<?=$d?>" <?=$selected?>><?=$d?></option>
               			<?endfor;?>
               		</select>
               		<select name="HORA2">
  	             		<option value=""><?=_('HORA')?></option>
    	           		<?for($h=0;$h<=23;$h++):?>
        	       			<? if ($hora2=="$h") $selected='selected'; else  $selected='';?>
            			<option value="<?=$h?>" <?=$selected?>><?=$h?></option>
               			<?endfor;?>
               		</select>           	  	
               		</div>
               		</td>
                  </tr>
               	  <tr valign="top">
               	  	<td colspan="8">&nbsp;</td>
               	  </tr>
         </table>
            <!--tabla de elemento de la busqueda avanzada-->
         <table style="width:100%;" >
            	<tr>
            		<td >
  	          		<?=_('CUENTAS')?>
            		<?  $checked='checked';
            		if ($_POST[IDCUENTA])
            		if ($_POST[TODASCUENTAS]!='on') $checked='';
 	         		?>
 	         		->&nbsp;&nbsp;
            		<?=_('TODOS')?><input type="checkbox"  name='TODASCUENTAS' id='todascuentas' onclick="marcar_todascuentas()" <?=$checked?>></input>
              		</td>
              		
            		<td >
            		<?=_('ETAPAS')?>
  	           		<?  $checked='checked';
  	           		if ($_POST[IDETAPA])	if ($_POST[TODASETAPAS]!='on') $checked='';
 	         		?>
 	         		->&nbsp;&nbsp;
  	         		<?=_('TODOS')?><input type="checkbox"  name='TODASETAPAS' id='todasetapas' onclick="marcar_todasetapas()"  <?=$checked?>></input>
            		</td>
            		
            		<td >
            		<?=_('USUARIOS')?>
            		<?  $checked='';
            		if ($_POST[IDUSUARIO])
            		if ($_POST[TODOUSUARIOS]=='on') $checked='checked';
            		?>
            		->&nbsp;&nbsp;
            		<?=_('TODOS')?><input type="checkbox"  name='TODOUSUARIOS' id='todousuarios' onclick="marcar_todousuarios()"  <?=$checked?>></input>
 	         		</td>
 	         		
 	         		<td colspan="2">&nbsp;</td>
            	</tr>
               	<tr>
               		<td >
               		<select name='IDCUENTA[]' multiple size=4 id='idcuentas'>
               	    <?foreach($cuentas_permitidas as $indice=>$cta):?>
               	   	   <? if (in_array($indice,$_POST['IDCUENTA'])) $selected='selected';	else $selected='';?>
               	   	  <option value="<?=$indice?>" id="<?=$indice?>" <?=$selected?> onclick="desactiva_check('todascuentas')"><?=$cta?></option>
               	   	 <?endforeach;?>
               	   	</select>
               		</td>
               		
               		<td >
               		<select name='IDETAPA[]' multiple size='4' id='idetapas'>
               		<?foreach($etapass as $indice=>$etapa):?>
               		  <? if (in_array($indice,$_POST['IDETAPA'])) $selected='selected'; else $selected='';?>
               		 <option value="<?=$indice?>" id="<?=$indice?>" <?=$selected?> onclick="desactiva_check('todasetapas')"><?=$etapa?></option>
              		  <?endforeach;?>
               		</select>
               	   	</td>
               	   	
               	   	<td >
               	   	<select name='IDUSUARIO[]' multiple size='4' id='idusuarios'>
               	   	<?foreach($coordinadores as $indice=>$coordinador):?>
               	   	<? if (in_array($indice,$usuarios_selec)) $selected='selected'; else $selected='';?>
               		 <option value="<?=$indice?>" id="<?=$indice?>" <?=$selected?> onclick="desactiva_check('todousuarios') "><?=$coordinador?></option>
               	   	<?endforeach;?>
               	   	</select>
               	   	</td>
               	   	
               	   	<td valign="top" align="right"><img src='/imagenes/64x64/Search.png' onclick="buscar();" title="<?=_('BUSCAR')?>" align='absbottom' border='1' style='cursor: pointer;' /></td>            	
      			   	<td align="center">
            			<input type="button" name="button" id="button" style="color:<?=($asis_pendientes[0][0]>0)?"#FFFFFF":""?>;background-color:<?=($asis_pendientes[0][0]>0)?"#E80000":""?>;" value="DELEGAR<?="[".$asis_pendientes[0][0]."]";?>" title="<?=$asis_pendientes[0][0]." "._('Asistencia(s) por aceptar');?> " class='guardar' onClick="window.open('../delegar/gestion_delegar.php','VentaDelegar')" >
        			</td>
        		</tr>
         	</table>	   	
    
  </fieldset>
  </form>
</div>
<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable" width="100%">
  <thead>
      <tr>
          <th width="2%"><h3><?=_('EXP')?></h3></th>
          <th class="desc"  width="2%"><h3><?=_('ASIS')?></h3></th>
          <th width="2%"><h3><?=_('CTA')?></h3></th>
          <th width="2%"><h3><?=_('PLAN')?></h3></th>
          <th width="10%"><h3><?=_('AFILIADO')?></h3></th>
          <th width="15%"><h3><?=_('SERVICIO')?></h3></th>
          <th width="15%"><h3><?=_('PROVEEDOR')?></h3></th>
          <th width="2%" title='<?=_('CONDICION DEL SERVICIO')?>'><h3><?=_('CON')?></h3></th>
          <th width="2%" title='<?=_('PRIORIDAD DEL SERVICIO')?>'><h3><?=_('PRI')?></h3></th>
          <th width="2%"><h3><?=_('ESTADO')?></h3></th>
          <th width="5%"><h3><?=_('ETAPA')?></h3></th>
          <th width="2%" title='<?=_('USUARIO RESPONSABLE')?>'><h3><?=_('RESP.')?></h3></th>
          <th width="2%"><h3><?=_('FECHA')?></h3></th>
          <th width="2%" class="nosort"><h3><?=_('OPCIONES')?></h3></th>
       </tr>
  </thead>
  
  <tbody >
    <?	
    while ($reg=$lista_asis->fetch_object())
    {
    	$varexis=crypt($reg->IDEXPEDIENTE,"666");

    	echo "<tr ondblclick=\"abrir_pestana('$reg->IDEXPEDIENTE','$varexis')\">";
    	echo "<td>$reg->IDEXPEDIENTE</td>";
    	echo "<td>$reg->IDASISTENCIA</td>";
    	echo "<td title='$reg->NOM_CUENTA'>$reg->IDCUENTA</td>";
    	echo "<td title='$reg->NOM_PROGRAMA'>$reg->IDPROGRAMA</td>";
    	echo "<td>$reg->NOM_AFILIADO</td>";
    	$nombre_servicio =($reg->NOM_SERVICIO=='')?$reg->AA_SERVICIO:$reg->NOM_SERVICIO;
    	echo "<td>$nombre_servicio</td>";
    	echo "<td>".$reg->NOM_PROVEEDOR."</td>";
    	echo "<td title='{$desc_cobertura_servicio[$reg->ARRCONDICIONSERVICIO]}'>$reg->ARRCONDICIONSERVICIO</td>";
    	echo "<td title='{$desc_prioridadAtencion[$reg->ARRPRIORIDADATENCION]}'>$reg->ARRPRIORIDADATENCION</td>";
    	echo "<td>".(in_array($reg->ARRSTATUSASISTENCIA,array('CM','CP','CON','PRO'))?$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA]:'S/D')."</td>";
    	echo "<td>".(in_array($reg->ARRSTATUSASISTENCIA,array('CM','CP','CON'))?'TERMINADO':$reg->NOM_ETAPA)."</td>";
    	echo "<td>$reg->IDUSUARIORESPONSABLE</td>";
    	echo "<td>$reg->FECHAHORA</td>";
    	echo "<td><img src='/imagenes/iconos/editars.gif' title='Editar' align='absbottom' border='0' style='cursor: pointer;' onclick=abrir_pestana('$reg->IDEXPEDIENTE','$varexis') >
    	<img src='/imagenes/iconos/bitacora.PNG' alt='12px' width='12px' title='bitacora' align='absbottom' border='0' style='cursor: pointer;' onclick=bitacora('$reg->IDASISTENCIA') >
    	";
    	echo "</tr>	";
    }
	?>
</tbody>	
</table>
	 <div id="tablefooter">
          <div id="tablenav">
            	<div>
                    <img src="/librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                    <img src="/librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                    <img src="/librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
                    <img src="/librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
                </div>
                <div>
                	<select id="pagedropdown"></select>
				</div>
                <div>
                	<a href="javascript:sorter.showall()"><?=_('Ver Todos')?></a>
                </div>
            </div>
			<div id="tablelocation">
            	<div>
                    <select onchange="sorter.size(this.value)">
                    <option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span><?=_('Entradas por pagina')?></span>
                </div>
                <div class="page"><?=_('Pagina')?> <span id="currentpage"></span> <?=_('de') ?><span id="totalpages"></span> <?=_('Total reg.')?> <span id='totalrecords'></span></div>
            </div>
        </div>
    </div>
   
<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">
var win = null;

var sorter = new TINY.table.sorter('sorter','table',{
	headclass:'head',
	ascclass:'asc',
	descclass:'desc',
	evenclass:'evenrow',
	oddclass:'oddrow',
	evenselclass:'evenselected',
	oddselclass:'oddselected',
	paginate:true,
	size:10,
	currentid:'currentpage',
	totalid:'totalpages',
	totalrecid:'totalrecords',
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	navid:'tablenav',
	sortcolumn:12,
	sortdir:1,
	init:true
});

function bitacora(idasistencia){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title:"<?=_('BITACORA')?>",
			width: 600,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/bitacora/bitacora.php?idasistencia="+idasistencia
		});

		win.keepMultiModalWindow = true;
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

function buscar()
{
	$('columnagen').value='';
	$('textcolumnagen').value='';
	url=$('datos_busqueda').serialize();
	$('datos_busqueda').submit();
	//reDirigir('/app/vista/principal/monitor_expediente/index.php?'+url);
	parent.window.frames['ifr_alerta'].carga();
	return;
}

function buscar_gen()
{
	if (trim($F('textcolumnagen'))=='') alert("<?=_('INGRESE EL VALOR A BUSCAR')?>");
	else 
	{
		url=$('datos_busqueda').serialize();
		$('datos_busqueda').submit();
		//reDirigir('/app/vista/principal/monitor_expediente/index.php?'+url);
		parent.window.frames['ifr_alerta'].carga();
	}
	return;
}



function cambio_option(){
	if ($F('arrstatusexpediente')=='CER'){
		$('PRO').disabled=true;
		$('TOD').selected=true;
	}
	else $('PRO').disabled=false;
	return
}

function marcar_todasetapas(){
	var Lista=document.getElementById('idetapas');
	var Arreglo = $A(Lista);

	if ($('todasetapas').checked)
	Arreglo.each(function(el, indice){
		el.selected=true;
	});
	else
	Arreglo.each(function(el, indice){
		el.selected=false;
	});
	return;
}

function marcar_etapasseleccionadas(){
	var Lista=document.getElementById('idetapas');
	var Arreglo = $A(Lista);

	if ($('todasetapas').checked)
	Arreglo.each(function(el, indice){
		el.selected=true;
	});

	return;
}

function marcar_todascuentas(){
	var Lista=document.getElementById('idcuentas');
	var Arreglo = $A(Lista);

	if ($('todascuentas').checked)
	Arreglo.each(function(el, indice){
		el.selected=true;
	});
	else
	Arreglo.each(function(el, indice){
		el.selected=false;
	});
	return;
}

function marcar_cuentasseleccionadas(){
	var Lista=document.getElementById('idcuentas');
	var Arreglo = $A(Lista);

	if ($('todascuentas').checked)
	Arreglo.each(function(el, indice){
		el.selected=true;
	});
	return;
}


function marcar_todousuarios(){
	var Lista=document.getElementById('idusuarios');
	var Arreglo = $A(Lista);

	if ($('todousuarios').checked)
	Arreglo.each(function(el, indice){
		el.selected=true;
	});
	else
	Arreglo.each(function(el, indice){
		el.selected=false;
	});
	return;
}


function marcar_usuariosseleccionados(){
	var Lista=document.getElementById('idusuarios');
	var Arreglo = $A(Lista);

	if ($('todousuarios').checked)
	{
		Arreglo.each(function(el, indice)
		{
			el.selected=true;
		});
	}



	return;
}


function desactiva_check(id){
	$(id).checked=false;
	return;
}


function abrir_pestana(idexpediente,varexis)
{
	var url= '../../expediente/entrada/expediente_frmexpediente.php?idexpediente='+idexpediente+'&origen=PROCESODEXP&varexis='+varexis;
	window.open(url,'EXPEDIENTE_'+idexpediente);
	return ;
}

function inicio(){
	marcar_etapasseleccionadas();
	marcar_cuentasseleccionadas();
	cambio_option();
	sorter.search('estado');
	parent.window.frames['ifr_alerta'].carga();

	return;
}

</script>


</body>
</html> 