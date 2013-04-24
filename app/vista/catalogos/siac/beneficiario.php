
<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 	Auth::required();
 
	validar_permisos("MENU_SAC",1);
	
	$cuenta_programa=$con->consultation("SELECT catalogo_afiliado.IDPROGRAMA,catalogo_programa.IDCUENTA FROM catalogo_afiliado INNER JOIN catalogo_programa ON catalogo_programa.IDPROGRAMA=catalogo_afiliado.IDPROGRAMA  WHERE catalogo_afiliado.IDAFILIADO=".$_GET["idafiliado"]);

	if($_GET["verinfo"])
	 {
		$_GET["idbeneficiario"]=$_GET["verinfo"];	
		$readonly="readonly";
		$disabled="disabled";
	 }
	 
	if($_GET["idbeneficiario"])
	 {
		
		$Sql_ben1="SELECT
					  catalogo_afiliado_beneficiario_ubigeo.CVEPAIS,
					  catalogo_afiliado_beneficiario_ubigeo.DIRECCION,
					  catalogo_afiliado_beneficiario_ubigeo.CODPOSTAL,
					  catalogo_afiliado_beneficiario.IDTIPODOCUMENTO,
					  catalogo_afiliado_beneficiario.GENERO,
					  catalogo_afiliado_beneficiario.IDDOCUMENTO,
					  LOWER(catalogo_afiliado_beneficiario.EMAIL1) AS EMAIL1,
					  LOWER(catalogo_afiliado_beneficiario.EMAIL2) AS EMAIL2,
					  LOWER(catalogo_afiliado_beneficiario.EMAIL3) AS EMAIL3,
					  catalogo_afiliado_beneficiario.GENERO,
					  catalogo_afiliado_beneficiario.ARRPARENTESCO,
					  catalogo_afiliado.IDAFILIADO,
					  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
					  catalogo_afiliado_beneficiario.IDDOCUMENTO,
					  catalogo_afiliado_beneficiario.APPATERNO,
					  catalogo_afiliado_beneficiario.APMATERNO,
					  catalogo_afiliado_beneficiario.NOMBRE,
					  catalogo_afiliado_beneficiario.ACTIVO
					FROM catalogo_afiliado_beneficiario
					  INNER JOIN catalogo_afiliado
						ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_beneficiario.IDAFILIADO  
					  LEFT JOIN catalogo_afiliado_beneficiario_telefono
						ON catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
					  LEFT JOIN catalogo_afiliado_beneficiario_ubigeo
						ON catalogo_afiliado_beneficiario_ubigeo.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
					WHERE catalogo_afiliado_beneficiario.IDBENEFICIARIO = ".$_GET["idbeneficiario"]."
					GROUP BY catalogo_afiliado_beneficiario.IDBENEFICIARIO
					ORDER BY catalogo_afiliado_beneficiario.FECHAMOD DESC";

		$rs_benef=$con->query($Sql_ben1);
		$rowben = $rs_benef->fetch_object();	
	 }

	$sql_ben_tel="SELECT
					  catalogo_afiliado_beneficiario_telefono.CODIGOAREA,
					  catalogo_afiliado_beneficiario_telefono.IDTIPOTELEFONO,
					  catalogo_afiliado_beneficiario_telefono.NUMEROTELEFONO,
					  catalogo_afiliado_beneficiario_telefono.EXTENSION,
					  catalogo_afiliado_beneficiario_telefono.IDTSP
					FROM catalogo_afiliado_beneficiario_telefono
					  INNER JOIN catalogo_afiliado_beneficiario
						ON catalogo_afiliado_beneficiario.IDBENEFICIARIO = catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO 
					WHERE catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO ='".$rowben->IDBENEFICIARIO."'
					ORDER BY catalogo_afiliado_beneficiario_telefono.PRIORIDAD
					LIMIT 4";
	
	$resultel=$con->query($sql_ben_tel);
	while($row = $resultel->fetch_object())
	 {
		$ii=$ii+1;
		$telefono[$ii]=$row->NUMEROTELEFONO;		
		$tipotelefono[$ii]=$row->IDTIPOTELEFONO;		
		$codigoa[$ii]=$row->CODIGOAREA;		
		$extension[$ii]=$row->EXTENSION;		
		$tsp[$ii]=$row->IDTSP;		
	 }
	
	if($rowben->IDBENEFICIARIO!="" or $rowben->IDBENEFICIARIO!=0)
	 {
		$ubigeo = new ubigeo();
		$ubigeo->leer("IDBENEFICIARIO",$con->catalogo,"catalogo_afiliado_beneficiario_ubigeo",$rowben->IDBENEFICIARIO);
	 }		 
	
	//echo $Sql_ben1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
