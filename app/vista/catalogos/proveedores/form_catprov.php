<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../includes/arreglos.php');

if (isset($_GET[idproveedor])){
    $prov = new proveedor();
    $prov->carga_datos($_GET[idproveedor]);
    $idproveedor = $_GET[idproveedor];


}

$edicion = (isset($_GET[edicion]))?$_GET[edicion]:0;


$con = new DB_mysqli();
$lista_pais = $con->uparray('SELECT IDPAIS,NOMBRE FROM catalogo_pais');
$lista_t_telf = $con->uparray("SELECT IDTIPOTELEFONO, DESCRIPCION FROM catalogo_tipotelefono");
$lista_tsp = $con->uparray("SELECT IDTSP,DESCRIPCION FROM catalogo_tsp");
$lista_servicios = $con->uparray("select IDSERVICIO,DESCRIPCION from catalogo_servicio");
$lista_ponderacion = $con->uparray('SELECT VALOR,DESCRIPCION FROM catalogo_ponderacion ORDER BY valor DESC');
$lista_sociedades = $con->uparray('SELECT cps.IDSOCIEDAD,cs.NOMBRE FROM catalogo_parametro_sociedad cps,catalogo_sociedad cs WHERE cs.IDSOCIEDAD = cps.IDSOCIEDAD ORDER BY cps.PRIORIDAD ASC');

$idusuariomod=$_SESSION[user];
$idextension= $_SESSION[extension];

$indice_serv= count($prov->servicios);

include_once("../../includes/head_prot_win_zap.php");
?>
<body>
<div  id='proveedor'  style="width:99%;"> 
<fieldset>
<legend><?=_('PROVEEDORES')?></legend>
  <form name='form_proveedor' id='form_proveedor' >
 	 <div id='datos_generales' style="width:58%;float:left">
 	 		<div id='botones' style="float:right">
				<input type='button' id='id_contactos' value='Contactos' onclick="contactos($F('idproveedor'));"  <?=isset($idproveedor)?'':'disabled';  ?> class="normal" >
				<input type='button' id='id_areas_servicio' value='Areas de servicio' onclick="areas_servicio($F('idproveedor'),0);" <?=isset($idproveedor)?'':'disabled'; ?> class="normal">
				<input type='button' id='id_horario' value='Horarios' onclick="horarios($F('idproveedor'));" <?=isset($idproveedor)?'':'disabled'; ?> class="normal">
				<input type='button' id='id_experiencia' value='Experiencia' onclick="experiencia($F('idproveedor'));" <?=isset($idproveedor)?'':'disabled'; ?> class="normal">
				<input type='button' id='id_foto' value='Foto' onclick="carga_foto($F('idproveedor'));" <?=isset($idproveedor)?'':'disabled'; ?> class="normal">
			</div>  <!--fin del div botones-->
			<? include('vista_datos_generales.php');?>
			 <div id='botones_del_form' style="float:right">
				<input type='button' value="<?=_('GUARDAR')?>" onclick='grabar();' 	class="guardar"  <?=($edicion==1)?'':'disabled'?>>
				<input type='button' value="<?=_('SALIR')?>" onclick="parent.win.close();reload();" class="cancelar">	
			</div>
 	 </div>  <!--fin ddel div='datos_generales'-->
 
	 <div id='Pestañas' style="width: 41%;float:right">
  		<div id="tabBar" ></div>
 			<div id="tabs" style="height: 45%">
 				<div id="SAP">
					<label><?=_('SAP')?></label>
					<? include_once('vista_sap.php');?>
				</div>    <!--fin del div SAP-->
				<div id="SERVICIOS" style="height:400px;overflow:auto;" >
					<label><?=_('SERVICIOS')?></label>
					<? 
					$sql="SELECT IDSERVICIO,DESCRIPCION FROM $con->catalogo.catalogo_servicio";
					$result = $con->query($sql);
					?>
					<table id='tblservicios' style="display:<?=($idproveedor!='')?'block':'none';?>">
					<?
					while ($reg=$result->fetch_object())
					{
					    if (in_array($reg->IDSERVICIO,$prov->servicios))
					    {
					        $check='checked';
					        $btn_costo ='';
					    }
					    else
					    {
					        $check='';
					        $btn_costo ='disabled';
					    }
						?>
						<tr class='modo1'>
						<td><input type='checkbox' name='CHECKSERVICIO[]' value='<?=$reg->IDSERVICIO?>' id='' onclick=act_des_btncosto('<?=$reg->IDSERVICIO?>') <?=$check?> ></td>
						<td><?=$reg->DESCRIPCION?></td>
						<td><input type='button'  onclick=areas_servicio('<?=$idproveedor?>','<?=$reg->IDSERVICIO?>') id='btnarea_<?=$reg->IDSERVICIO?>' value="<?=_('AREAS')?>" class='normal' <?=$btn_costo?>></td>
						<td><input type='button' onclick= costo($F('idproveedor'),'<?=$reg->IDSERVICIO?>')  id='btncosto_<?=$reg->IDSERVICIO?>' value="<?=_('COSTO')?>" class='normal' <?=$btn_costo?>></td>
						</tr>

					<?}?>
					</table>
				</div>  <!--fin del div SERVICIOS-->
				<div id="INFRAESTRUCTURA" style="display:none">
					<label><?=_('INFRAESTRUCTURA')?></label>		  
					<? include_once('vista_infraestructura.php')?>
				</div>  <!--fin del div INFRAESTRUCTURA--> 
			</div>		<!--fin del div TABS-->
		</div> 	<!--fin del div pestañas-->
		
	
  </form>
