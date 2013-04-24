<?php
	session_start(); 
 
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/validar_permisos.php");	
	include_once("../../../modelo/functions.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	Auth::required();


//registro a mostrar
	$RegistrosAMostrar=17;
 
//estos valores los recibo por GET
	if(isset($_GET['pag'])){
		$RegistrosAEmpezar=($_GET['pag']-1)*$RegistrosAMostrar;
		$PagAct=$_GET['pag'];
//caso contrario los iniciamos
	}else{
		$RegistrosAEmpezar=0;
		$PagAct=1;
		
	}

//sesion del contenido de busqueda
	if($_REQUEST['cuenta'] =="OPCIONAL")	$_REQUEST['cuenta']="";
	if($_REQUEST['busqueda']!="" and $_SESSION["user"]!="")	$_SESSION["busqueda"] =$_REQUEST['busqueda'];
	if($_REQUEST['busqueda'] and $_SESSION["user"]!="")	$_SESSION["cuenta"] =$_REQUEST['cuenta'];
	if($_REQUEST['busqueda'] and $_SESSION["user"]!="")	$_SESSION["cmbbusqueda"] =$_REQUEST['cmbbusqueda'];

	if($_SESSION["busqueda"] and !$_GET['busqueda'])	$_REQUEST['busqueda']=$_SESSION["busqueda"];
	if($_SESSION['busqueda'] and !$_GET['cuenta'])	$_REQUEST['cuenta']=$_SESSION["cuenta"];
	if($_SESSION['busqueda'] and !$_GET['busqueda'])	$_REQUEST['cmbbusqueda']=$_SESSION["cmbbusqueda"];
	 
//verificar permisos de accesos a las cuentas
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	if($_REQUEST["cuenta"])	$ver_cuentas="catalogo_cuenta.IDCUENTA='".$_REQUEST["cuenta"]."' AND ";
	 
	$busqueda=($_REQUEST["cmbbusqueda"]!=2 )?filtradoStr($_REQUEST['busqueda'],true):filtradoStr($_REQUEST['busqueda'],false);
 
 	$Sql="SELECT
			  catalogo_afiliado.CVEAFILIADO,
			  catalogo_afiliado.IDAFILIADO,
			  catalogo_afiliado.IDUSUARIOCREACION,
			  catalogo_cuenta.NOMBRE                AS nombrecuenta,
			  catalogo_afiliado.STATUSASISTENCIA,
			  catalogo_afiliado_persona.IDDOCUMENTO,
			  catalogo_programa.NOMBRE              AS nombreprograma,
			  catalogo_afiliado.IDAFILIADO,
			  catalogo_programa.IDCUENTA,
			  catalogo_programa.IDPROGRAMA,
			  CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,', ',catalogo_afiliado_persona.NOMBRE) AS nombres
			FROM $con->catalogo.catalogo_afiliado
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona
				ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_telefono
				ON catalogo_afiliado_persona_telefono.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  INNER JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
			  INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
			  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_vehiculo
				ON catalogo_afiliado_persona_vehiculo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			  LEFT JOIN $con->temporal.retencion
				ON retencion.IDAFILIADO = catalogo_afiliado.IDAFILIADO				
			WHERE $ver_cuentas ";

		if($_REQUEST["cmbbusqueda"]!="null" and $_REQUEST["cmbbusqueda"]!="" )
		 {	 
		
			if($_GET["cmbbusqueda"]==1  || $_REQUEST["cmbbusqueda"] ==1)
			 {
				$Sql=$Sql." catalogo_afiliado.CVEAFILIADO like '%$busqueda%' ";	
			 } 
			else if($_REQUEST["cmbbusqueda"] ==2 and $_GET["busqueda"])
			 {
				$Sql=$Sql."  MATCH( catalogo_afiliado_persona.NOMBRE, catalogo_afiliado_persona.APPATERNO, catalogo_afiliado_persona.APMATERNO ) AGAINST('".$busqueda."' IN BOOLEAN MODE) ";	
			 }	
			else if($_REQUEST["cmbbusqueda"] ==3)	
			 {
				$Sql=$Sql."  catalogo_afiliado_persona.IDDOCUMENTO like '%$busqueda%' ";	
			 }	
			else if($_REQUEST["cmbbusqueda"] ==4) 	
			 {
				$Sql=$Sql."  catalogo_afiliado_persona_vehiculo.PLACA like '%$busqueda%' ";
			 }	
			else if($_REQUEST["cmbbusqueda"] ==5)	
			 {
				$Sql=$Sql."  catalogo_afiliado_persona_telefono.NUMEROTELEFONO like '%$busqueda%' ";
			 }			
			else if($_REQUEST["cmbbusqueda"] ==6  and trim($_REQUEST["busqueda"]))	
			 {
				if(!ctype_digit($_REQUEST["busqueda"]))		$busqueda="null";		else 	$busqueda=$_REQUEST["busqueda"];
				$Sql=$Sql."  retencion.IDRETENCION =".$busqueda;				
			 }

			$SqlFinal=$Sql." group by catalogo_afiliado.IDAFILIADO order by catalogo_afiliado_persona.APPATERNO LIMIT 17";
 
			if($busqueda and $ver_cuentas and $_REQUEST["btnbuscar"])	$result=$con->query($SqlFinal);
 
		  }
		  /*
		 else if($_REQUEST["busqueda"] and $_REQUEST["btnbuscar"])
		  {

				$SqlFinal="";
				for( $i = 1 ; $i <= 6 ; $i ++) {		

					$busqueda2=($i!=2 )?filtradoStr($_REQUEST['busqueda'],true):filtradoStr($_REQUEST['busqueda'],false);
					
					if($busqueda2=="")	$busqueda2="null";
					 
					if($i ==1 and $_REQUEST["busqueda"])
					 { 
						$Sqlcondicion=" catalogo_afiliado.CVEAFILIADO like '%".$busqueda2."%'";
					 } 
					else if($i ==2 and $_GET["busqueda"]) 	
					 { 
						$Sqlcondicion=" MATCH(catalogo_afiliado_persona.NOMBRE, catalogo_afiliado_persona.APPATERNO, catalogo_afiliado_persona.APMATERNO ) AGAINST('".$busqueda2."' IN BOOLEAN MODE) ";	
					 }	
					else if($i ==3)	
					 {
						$Sqlcondicion=" catalogo_afiliado_persona.IDDOCUMENTO like '%$busqueda2%' ";	
					 }	
					else if($i ==4) 	
					 {
						$Sqlcondicion=" catalogo_afiliado_persona_vehiculo.PLACA like '%$busqueda2%' ";	
					 }	
					else if($i ==5)	
					 {
						$Sqlcondicion=" catalogo_afiliado_persona_telefono.NUMEROTELEFONO like '%$busqueda2%' ";	
					 }		
					else if($i ==6)
					 {					 
						if(!ctype_digit($_REQUEST["busqueda"]))		$busqueda="null";		else 	$busqueda=$_REQUEST["busqueda"];
						$Sqlcondicion=" retencion.IDRETENCION =".$busqueda;	
						 
					 }
					
					$SqlFinal=$Sql.$Sqlcondicion." group by catalogo_afiliado.IDAFILIADO order by catalogo_afiliado_persona.APPATERNO";
 
					if($busqueda and $ver_cuentas and $_REQUEST["btnbuscar"])	$result=$con->query($SqlFinal." LIMIT $RegistrosAEmpezar, $RegistrosAMostrar");
					if($result->num_rows >0)	break;	
				}
		  }*/
		  
	$numreg=$result->num_rows;
 
  	$quitar= array(',','  ','%','\'','/','\\');
	$txtnombre=trim(str_replace($quitar, "",$_REQUEST['busqueda']));