<style type="text/css">
<!--
.style3 {color: #4E4E4E; font-weight: bold; }
 
body {
	margin: 1px; 
	padding: 0px;
}
-->
</style>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>
	
	<script type="text/javascript">

		function llamada(codigoarea,numero){
			numero = codigoarea+numero;

			new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
			{	method : 'get',
				parameters: {
					prefijo: "",
					num: numero,
					ext: '<?=$_SESSION["extension"];?>'
					} 
			}
			);
		}

	</script>	
	<script type="text/javascript">
		
		function verificardiv(nombrediv,valors,nombre){
			if(valors=='V'){
				comportamientoDiv('+',nombrediv);
				document.getElementById(nombre).value='O';
			}else{
				comportamientoDiv('-',nombrediv);
				document.getElementById(nombre).value='V';	
			} 
		 } 
		
	</script>
	<script type="text/javascript">

		function validarIngreso(valors) {
			
		if(document.form1.cmbcuenta.value==""){
                  alert("SELECCIONE ALGUN CUENTA.");
                  document.form1.cmbcuenta.focus();
                  return (false);
           } 	
		 else if(document.form1.txtnombres.value==""){
                  alert("INGRESE EL NOMBRE.");
                  document.form1.txtnombres.focus();
                  return (false);
           }  
		   else if(document.form1.txtpaterno.value==""){
                  alert("INGRESE EL APELLIDO PATERNO.");
                  document.form1.txtpaterno.focus();
                  return (false);
           }  		 

		  if(confirm('<?=(!$_GET["idbeneficiario"])?_("REALMENTE DESEA PROSEGUIR CON EL INGRESO DEL NUEVO BENEFICIARIO?."):_("REALMENTE DESEA PROSEGUIR CON LA ACTUALIZACION DEL BENEFICIARIO?.") ;?>'))
		   {
				document.form1.action="gbeneficiario.php" ;
				document.form1.submit();
		   }
		
			return (false);	 

		}			   
	</script> 
    <style type="text/css">
<!--
.style4 {
	color: #FFFFFF;
	font-weight: bold;
}
.style5 {font-weight: bold}
-->
    </style>