</fieldset>
</div>   <!--fin del div='proveedor'-->
</body>  
</html>

<script type="text/javascript">
var win = null;

var objTabs = new Zapatec.Tabs({
    // ID of Top bar to show the Tabs: Game, Photo, Music, Chat
    tabBar: 'tabBar',
    /*
    ID to get the LABEL contents to create the tabBar tabs
    Also, each DIV in this ID will contain the contents for each tab
    */
    tabs: 'tabs',
    // Theme to use for the tabs
    theme: 'rounded',
    themePath: '/librerias/zapatec/zptabs/themes/',
    closeAction: 'hide'
});


function ver_ddn(campo){

    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("CODIGOS DE AREA")?>',
            width: 270,
            height: 300,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: "listar_codigoarea.php?campo="+campo

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


}




// **********************  Ajax para autocompletar las calles ********************************//
new Ajax.Autocompleter('direccion',	'sugeridos','../../../controlador/ajax/ajax_calles.php',
{
    method: 'get',
    paramName: 'calle',
    callback: function(editor, paramText){
        parametros = "&cveentidad1="+ $F('cveentidad1')
        +"&cveentidad2="+$F('cveentidad2')
        +"&cveentidad3="+$F('cveentidad3')
        //+"&cveentidad4="+$F('cveentidad4')
        //+"&cveentidad5="+$F('cveentidad5')
        //+"&cveentidad6="+$F('cveentidad6')
        //+"&cveentidad7="+$F('cveentidad7');
        return  paramText+parametros;
    },
    afterUpdateElement: function(text,li){
        var coordenadas = li.id.split(',');
        $('latitud').value=coordenadas[0];
        $('longitud').value=coordenadas[1];
        //			$('cvetipovia').value=coordenadas[2];


    },
    minChars: 2,
    selectFirst: false
}
);


