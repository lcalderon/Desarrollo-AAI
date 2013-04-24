<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');

include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_unidadfederativa.inc.php');
include_once('../../../modelo/validar_permisos.php');
include_once('../../../modelo/clase_asistencia.inc.php');
include_once('../../includes/arreglos.php');
include_once("../../../vista/login/Auth.class.php");
  
Auth::required();
include_once('../../includes/head_prot_win.php');


if (isset($_GET[CVEPAIS]))
{
	$ubigeo->cvepais= $_GET[CVEPAIS];
	$ubigeo->cveentidad1 = ($_GET[CVEENTIDAD1]=='')?'0':$_GET[CVEENTIDAD1];
	$ubigeo->cveentidad2 = ($_GET[CVEENTIDAD2]=='')?'0':$_GET[CVEENTIDAD2];
	$ubigeo->cveentidad3 = ($_GET[CVEENTIDAD3]=='')?'0':$_GET[CVEENTIDAD3];
	$ubigeo->cveentidad4 = ($_GET[CVEENTIDAD4]=='')?'0':$_GET[CVEENTIDAD4];
	$ubigeo->cveentidad5 = ($_GET[CVEENTIDAD5]=='')?'0':$_GET[CVEENTIDAD5];
	$ubigeo->cveentidad6 = ($_GET[CVEENTIDAD6]=='')?'0':$_GET[CVEENTIDAD6];
	$ubigeo->cveentidad7 = ($_GET[CVEENTIDAD7]=='')?'0':$_GET[CVEENTIDAD7];
	$prov = new proveedor();
	$lista_prov = $prov->lista_proveedores($ubigeo,$_GET[IDFAMILIA],$_GET[IDSERVICIO],$_GET[INTERNO],$_GET[ACTIVO],$_GET[TEXTBUSQUEDA]);
}
else 
{
	$ubigeo->cvepais=$con->lee_parametro('IDPAIS');
	$ubigeo->cveentidad1='';
}

$con = new DB_mysqli();
?>
<body onload="inicio();">  
<font size="4px"><?=_("PROVEEDORES")?></font>
<form id='frm_buscar'>
		<fieldset>
			<table 	style="width:100%">
				<tr>
					<td width="35%"  class="busqueda"><?=_('ENTIDADES')?></td>
					<td width="10%"  class="busqueda"><?=_('INTERNO')?></td>
					<td width="25%"  class="busqueda"><?=_('FAMILIA DE SERVICIOS')?></td>
					<td width="20%"  class="busqueda"><?=_('NOMBRE FISCAL / COMERCIAL')?></td>
					<td width="10%" valign="top" rowspan="3" align="center">
					<img src="/imagenes/32x32/Search.png" title="<?=_('BUSCAR')?>" align='absbottom' border='1' style='cursor: pointer;' onclick="buscar();" />
					<img src="/imagenes/32x32/User.png" title="<?=_('NUEVO PROVEEDOR')?>" align='absbottom' border='1' style='cursor: pointer;' onclick="edit_proveedor('');" />
					</td>
				</tr>
				<tr>
					<td rowspan="9"  >
						<table id='vista_entidades' >
							<? include_once('../../includes/vista_entidades_ubigeo.php');?>	
						</table>
					</td>
					<td><select name='INTERNO' id='interno'>
							<option value='' <?=($_GET[INTERNO]=='')?'selected':'';?>><?=_('TODOS')?></option>
							<option value='1' <?=($_GET[INTERNO]=='1')?'selected':'';?>><?=_('INTERNOS')?></option>
							<option value='0' <?=($_GET[INTERNO]=='0')?'selected':'';?>><?=_('EXTERNOS')?></option>
						</select>
					</td>
					<td>
					<?
	  				 $sql="SELECT IDFAMILIA,DESCRIPCION FROM $con->catalogo.catalogo_familia ORDER BY 2 ";
					 $con->cmbselect_db('IDFAMILIA',$sql,$_GET[IDFAMILIA],"id='idfamilia'","onchange='servicios(this.value);'");?>
					</td>
					<td><input type="text" name="TEXTBUSQUEDA" id="query" size='40' value='<?=$_GET[TEXTBUSQUEDA]?>'/></td>
				</tr>
				<tr>
					<td><?=_('ACTIVO')?></td>
					<td><?=_('SERVICIOS')?></td>
				</tr>
				<tr>
					<td><select name='ACTIVO' id='activo'>
							<option value='' <?=($_GET[ACTIVO]=='')?'selected':'';?>><?=_('TODOS')?></option>
							<option value='1' <?=($_GET[ACTIVO]=='1')?'selected':'';?>><?=_('ACTIVOS')?></option>
							<option value='0' <?=($_GET[ACTIVO]=='0')?'selected':'';?>><?=_('DE BAJA')?></option>
						</select>
					</td>
					<td colspan="3"  >
							<select name='IDSERVICIO' id='idservicio' >
								<option value=''><?=_('TODOS')?></option>
							</select>
					</td>
				</tr>
		</table>
	</fieldset>