</head>
<body onload="document.form1.txtnombres.focus();" >
<form id="form1" name="form1" method="post" action="" onSubmit="return validarIngreso(this)">
	<input type="hidden" name="idafiliado" value="<?=$_GET["idafiliado"];?>" />
	<input type="hidden" name="idbeneficiario" value="<?=$_GET["idbeneficiario"];?>" />

	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#EFEDDC" style="border:1px solid #A4A4A4">
		<tr bgcolor="#666666">
			<td colspan="10" bgcolor="#666666"><span class="style4"><?=_("BENEFICIARIOS") ;?></span></td>
		</tr>
		<tr>
			<td width="24"><?=_("CUENTA") ;?></td>
			<td colspan="3">
			<?
				$sql="select IDCUENTA,NOMBRE from catalogo_cuenta order by NOMBRE";
				$con->cmbselectall($sql,"cmbcuenta",$cuenta_programa[0][1],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'","2");
			?>
			</td>
			<td width="24"><?=_("PROGRAMA") ;?></td>
			<td colspan="3">
			<?
				$sql="select IDPROGRAMA,NOMBRE from catalogo_programa order by NOMBRE";
				$con->cmbselectall($sql,"cmbprograma",$cuenta_programa[0][0],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'","2");				
			?>
			</td>
		</tr>
		<tr>
			<td><span class="style6"><?=_("NOMBRES") ;?></span></td>
			<td colspan="3"><input name="txtnombres" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtnombres" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="35" maxlength="37" value="<?=$rowben->NOMBRE;?>" /></td>
			<td><?=_("APE.PATERNO") ;?></td>
			<td colspan="3"><input name="txtpaterno" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtpaterno" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="20" maxlength="22"  value="<?=$rowben->APPATERNO;?>"/></td>
		</tr>
		<tr>
			<td><?=_("APE.MATERNO") ;?></td>
			<td colspan="3"><input name="txtmaterno" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtmaterno" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="20" maxlength="22"  value="<?=$rowben->APMATERNO;?>"/></td>
			<td><?=_("PARENTESCO") ;?></td>
			<td colspan="3">
			<?
				$con->cmbselectdata("SELECT IDPARENTESCO,DESCRIPCION from $con->catalogo.catalogo_parentesco ORDER BY DESCRIPCION","cmbparentesco",$rowben->ARRPARENTESCO,"$disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ");
			?>		
			</td>
		</tr>
		<tr>
			<td><?=_("#DOCUMENTO") ;?></td>
			<td width="245"><input name="txtndocumento" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtndocumento" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17"  value="<?=$rowben->IDDOCUMENTO;?>"/></td>
			<td width="49"><?=_("TIPODOC") ;?></td>
			<td width="146">
			<?
				$sql="select IDTIPODOCUMENTO,DESCRIPCION from catalogo_tipodocumento order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtipodoc",($rowben->IDTIPODOCUMENTO!="")?$rowben->IDTIPODOCUMENTO:"DNI"," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
			?>
			</td>
			<td><?=_("GENERO") ;?></td>
			<td colspan="3">
			<?
				$con->cmb_array("cmbgenero",$desc_genero,$rowben->GENERO," $disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'")
			?>
			</td>
		</tr>
			<? include("frm_telefono.php");?>
		<tr>
			<td><?=_("EMAIL1") ;?></td>
			<td><input name="txtemail" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtemail" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this); isEmail(document.form1.txtemail);" size="35" maxlength="37"  value="<?=$rowben->EMAIL1;?>" /></td>
			<td><?=_("EMAIL2") ;?></td>
			<td colspan="3"><input name="txtemail2" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtemail2" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this); isEmail(document.form1.txtemail2);" size="35" maxlength="37" value="<?=$rowben->EMAIL2;?>" /></td>
			<td width="51"><?=_("EMAIL3") ;?></td>
			<td colspan="2"><input name="txtemail3" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtemail3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this); isEmail(document.form1.txtemail3);" size="35" maxlength="37" value="<?=$rowben->EMAIL3;?>" /></td>
		</tr>
		<? include("../../../vista/includes/vista_entidades2.php");?>
		<input type='hidden'  name='LATITUD' id='latitud'  value = "" >
		<input type='hidden'  name='LONGITUD' id='longitud' value = "" >		
		<tr>
			<td><?=_("DIRECCION") ;?></td>
			<td colspan="3"><input type="text" name='DIRECCION' id='direccion' <?=($_GET["verinfo"])?$readonly:"" ;?> size='65' autocomplete="off" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;" value="<?=$rowben->DIRECCION;?>"></td>
			<div id='sugeridos' class="autocomplete" style="display:none" ></div>
			<td><?=_("COD.POSTAL") ;?></td>
			<td width="182" colspan="3"><input name="txtcodpostal" type="text" class="classtexto" id="txtcodpostal" <?=($_GET["verinfo"])?$readonly:"" ;?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="10" maxlength="10"  value="<?=$rowben->CODPOSTAL;?>" /></td>		
		</tr>   
		<tr><td colspan="6"></td></tr>
	</table>
	
	<div align="right">    
		<input type="button" name="btncerrar" id="btncerrar" value="CERRAR" style="width:100px;font-size:10px;" onclick="actualizaPadre()"/>
		<input type="submit" name="btngrabar" id="btngrabar" value="REGISTRAR BENEFICIARIO" style="font-weight:bold;width:200px;font-size:10px;" <?=($_GET["verinfo"])?"disabled":"";?> />
	</div>
	<p></p>
	
	<table width="875" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #E0E0E0">
		<tr>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("ID") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("NOMBRE BENEFICIARIO") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("FECHAREGISTRO") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("TELEFONO1") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("TELEFONO2") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("TELEFONO3") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("TELEFONO4") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("NUM. DOCUMENTO") ;?></span></div></td>
			<td bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("STATUS") ;?></span></div></td>
			<td bgcolor="#F2F2F2" colspan="3"><div align="center"></div></td>
		</tr>	
		 <?			
			$Sql_benef="SELECT
						  catalogo_afiliado.IDAFILIADO,
						  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
						  catalogo_afiliado_beneficiario.IDDOCUMENTO,
						  CONCAT(catalogo_afiliado_beneficiario.APPATERNO,' ',catalogo_afiliado_beneficiario.APMATERNO,', ',catalogo_afiliado_beneficiario.NOMBRE) AS nombres,
						  catalogo_afiliado_beneficiario.FECHAMOD,
						  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
						  catalogo_afiliado_beneficiario.ACTIVO
						FROM catalogo_afiliado_beneficiario
						  INNER JOIN catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_beneficiario.IDAFILIADO
						  LEFT JOIN catalogo_afiliado_beneficiario_telefono
							ON catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
						  LEFT JOIN catalogo_afiliado_beneficiario_ubigeo
							ON catalogo_afiliado_beneficiario_ubigeo.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
						WHERE catalogo_afiliado_beneficiario.IDAFILIADO =".$_GET["idafiliado"]."
						GROUP BY catalogo_afiliado_beneficiario.IDBENEFICIARIO
						ORDER BY catalogo_afiliado_beneficiario.FECHAMOD DESC";
 
			$result=$con->query($Sql_benef);			
			while($reg = $result->fetch_object())
			 {	
				$c=$c+1;
		?>		
    <tr bgcolor="<?=($_GET["idbeneficiario"]==$reg->IDBENEFICIARIO and !$_GET["verinfo"])?"#FFBBBB":"#FFFFFF" ?>" >
		<td bgcolor="#E0E0E0"><div align="center"><strong><?=$reg->IDBENEFICIARIO;?></strong></div></td>
		<td><?=$reg->nombres;?></td>			
		<td><?=$reg->FECHAMOD;?></td>			
			 <?
				$ii=0;
				$Sql_tel="SELECT
						  catalogo_afiliado_beneficiario_telefono.NUMEROTELEFONO
						FROM catalogo_afiliado_beneficiario_telefono
						  INNER JOIN catalogo_afiliado_beneficiario
							ON catalogo_afiliado_beneficiario.IDBENEFICIARIO = catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO
						WHERE catalogo_afiliado_beneficiario.IDBENEFICIARIO =".$reg->IDBENEFICIARIO."
						ORDER BY catalogo_afiliado_beneficiario_telefono.PRIORIDAD
						LIMIT 4";

				$resultel=$con->query($Sql_tel);
				while($row = $resultel->fetch_object())
				 {
					$ii=$ii+1;
					$telefono[$ii]=$row->NUMEROTELEFONO;
				 }	
					 
				for ($i=1;$i<=4;$i++)
				 {
			?>
		
			<td><?=$telefono[$i];?></td>
			<?		
					$telefono[$i]="";
				 }
			?>
			<td><?=$reg->IDDOCUMENTO;?></td>
			<td align="center"><div id="div-status<?=$c;?>" style="text-align:center;width:65px;height:15px;background-color:<?=($reg->ACTIVO==1)?"#CEFFCE":"#FF2F2F";?>"><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO");?></div></td>			
			<td style="text-align:center"><? if($_GET["idbeneficiario"]==$reg->IDBENEFICIARIO and !$_GET["verinfo"]){ ?><a href="beneficiario.php?idafiliado=<?=$_GET["idafiliado"];?>"><img src="../../../../imagenes/iconos/cancelar_dato.jpg" title="<?=_("CANCELAR") ;?>" width="46" height="15" border="0" onClick="reDirigir('beneficiario.php?idafiliado=<?=$_GET["idafiliado"];?>')"></a><? }else{ ?><a href="beneficiario.php?idbeneficiario=<?=$reg->IDBENEFICIARIO;?>&idafiliado=<?=$reg->IDAFILIADO;?>"><img src="../../../../imagenes/iconos/editar_dato.jpg" title="<?=_("EDITAR REGISTRO") ;?>" width="35" height="14" border="0"></a><? } ?></td>
			<td style="text-align:center"><input type="checkbox" name="chbactivar<?=$c;?>" id="chbactivar<?=$c;?>" <?=($reg->ACTIVO==1)?"checked":"";?> onclick="actulizar_status('<?=$reg->IDBENEFICIARIO;?>','<?=$reg->IDAFILIADO;?>',this.checked,this.name,'<?="div-status".$c;?>')" title="<?=_("ACTIVAR/DESACTIVAR") ;?>" <? if($_GET["idbeneficiario"]==$reg->IDBENEFICIARIO and !$_GET["verinfo"]){ ?> disabled <? } ?>/></td>
			<td style="text-align:center"><img src="../../../../imagenes/iconos/historia_s.gif" title="<?=_("HISTORIAL") ;?>"  style="cursor:pointer" onClick="ventana_historialvehiculo('<?=$reg->IDBENEFICIARIO;?>')" ></td>  
		 </tr>	
		<?			 
			 $stylo="";
			}
		?>
	</table>


	<script type="text/javascript">	
	
// **********************  Ajax para autocompletar las calles ********************************//
	new Ajax.Autocompleter('direccion',	'sugeridos',
	"../../../controlador/ajax/ajax_calles.php",
	{
		method: "get",
		paramName: "calle",
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
		selectFirst: true
	}
	);
	
	</script>
	
</form>
</body>
</html>

<script type="text/javascript">	
		
		var validar_func = '';
		var win = null;
		
		function ventana_historialvehiculo(id){
		
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("HISTORIAL -> STATUS BENEFICIARIO")?>',
					width: 350,
					height: 170,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: 'historial_beneficiarios.php?id='+id
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
	 		
		function actulizar_status(id,idcodigo,valor,nombre,nombrediv){
 
			if(valor)	valor=1; else valor=0;			
			if(valor==1)	msg='<?=_("DESEA ACTIVAR EL REGISTRO DEL BENEFICIARIO?")?>.'; else msg='<?=_("DESEA INACTIVAR EL REGISTRO DEL BENEFICIARIO?")?>.';
			
			
			
			if(confirm(msg))
			 {
					new Ajax.Updater(nombrediv, 'g_statusben.php', {
					  parameters: 'id='+id+'&idcodigo='+idcodigo+'&valor='+valor, 
					  method: 'post',
					  onSuccess: function(t){
							if(t.responseText=='INACTIVO')	$(nombrediv).style.background="#FF2F2F"; else $(nombrediv).style.background="#CEFFCE";

						}					  
					});

			 }
			else
			 {
				if(valor==1)	valor=0; else valor=1;	
				$(nombre).checked=valor;	
			 }
				
		}	
		
			
	</script>
		