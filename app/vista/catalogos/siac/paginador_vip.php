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
	 
//verificar sesion activa.
	Auth::required($_REQUEST["txturl"]);

//registro a mostrar
	$RegistrosAMostrar=18;
 
//estos valores los recibo por GET
	if(isset($_GET['pag'])){
		$RegistrosAEmpezar=($_GET['pag']-1)*$RegistrosAMostrar;
		$PagAct=$_GET['pag'];
//caso contrario los iniciamos
	} else{
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
	$VIPVALIDADO=$con->consultation("SELECT IDCUENTA FROM $con->catalogo.catalogo_cuenta WHERE VIPVALIDADO=1");
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	if($_REQUEST["buscarafiliado"] ==1)	$vervicio="catalogo_afiliado.AFILIADO_SISTEMA='VALIDADO' AND ";
 
	if($_REQUEST["cuenta"])	$ver_cuentas="catalogo_cuenta.IDCUENTA IN('".$_REQUEST["cuenta"]."','".$VIPVALIDADO[0][0]."') AND ";

	$busqueda=($_REQUEST["cmbbusqueda"]==2 || $_REQUEST["cmbbusqueda"] ==7)?filtradoStr($_REQUEST['busqueda'],false):filtradoStr($_REQUEST['busqueda'],true);

	if($_REQUEST["cmbbusqueda"]!="null" and $_REQUEST["cmbbusqueda"]!=""){
			
		$Sql="SELECT
				  /* BUSCAR AFILIADO SAC */
				  catalogo_afiliado.CVEAFILIADO,
				  catalogo_afiliado.IDAFILIADO,
				  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
				  catalogo_afiliado.IDUSUARIOCREACION,
				  catalogo_cuenta.NOMBRE                AS nombrecuenta,
				  catalogo_cuenta.ACTIVO      AS STATUSCUENTA,
				  catalogo_cuenta.CUENTAVIP,
				  catalogo_afiliado.STATUSASISTENCIA,
				  catalogo_status_comercial.DESCRIPCION AS STATUSCOMERCIAL,
				  catalogo_afiliado_persona.IDDOCUMENTO,
				  /*catalogo_afiliado_persona.AFILIADO_VIP,*/
				  catalogo_afiliado_beneficiario.IDDOCUMENTO AS DOCUMENTOBEN,
				  catalogo_programa.NOMBRE              AS nombreprograma,
				  catalogo_afiliado.IDAFILIADO,
				  catalogo_afiliado.AFILIADO_SISTEMA,
				  catalogo_programa.IDCUENTA,
				  catalogo_programa.IDPROGRAMA,
				  catalogo_programa.ACTIVO,
				  catalogo_programa.PROGRAMAVIP,
				  /*CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,', ',catalogo_afiliado_persona.NOMBRE) AS nombres,*/
				  IF(catalogo_afiliado_persona.NOMBRE ='',catalogo_afiliado_persona.APPATERNO,IF(catalogo_afiliado_persona.APPATERNO ='',catalogo_afiliado_persona.NOMBRE,CONCAT(catalogo_afiliado_persona.APPATERNO,' ',catalogo_afiliado_persona.APMATERNO,', ',catalogo_afiliado_persona.NOMBRE))) AS nombres,
				  CONCAT(catalogo_afiliado_beneficiario.APPATERNO,' ',catalogo_afiliado_beneficiario.APMATERNO,', ',catalogo_afiliado_beneficiario.NOMBRE) AS BENEFICIARIO
				FROM $con->catalogo.catalogo_afiliado
				  INNER JOIN $con->catalogo.catalogo_afiliado_persona
					ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
				  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_telefono
					ON catalogo_afiliado_persona_telefono.IDAFILIADO = catalogo_afiliado.IDAFILIADO
				  LEFT JOIN $con->catalogo.catalogo_programa
					ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
				  LEFT JOIN $con->catalogo.catalogo_cuenta
					ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
				  LEFT JOIN $con->catalogo.catalogo_afiliado_persona_vehiculo
					ON catalogo_afiliado_persona_vehiculo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
				  LEFT JOIN $con->temporal.retencion
					ON retencion.IDAFILIADO = catalogo_afiliado.IDAFILIADO	
				  LEFT JOIN $con->catalogo.catalogo_status_comercial
					ON catalogo_status_comercial.STATUSCOMERCIAL = catalogo_afiliado.STATUSCOMERCIAL
						AND catalogo_status_comercial.IDCUENTA = catalogo_afiliado.IDCUENTA
				  LEFT JOIN $con->catalogo.catalogo_afiliado_beneficiario
					ON catalogo_afiliado_beneficiario.IDAFILIADO = catalogo_afiliado.IDAFILIADO				
				WHERE $vervicio catalogo_cuenta.IDCUENTA!='".$_REQUEST["cuenta"]."' AND";

			if($_REQUEST["cmbbusqueda"]!="null" and $_REQUEST["cmbbusqueda"]!="")
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
				else if($_REQUEST["cmbbusqueda"] ==7  and trim($_REQUEST["busqueda"]))	
				 {
					$Sql=$Sql." MATCH( catalogo_afiliado_beneficiario.NOMBRE, catalogo_afiliado_beneficiario.APPATERNO, catalogo_afiliado_beneficiario.APMATERNO ) AGAINST('".$busqueda."' IN BOOLEAN MODE) ";				
				 }

				if($_REQUEST["cmbbusqueda"] ==7) $SqlFinal=$Sql."AND catalogo_cuenta.CUENTAVIP=1 group by catalogo_afiliado.IDAFILIADO,catalogo_afiliado_beneficiario.IDBENEFICIARIO order by catalogo_afiliado_beneficiario.APPATERNO,catalogo_afiliado_beneficiario.APMATERNO ";
				else $SqlFinal=$Sql."AND catalogo_cuenta.CUENTAVIP=1 group by catalogo_afiliado.IDAFILIADO order by catalogo_afiliado_persona.APPATERNO,catalogo_afiliado_persona.APMATERNO";

				if($busqueda and $_REQUEST["btnbuscar"])	$result=$con->query($SqlFinal." LIMIT $RegistrosAEmpezar,$RegistrosAMostrar");

			  } 
	//echo $SqlFinal;
			$numreg=$result->num_rows;
	}
 
  	$quitar= array(',','  ','%','\'','/','\\');
	$txtnombre=trim(str_replace($quitar, "",$_REQUEST['busqueda']));
	
	if($_REQUEST["cuenta"]!="null" and $_REQUEST["cuenta"]!=""){
?>
	<table cellpadding="1" cellspacing="1" border="0" id="table" class="tinytable" style="width:100%">	
		<tr bgcolor='#597D98'>
			<th class="nosort" colspan="12" style="color:red"><h3><u>AFILIADOS VIP</u></h3></th>
		</tr> 	
		<tr bgcolor='#597D98'>
			<th class="nosort"><h3></h3></th>
			<th style="color:#FFFFFF" height="27" width="100px"><h3><?=_("CLAVEAFI.") ;?></h3></th>
			<th style="color:#FFFFFF" bgcolor="<?=($_REQUEST["cmbbusqueda"] ==7)?"#425E71":""?>"><h3><?=($_REQUEST["cmbbusqueda"] ==7)?_("BENEFICIARIO"):_("TITULAR") ;?></h3></th>
			<th style="color:#FFFFFF" bgcolor="<?=($_REQUEST["cmbbusqueda"] ==7)?"#425E71":""?>"><h3><?=_("TELEFONO1") ;?></h3></th>
			<th style="color:#FFFFFF" bgcolor="<?=($_REQUEST["cmbbusqueda"] ==7)?"#425E71":""?>"><h3><?=_("TELEFONO2") ;?></h3></th>
			<th style="color:#FFFFFF" bgcolor="<?=($_REQUEST["cmbbusqueda"] ==7)?"#425E71":""?>"><h3><?=_("DOCUMENTO") ;?></h3></th>
			<th style="color:#FFFFFF"><h3><?=_("CUENTA") ;?></h3></th>
			<th style="color:#FFFFFF"><h3><?=_("PLAN") ;?></h3></th>
			<th style="color:#FFFFFF"><h3><?=_("USUARIO") ;?></h3></th>
			<th style="color:#FFFFFF"><h3><?=_("EST.COMERCIAL") ;?></h3></th>
			<th style="color:#FFFFFF"><h3><?=_("EST.AFILIADO") ;?></h3></th>
			<th class="nosort"><h3></h3></th>
		</tr> 
<?
		 if($busqueda and $_REQUEST["btnbuscar"]){  		 
			while($reg = $result->fetch_object())
			 {
				//Cantidad de comentario
				$cantidadComentario=$con->consultation("SELECT COUNT(*) AS COMENTARIO FROM $con->temporal.retencion WHERE IDAFILIADO='".$reg->IDAFILIADO."' AND IDDETMOTIVOLLAMADA=85");
		
				$Sql_tel="SELECT
						  catalogo_afiliado_persona_telefono.NUMEROTELEFONO
						FROM $con->catalogo.catalogo_afiliado_persona_telefono
						  INNER JOIN $con->catalogo.catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO
						WHERE catalogo_afiliado_persona_telefono.IDAFILIADO = '".$reg->IDAFILIADO."' 
						ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
						LIMIT 2";
			
				if($_REQUEST["cmbbusqueda"] ==7){
					$reg->nombres=$reg->BENEFICIARIO;
					$reg->IDDOCUMENTO=$reg->DOCUMENTOBEN;
					

				$Sql_tel="SELECT
						  catalogo_afiliado_beneficiario_telefono.NUMEROTELEFONO
						FROM $con->catalogo.catalogo_afiliado_beneficiario_telefono
						  INNER JOIN $con->catalogo.catalogo_afiliado_beneficiario
							ON catalogo_afiliado_beneficiario.IDBENEFICIARIO = catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO
						WHERE catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = '".$reg->IDBENEFICIARIO."' 
						ORDER BY catalogo_afiliado_beneficiario_telefono.PRIORIDAD
						LIMIT 2";			
				}
				
			if($c%2==0) $fondo='#FFFFFF'; else $fondo='#ECF2F6';
?>
		 <tr bgcolor=<?=$fondo;?> class='<?=$clase;?>' title="<?=$reg->CVEAFILIADO." - ".$reg->nombres;?>" >
			<td><? if($cantidadComentario[0][0]>0){ ?><img src="../../../../imagenes/iconos/comments.png" title="Comentarios Afiliado" style="cursor:pointer" onClick="presentar_formulario('','comentarios_afiliado.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("COMENTARIOS SOBRE EL AFILIADO")?>','640','340','','','','','','','','<?=$reg->IDAFILIADO?>','mac_os_x')"/><? } ?></td>			
			<? if($reg->CUENTAVIP ==1 || $reg->PROGRAMAVIP ==1 || $reg->AFILIADO_VIP ==1 ){?>
			<td background="../../../../imagenes/logos/vip.png" style="color:#FFFFFF;font-weight:bold" title="AFILIADO VIP">
				<div align="center"><?=$reg->CVEAFILIADO;?></div>
			</td>
			<? } else{ ?>			
			<td><div align="center"><?=$reg->CVEAFILIADO;?></div></td>			
			<? } ?>
			<td height="30px"><?=utf8_encode($reg->nombres);?></td>			
			 <?

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
				 
				if($reg->STATUSASISTENCIA=="CAN")	$stylo="color:#FF0000"; else $stylo="color:#004488"; 
			?>
			<td><?=$reg->IDDOCUMENTO;?></td>
			<td><?=$reg->nombrecuenta;?></td>
			<td><?=$reg->nombreprograma;?></td>
			<td><?=$reg->IDUSUARIOCREACION;?></td>			
			<td align="center" style="font-weight:bold;background-color:#FFFF97"><u><?=($reg->STATUSCOMERCIAL)?$reg->STATUSCOMERCIAL:"S/D";?></u></td>			
			<td style="<?=$stylo;?>;font-weight:bold"><div align="center">
			  <?=$desc_status_afi_asistencia[$reg->STATUSASISTENCIA];?>
		    </div></td>
			<? if(!$_REQUEST["buscarafiliado"]){ ?>
				<td width="26px">
					<form name="frmdetalle"  action="gestionarafiliado.php" method="post">
						<input type="hidden" name="idafiliado" value="<?=$reg->IDAFILIADO;?>" />	
						<input type="hidden" name="status" value="<?=$reg->STATUSASISTENCIA;?>" />	
						<input type="submit" name="btngestionar" id="btngestionar" value="<?=_("GESTIONAR") ;?>" style="font-weight:bold" />
					</form>					
				</td>
			<? 
                } else{
            ?>
				<td width="13%">
					<form name="frmdetalle"  action="gestionarafiliado.php" method="post">
						<input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" <?=($reg->ACTIVO==0 or $reg->STATUSCUENTA ==0)?"disabled":""?> style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_titular('<?=$reg->IDAFILIADO;?>','<?=$reg->IDCUENTA;?>','<?=$reg->IDPROGRAMA;?>')" />
						<input type="hidden" name="idafiliado" value="<?=$reg->IDAFILIADO;?>" />	
						<input type="hidden" name="status" value="<?=$reg->STATUSASISTENCIA;?>" />	
						<input type="hidden" name="buscarafiliado" value="<?=$_REQUEST["buscarafiliado"];?>" />	
						<input type="hidden" name="cmbbusqueda" value="<?=$_REQUEST["cmbbusqueda"];?>" />	
						<input type="submit" name="btngestiona" id="btngestiona" value="<?=_("DETALLE") ;?>" style="font-weight:bold" />
					</form>					
				</td>
			<? } ?>
			
		 </tr>
		<?
			 $c=$c+1;
			 $stylo="";
			}
		} 
	
//******--------determinar las páginas---------******//

	if($_REQUEST["btnbuscar"] and $numreg >0){
		
	 	$NroReg=$con->query($SqlFinal." ");
		$NroRegistros=$NroReg->num_rows;
		
		$PagAnt=$PagAct-1;
		$PagSig=$PagAct+1;
		$PagUlt=$NroRegistros/$RegistrosAMostrar;

//verificamos residuo para ver si llevará decimales
	$Res=$NroRegistros%$RegistrosAMostrar;
// si hay residuo usamos funcion floor para que me
// devuelva la parte entera, SIN REDONDEAR, y le sumamos
// una unidad para obtener la ultima pagina
	if($Res>0) $PagUlt=floor($PagUlt)+1;

//desplazamiento 
	echo "<tr><td style='font-weight:bold' colspan='13'>";
	echo "<a onclick=\"paginar_total('1',0,2)\" style='text-decoration:underline;cursor:pointer' title='Primero'>Primera</a> "; 
	if($PagAct>1) echo "<a onclick=\"paginar_total('$PagAnt',0,2)\" style='text-decoration:underline;cursor:pointer' title='Anterior'>Anterior</a> ";
	echo "<strong>Pagina ".$PagAct."/".$PagUlt."</strong>";
	if($PagAct<$PagUlt)  echo " <a onclick=\"paginar_total('$PagSig',0,2)\" style='text-decoration:underline;cursor:pointer' title='Siguiente'>Siguiente</a> ";
	if($NroRegistros > $RegistrosAMostrar) echo " <a onclick=\"paginar_total('$PagUlt',0,2)\" style='text-decoration:underline;cursor:pointer' title='Ultimo'>Ultimo</a>";
	
	echo "</td></tr>";	
 
	} else{
		echo "<tr><td style='font-weight:bold' colspan='12'>SIN REGISTROS</td></tr>";
	}
?>
 </table> 
 <br/><br/>
<? } ?>