?>
 
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
                    <th class="nosort"><h3><?=_("CODIGO-ID.") ;?></h3></th>
                    <th><h3><?=_("TITULAR") ;?></h3></th>
                    <th><h3><?=_("TELEFONO1") ;?></h3></th>
                    <th><h3><?=_("TELEFONO2") ;?></h3></th>
                    <th><h3><?=_("DOCUMENTO") ;?></h3></th>
                    <th><h3><?=_("CUENTA") ;?></h3></th>
                    <th><h3><?=_("PLAN") ;?></h3></th>
                    <th><h3><?=_("USUARIO") ;?></h3></th>
                    <th><h3><?=_("STATUS") ;?></h3></th>
                    <th class="nosort"><h3></h3></th>
                </tr>
				
 
		
            </thead>
            <tbody>
			
			
	 <?
		 
		 if($busqueda and $ver_cuentas and $_REQUEST["btnbuscar"]){			  		 
			while($reg = $result->fetch_object())
			 {				
				if($c%2==0) $fondo='#FFFFFF'; else $fondo='#F9F9F9';	
				if($c%2==0) $clase='trbuc0'; else $clase='trbuc1';			
		?>		
		 <tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' title="<?=$reg->CVEAFILIADO." - ".$reg->nombres;?>" >
			<td><div align="center">
			  <?=$reg->CVEAFILIADO;?>
		    </div></td>
			<td><?=utf8_encode($reg->nombres);?></td>			
			 <?
				$Sql_tel="SELECT
							  catalogo_afiliado_persona_telefono.NUMEROTELEFONO
							FROM catalogo_afiliado_persona_telefono
							  INNER JOIN catalogo_afiliado
								ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
							WHERE catalogo_afiliado_persona_telefono.IDAFILIADO = '".$reg->IDAFILIADO."' 
							ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
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
			<td><?=$reg->IDDOCUMENTO;?></td>
			<td><?=$reg->nombrecuenta;?></td>
			<td><?=$reg->nombreprograma;?></td>
			<td><?=$reg->IDUSUARIOCREACION;?></td>			
			<td style="<?=$stylo;?>;font-weight:bold"><div align="center">
			  <?=$desc_status_afi_asistencia[$reg->STATUSASISTENCIA];?>
		    </div></td>
			<? if(!$_REQUEST["buscarafiliado"]){ ?>
				<td width="26px">
					<form name="frmdetalle"  action="gestionarafiliado.php" method="post">
						<input type="hidden" name="idafiliado" value="<?=$reg->IDAFILIADO;?>" />	
						<input type="hidden" name="status" value="<?=$reg->STATUSASISTENCIA;?>" />	
						<input type="submit" name="btngestionar" id="btngestionar" value="<?=_("GESTIONAR") ;?>" style="font-weight:bold;font-size:9px;" />
					</form>
				</td>
			<? } else{ ?>
				<td width="18%">
					<form name="frmdetalle"  action="gestionarafiliado.php" method="post">
						<input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_titular('<?=$reg->IDAFILIADO;?>','<?=$reg->IDCUENTA;?>','<?=$reg->IDPROGRAMA;?>')" />
						<input type="hidden" name="idafiliado" value="<?=$reg->IDAFILIADO;?>" />	
						<input type="hidden" name="status" value="<?=$reg->STATUSASISTENCIA;?>" />	
						<input type="hidden" name="buscarafiliado" value="<?=$_REQUEST["buscarafiliado"];?>" />	
						<input type="submit" name="btngestiona" id="btngestiona" value="<?=_("DETALLE") ;?>" style="font-weight:bold;font-size:9px;" />
					</form>
				</td>
			<? } ?>
			
		 </tr>
		<?
			 $c=$c+1;
			 $stylo="";
			}
		}
		?>
                
            </tbody>
			
			<tfoot>
					<tr>
						<td>
							<div id="tablenav">
								<img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" title="Primera Pagina" onclick="sorter.move(-1,true)" />
								<img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" title="Pagina Siguiente" onclick="sorter.move(-1)" />
								<img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" title="Pagina Anterior" onclick="sorter.move(1)" />
								<img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" title="Ultima Pagina" onclick="sorter.move(1,true)" />
							</div>						
						</td>
						<td colspan="9">						
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
						
						</td></tr>
			</tfoot>

        </table>
		<? if($numreg == 0){ echo  _("SIN REGISTROS.") ;} ?>
		<? if($numreg >0){	?>
       
	 
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
		paginate:false,
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
		sortcolumn:0,
		sortdir:0,
		//sum:[8],
		//avg:[6,7,8,9],
		//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});
  </script>