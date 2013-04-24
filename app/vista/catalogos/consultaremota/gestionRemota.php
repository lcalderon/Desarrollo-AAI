<?php
 
	session_start(); 
 
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	$con->select_db($con->catalogo);	
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	Auth::required($_SERVER['REQUEST_URI']);
 
    list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	if($_POST["cbmcuenta"])	$ver_cuentas="catalogo_cuenta.IDCUENTA='".$_POST["cbmcuenta"]."' AND ";
 
	if($allcuentas==1)	$Sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $Sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
    
    $Sql_programa="SELECT IDPROGRAMA,NOMBRE FROM catalogo_programa where IDCUENTA='".$_POST["cbmcuenta"]."' ORDER BY NOMBRE ";
 
    if(!$_POST["txtexpediente"])	
		$sqlcondi="DATE(expediente.FECHAREGISTRO) = '".$_POST["txtinicio"]."'"; 		
	else 
		$sqlcondi="expediente.IDEXPEDIENTE = '".$_POST["txtexpediente"]."' ";
	
 $Sql="SELECT
          /* BUSCAR EXPEDIENTE -CONSULTA REMOTA*/ 
          expediente.IDEXPEDIENTE,
          expediente.IDAFILIADO,
          expediente.ARRSTATUSEXPEDIENTE,
          expediente_persona.IDPERSONA,
          CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE) AS titular,
          (SELECT
             CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE)
           FROM $con->temporal.expediente_persona
           WHERE expediente_persona.ARRTIPOPERSONA = 'CONTACTO'
               AND expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE) AS contacto,
          catalogo_cuenta.NOMBRE       AS cuenta,
          catalogo_programa.NOMBRE     AS plan,
          expediente.FECHAREGISTRO,
		  CONCAT(catalogo_tipodocumento.DESCRIPCION,'-',expediente_persona.IDDOCUMENTO) AS DOCUMENTOS
        FROM $con->temporal.expediente
          INNER JOIN $con->temporal.expediente_persona
            ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE 
          INNER JOIN $con->temporal.asistencia
            ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE
          INNER JOIN $con->catalogo.catalogo_cuenta
            ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
          INNER JOIN $con->catalogo.catalogo_programa
            ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA  
		  LEFT JOIN $con->catalogo.catalogo_tipodocumento
            ON catalogo_tipodocumento.IDTIPODOCUMENTO = expediente_persona.IDTIPODOCUMENTO
        WHERE $ver_cuentas $sqlcondi
            AND asistencia.ARRSTATUSASISTENCIA LIKE '".$_POST["cmbstatus"]."%'
	        AND asistencia.ARRCONDICIONSERVICIO LIKE '".$_POST["cmbcondicion"]."%'
	        AND (expediente_persona.APPATERNO LIKE '%".$_POST["txtnombre"]."%' OR expediente_persona.NOMBRE LIKE '%".$_POST["txtnombre"]."%' OR expediente_persona.APMATERNO LIKE '%".$_POST["txtnombre"]."%')
	        AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'    
	        AND expediente.IDPROGRAMA  LIKE '".$_POST["cmbprogramatitular"]."%'  /*AND expediente.ARRSTATUSEXPEDIENTE='CER'*/
	        GROUP BY expediente.IDEXPEDIENTE";
         
	if($_POST["btnbuscar"]){
	
        $result=$con->query($Sql);    
        $numreg=$result->num_rows*1;       
	}
 
//verificar permisos de accesos a las cuentas	 
	 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consulta Remota</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">	
		
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
	<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
	<style type="text/css">@import url("../../../../librerias/jscalendar-1.0/calendar-system.css");</style>
	
	<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />	
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">	
	<style type="text/css">
	<!--
	.style1 {font-size: 10px}
	-->
	</style>	
 
</head>
<body>
<?
//visualizar el logo de pruebas
    if($con->logoMensaje){
?>
    <div  id='en_espera'><? include("../../avisosystem.php");?> </div><br> 
<? } ?>
<h2 class="Box"><?=_("CRITERIO DE BUSQUEDA") ;?></h2>
<form id="form1" name="form1" method="post" action="gestionRemota.php">
	<input type="hidden" name="buscarafiliado" value="<?=$_REQUEST["buscarafiliado"];?>" />
	
  <table width="75%" border="0" cellpadding="1" cellspacing="1" bgcolor="" style="border:1px solid #999999">
    <tr bgcolor="#A8D3FF">
      <td colspan="4" bgcolor="#336699" style="color:#FFFFFF"> 
        <strong>
       <span class="style1"><?=_("CONSULTA DE EXPEDIENTE") ;?></span></strong></td>
    </tr>