</form>

<div id="cargando" class="spinner" style="display:none;" >
			<img src="/imagenes/iconos/spinner.gif" align="center" />Cargando…
</div> 	
<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
  <thead>
       <tr>
          <th width="2%" class="nosort"><h3><?=_('ID')?></h3></th>
          <th width="10%"><h3><?=_('NOMBRE FISCAL')?></h3></th>
          <th width="10%"><h3><?=_('NOMBRE COMERCIAL ')?></h3></th>
          <th width="10%"><h3><?=_('DIRECCION')?></h3></th>
          <th width="2%"><h3><?=_('ACTIVO')?></h3></th>
          <th width="2%"><h3><?=_('INT/EXT')?></h3></th>
         <!-- <th width="5%" class="nosort"><h3><?=_('TELEFONO')?></h3></th>-->
       </tr>
  </thead>
  
<tbody>
    <?  foreach ($lista_prov as $idproveedor=>$datos) :?>
    	<?  $prov->carga_datos($idproveedor);  	?>
        	<tr onclick='edit_proveedor("<?=$prov->idproveedor?>");'>
    			<td><?=$prov->idproveedor?></td>
    			<td><?=$prov->nombrefiscal?></td>
				<td><?=$prov->nombrecomercial?></td>
    			<td><?=$prov->direccion?></td>
    			<td align="center"><?=($prov->activo)?_('ACTIVO'):_('DE BAJA')?></td>
    			<td align="center"><?=($prov->interno)?_('INTERNO'):_('EXTERNO')?></td>
    		</tr>
    <?endforeach;?>
</tbody>	

</table>
<div id="tablefooter" >
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
                	<a href="javascript:sorter.showall()"><font size="1px"><?=_('VER TODOS')?></font></a>
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
                    <span><?=_('ENTRADAS POR PAG.')?></span>
                </div>
                <div class="page"><?=_('PAG.')?>
               	 <span id="currentpage"></span> <?=_('DE')?> 
               	 <span id="totalpages"></span>  &nbsp;&nbsp;<?=_('TOTAL REG.')?> 
                 <span id="totalrecords"></span>
                </div>
               
            </div>
    </div><!--  fin del tablefooter-->
</body>
</html> 


<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">

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
		sortcolumn:2,
		sortdir:1,
		init:true
	});
	
	
function buscar()
{
	url=$('frm_buscar').serialize();
	reDirigir('/app/vista/catalogos/proveedores/consulta.php?'+url);
	return;
}

function servicios(idfamilia){
	new Ajax.Updater('idservicio','/app/controlador/ajax/ajax_servicios.php',
	{
		method:'post',
		parameters: { 
			IDFAMILIA: idfamilia,
			IDSERVICIO: '<?=$_GET[IDSERVICIO]?>'
		}
	}
	);
	return;
}



var win = null;


function edit_proveedor(idproveedor){

	if (idproveedor=='') parametro='?edicion=1';
	else
	parametro ='?idproveedor='+idproveedor+"&edicion=1";

	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			width: 930,
			height: 500,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "form_catprov.php"+parametro
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




function inicio(){
	
	servicios($F('idfamilia'));
	return;
}
</script>