// ***********************  Abre la ventana del mapa  *****************************************//
new Event.observe('ver_mapa','click',function()
{
    var lat= $F('latitud');
    var lng= $F('longitud');

 //   if (lat==0 && lng==0) alert('<?=_('NO HAY NADA QUE MOSTRAR EN ESTE MAPA')?>');
  //  else
   // {
        if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
        else
        {
            win = new Window({
                className: "alphacube",
                title: '<?=_("MAPA DE LOCALIZACION")?>',
                width: 400,
                height: 400,
                showEffect: Element.show,
                hideEffect: Element.hide,
                resizable: false,
                destroyOnClose: true,
                minimizable: false,
                maximizable: false,
                url: "../../ubigeo/mapa_localizacion.php?lat="+lat+"&lng="+lng

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
  //  }
});


function horarios(idproveedor)
{
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("HORARIO DE ATENCION")?>',
            width: 280,
            height: 230,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: 'horarios.php?idproveedor='+idproveedor+'&edicion='+<?=$edicion?>

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
}

function experiencia(idproveedor)
{
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("EXPERIENCIA")?>',
            width: 450,
            height: 200,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: 'experiencia.php?idproveedor='+idproveedor+'&edicion='+<?=$edicion?>

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
}

function carga_foto(idproveedor)
{
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("IMAGEN")?>',
            width: 450,
            height: 200,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: true,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: 'foto/form_foto.php?idproveedor='+idproveedor

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
}



function contactos(idproveedor)
{
    //	alert(idproveedor);
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("CONTACTOS")?>',
            width: 600,
            height: 500,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: 'contacto.php?idproveedor='+idproveedor+'&edicion='+<?=$edicion?>

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
}






function areas_servicio(idproveedor,idservicio)
{
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("AREAS DE SERVICIO")?>',
            width: 800,
            height:420 ,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: '../../ubigeo/areas_servicio.php?idproveedor='+idproveedor+'&idservicio='+idservicio+'&edicion='+<?=$edicion?>

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

}



function grabar(){



    if ($F('nombrefiscal').length == 0) alert("<?=_('INGRESE EL NOMBRE FISCAL')?>");
    else if ($F('nombrecomercial').length==0) alert("<?=_('Ingrese el nombre comercial')?>");
    else if ($F('idtipodocumento')=='') alert("<?=_('Seleccione tipo de documento')?>");
    else if ($F('iddocumento').length==0) alert("<?=_('Ingrese documento de identidad')?>");
    else if ($F('direccion').length==0) alert("<?=_('Ingrese su direccion')?>");
    else if (($('id_arrevalranking_skill').checked==true) && !($F('skill')>=0 && $F('skill') <= 100)) alert("<?=_('Ingrese un valor de Skill entre 0 y 100')?>");
    else if ($F('idmoneda')=='') alert("<?=_('Seleccione la moneda ')?>");
    else if ($F('brsch')=='') alert("<?=_('Seleccione el Ramo ')?>");
    else if ($F('fdgrv')=='') alert("<?=_('Seleccione el Grp. Tesoreria')?>");
    else if ($F('zterm')=='') alert("<?=_('Seleccione Condicion de pago ')?>");
    else if ($F('mwskz')=='') alert("<?=_('Seleccione Indicador de impuesto ')?>");
    else if ($F('parvo')=='') alert("<?=_('Seleccione tipo de ubicacion ')?>");
    else if ($F('pavip')=='') alert("<?=_('Seleccione tipo de empresa ')?>");
    else
    {
        /*  validacion de la sociedad*/
        var sw_sociedad=1;
        var Lista=$$('input[a=sociedad]');
        var Arreglo = $A(Lista);
        Arreglo.each(function(el, indice){
            if (el.checked==true) {sw_sociedad=0; return}
        });
        if (sw_sociedad) alert("<?=_('No especifico la sociedad')?>");
        
        new Ajax.Request('../../../controlador/ajax/ajax_proveedor.php?opcion=nuevo',
        {
            method: 'post',
            parameters:  $('form_proveedor').serialize(true),
            onSuccess: function(t){
                //				alert(t.responseText);
                if (t.responseText=='0') alert("<?=_('Este proveedor ya existe')?>");
                else
                {
                    alert("<?=_('LOS DATOS SE GRABARON CORRECTAMENTE ')?>");
                    $('idproveedor').value = t.responseText;
                    $('id_areas_servicio').disabled = false;
                    $('id_horario').disabled = false;
                    $('id_experiencia').disabled = false;
                    $('id_contactos').disabled = false;
                    $('tblservicios').style.display='block';


                }
            }
        });
    }
    return;
}



function act_des_btncosto(id){

    if ($('btncosto_'+id).disabled == false) {
        $('btncosto_'+id).disabled=true;
        $('btnarea_'+id).disabled=true;

    }
    else{
        $('btncosto_'+id).disabled=false;
        $('btnarea_'+id).disabled=false;
    }

    return;
}



function costo(idproveedor,idservicio)
{

    var moneda = $F('idmoneda');
    if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
    else
    {
        win = new Window({
            className: "alphacube",
            title: '<?=_("COSTOS NEGOCIADOS")?>',
            width: 850,
            height: 250,
            showEffect: Element.show,
            hideEffect: Element.hide,
            resizable: false,
            destroyOnClose: true,
            minimizable: false,
            maximizable: false,
            url: 'prov_serv_cost.php?idproveedor='+idproveedor+'&idservicio='+idservicio+'&idmoneda='+moneda+'&edicion='+<?=$edicion?>

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