<? if($_SESSION["user"]=="EXNUSERRIM"){?>
    <tr>
      <td><?=_("CUENTA") ;?></td>
      <td><? $con->cmbselectdata($Sql_cuenta,"cbmcuenta",$_POST["cbmcuenta"]," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2");?></td>
      <td><?=_("PLAN") ;?></td>
      <td><div id="div-programa">
           <? $con->cmbselectdata("SELECT IDPROGRAMA,NOMBRE FROM catalogo_programa where IDPROGRAMA='RSD' ORDER BY NOMBRE","cmbprogramatitular","RSD"," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2"); ?>
           </div></td>
    </tr>

<? } else if($_SESSION["user"]=="EXNUSER02RIM"){?>
    <tr>
      <td><?=_("CUENTA") ;?></td>
      <td><? $con->cmbselectdata($Sql_cuenta,"cbmcuenta",$_POST["cbmcuenta"]," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2");?></td>
      <td><?=_("PLAN") ;?></td>
      <td><div id="div-programa">
           <? $con->cmbselectdata("SELECT IDPROGRAMA,NOMBRE FROM catalogo_programa where IDPROGRAMA='RCH' ORDER BY NOMBRE","cmbprogramatitular","RSD"," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2"); ?>
           </div></td>
    </tr>

<? } else{?>
    <tr>
      <td><?=_("CUENTA") ;?></td>
      <td><? $con->cmbselectdata($Sql_cuenta,"cbmcuenta",$_POST["cbmcuenta"],"onchange=\"show_divs('mostrarprograma.php',this.value,'div-programa','$row->IDPROGRAMA','"._("TODOS")."')\"; onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","",_("TODOS")." >>>");?></td>
      <td><?=_("PLAN") ;?></td>
      <td><div id="div-programa">
           <? $con->cmbselectdata($Sql_programa,"cmbprogramatitular",$_POST["cmbprogramatitular"]," onchange='verifica_cuenta()';onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","",_("TODOS")." >>>"); ?>
           </div></td>
    </tr>
<? } ?>	
    <tr>
      <td><?=_("FECHA REGISTRO") ;?></td> 
      <td > 
    	  <input name="txtinicio" id="f_date_b" type="text" size="14" readonly class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=($_POST["txtinicio"])?$_POST["txtinicio"]:date("Y-m-d"); ?>" ><button type="reset" id="f_trigger_b">...</button>
        
          <? //=_("AL") ;?>
    	  <!-- input name="txtfinal" id="f_date_b2" type="text" size="14" readonly class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?//=($_POST["txtfinal"])?$_POST["txtfinal"]:date("Y-m-d"); ?>" ><button type="reset" id="f_trigger_b2">...</button -->      </td> 
      <td><?=_("STATUS ASIS.") ;?></td> 
      <td><? $con->cmb_array("cmbstatus",$desc_status_asistencia,$_POST["cmbstatus"]," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS")." >>>","");?></td>
    </tr>
    <tr>
      <td><?=_("NRO EXPEDIENTE") ;?></td>
      <td>
        <input name="txtexpediente" type="text" id="txtexpediente" size="15" maxlength="15" onKeyPress="return validarnumero(event)" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' value="<?=$_POST["txtexpediente"]; ?>" /> <font color="red">* <?=_("Busqueda Exacta");?></font>       </td>
       <td><?=_("CONDICION SERV.") ;?></td>
       <td><? $con->cmb_array("cmbcondicion",$desc_cobertura_servicio,$_POST["cmbcondicion"]," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","",_("TODOS")." >>>","","PRO");?></td>
    </tr>
    
    <tr>
       <td><?=_("NOMBRE TITULAR") ;?>             </td>
       <td><input name="txtnombre" type="text" id="txtnombre" size="40" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' value="<?=$_POST["txtnombre"]; ?>" /></td>
       <td colspan="2"><input type="submit" name="btnbuscar" id="btnbuscar" value=">>><?=_("CONSULTAR") ;?>"  style="font-weight:bold;width:120px;height:40px;font-size:10px"/></td>
    </tr>    
  </table>

	<script type="text/javascript">
			Calendar.setup({
			inputField     :    "f_date_b",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
	</script>  
    
	<script type="text/javascript">
			/* Calendar.setup({
			inputField     :    "f_date_b2",      // id of the input field
			ifFormat       :    "%Y-%m-%d",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "f_trigger_b2",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		}); */
	</script>      
</form>
  <br>

	<div id="tablewrapper">	 
        	
                <input type="hidden" id="columns" />
                <input type="hidden" id="query" />
        <? if($numreg >0){	?>
            <span class="details">
				<div><?=_("Registros") ;?><span id="startrecord"></span>-<span id="endrecord"></span> de <span id="totalrecords"></span></div>
        		 
        	</span>
		<? } ?>
        <table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
            <thead>
                <tr>
                    <th class="nosort"><h3><?=_(" ") ;?></h3></th>
                    <th><h3><?=_("#EXPEDIENTE") ;?></h3></th>
                    <th><h3><?=_("AFILIADO") ;?></h3></th>
                    <th><h3><?=_("CONTACTO") ;?></h3></th>
                    <th><h3><?=_("STATUSEXP.") ;?></h3></th>
                    <th><h3><?=_("FECHAREGISTRO") ;?></h3></th>
                    <th><h3><?=_("TELEFONO1") ;?></h3></th>
                    <th><h3><?=_("TELEFONO2") ;?></h3></th>
                    <th><h3><?=_("DOCUMENTO") ;?></h3></th>					
                    <th><h3><?=_("CUENTA") ;?></h3></th>
                    <th><h3><?=_("PLAN") ;?></h3></th>
                    <th class="nosort"><h3></h3></th>
                </tr>		
            </thead>
            <tbody>			
	 <?
		 
		  if($_POST["btnbuscar"]){		  		 
			while($reg = $result->fetch_object())
			 {				
				if($c%2==0) $fondo='#FFFFFF'; else $fondo='#F9F9F9';	
				if($c%2==0) $clase='trbuc0'; else $clase='trbuc1';
				
				$c=$c+1;
				
				$varexis=crypt($reg->IDEXPEDIENTE,"666");
				 
		?>		
		 <tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' title="<?=$reg->IDEXPEDIENTE." - ".$reg->titular;?>" >
			<td align="center"><div align="center"><?=$c;?></div></td>
			<td align="center"><?=$reg->IDEXPEDIENTE;?></td>	
			<td><?=utf8_encode($reg->titular);?></td>			
			<td><?=utf8_encode($reg->contacto);?></td>			
			<td><?=$desc_status_expeduiente[$reg->ARRSTATUSEXPEDIENTE];?></td>			
			<td align="center" style="color:#FF0000"><?=substr($reg->FECHAREGISTRO,0,10);?></td>			
			<?
				$Sql_tel="SELECT
							  expediente_persona_telefono.NUMEROTELEFONO
							FROM $con->temporal.expediente_persona_telefono
							  INNER JOIN $con->temporal.expediente_persona
								ON expediente_persona.IDPERSONA = expediente_persona_telefono.IDPERSONA
							WHERE expediente_persona_telefono.IDPERSONA = '".$reg->IDPERSONA."' 
							ORDER BY expediente_persona_telefono.PRIORIDAD
							LIMIT 2";

				$resultel=$con->query($Sql_tel);
				
				while($row = $resultel->fetch_object())
				 {
					$ii=$ii+1;
					$telefono[$ii]=$row->NUMEROTELEFONO;
				 }	
					$ii=0;
				for ($i=1;$i<=2;$i++)				
				 {
			?>
		
			<td><?=$telefono[$i];?></td>
			<?		
					$telefono[$i]="";
				 }
				 
				if($reg->STATUSASISTENCIA=="CAN")	$stylo="color:#FF0000";

			?>
			<td><?=$reg->DOCUMENTOS;?></td>
			<td><?=$reg->cuenta;?></td>
			<td><?=$reg->plan;?></td>
			<td style="background-color:#FF8080"><img src="../../../../imagenes/iconos/ver_registro.png" alt="Ver info" title="Ver detalle" onClick="window.open('expediente_detalle.php?idexpediente=<?=$reg->IDEXPEDIENTE;?>&varexis=<?=$varexis;?>','mywindow','location=no,status=no,scrollbars=1,resizable=no,height=550,width=950')" style="cursor:pointer" title="Ver Info"/></td>
			
			<?				 
			 
	       	}
		}
			?>                
            </tbody>
        </table>
		<? if($numreg >0){	?>
        <div id="tablefooter" style="border:1px dashed #003366;height:17px">
          <div id="tablenav">
            	<div >
                    <img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" title="Primera Pagina" onclick="sorter.move(-1,true)" />
                    <img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" title="Pagina Siguiente" onclick="sorter.move(-1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" title="Pagina Anterior" onclick="sorter.move(1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" title="Ultima Pagina" onclick="sorter.move(1,true)" />
                </div>
            </div>
			<div id="tablelocation">
            	<div>
                    <select onchange="sorter.size(this.value)" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);">
						<option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span><?=_("Entrada de Pagina") ;?></span>
                </div>
                <div class="page">Pag. <span id="currentpage"></span> de <span id="totalpages"></span></div>
            </div>
        </div>
		<? }	?>
    </div>
	
	
	
	<script type="text/javascript" src="../../../../librerias/tinytablev3.0/script.js"></script>
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
		colddid:'columns',
		currentid:'currentpage',
		totalid:'totalpages',
		startingrecid:'startrecord',
		endingrecid:'endrecord',
		totalrecid:'totalrecords',
		hoverid:'selectedrow',
		//pageddid:'pagedropdown',
		navid:'tablenav',
		sortcolumn:1,
		sortdir:1,
		//sum:[8],
		//avg:[6,7,8,9],
		columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});
  </script>
</body>
</html>