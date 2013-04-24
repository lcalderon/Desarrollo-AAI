<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../../app/modelo/functions.php');	
	include_once("../../../vista/login/Auth.class.php");

	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	Auth::required();	

	$con->select_db($con->catalogo); 
	
	$Sql_1="SELECT
			  IF(catalogo_mediodepago.CLASEPAGO='TARJETACREDITO','TARJETA DE CREDITO',IF(catalogo_mediodepago.CLASEPAGO='OPERADORTARJETA','OPERADOR TARJETA','OTROS TARJETAS')) AS nombretipo,
			  catalogo_afiliado_medio_pago.IDMEDIOPAGO,
			  catalogo_afiliado_medio_pago.IDAFILIADO,
			  catalogo_mediodepago.DESCRIPCION,
			  catalogo_mediodepago.CLASEPAGO,
			  catalogo_afiliado_medio_pago.NOMBRETITULAR,
			  catalogo_afiliado_medio_pago.NUMEROTARJETA,
			  catalogo_afiliado_medio_pago.CODIGOSEGURIDAD,
			  catalogo_afiliado_medio_pago.ID,
			  catalogo_afiliado_medio_pago.DIGITOVERIFICADOR,
			  catalogo_afiliado_medio_pago.IDDOCUMENTO,
			  catalogo_afiliado_medio_pago.FECHAVENCIMIENTO
			FROM catalogo_afiliado_medio_pago
			  INNER JOIN catalogo_mediodepago
				ON catalogo_mediodepago.IDMEDIOPAGO = catalogo_afiliado_medio_pago.IDMEDIOPAGO
			WHERE catalogo_afiliado_medio_pago.IDAFILIADO = '".$_GET["idafiliado"]."'
				ORDER BY catalogo_mediodepago.IDMEDIOPAGO
			";
 
	$result=$con->query($Sql_1);
	
	list($array_aniovalido,$numero_mes)=mostrarAnio_valido();
	 
	$Sql_2="SELECT			  
			  catalogo_afiliado_medio_pago.IDMEDIOPAGO,
			  catalogo_afiliado_medio_pago.IDAFILIADO,
			  catalogo_mediodepago.DESCRIPCION,
			  catalogo_mediodepago.CLASEPAGO,
			  catalogo_afiliado_medio_pago.ID,
			  catalogo_afiliado_medio_pago.NOMBRETITULAR,
			  catalogo_afiliado_medio_pago.NUMEROTARJETA,
			  catalogo_afiliado_medio_pago.CODIGOSEGURIDAD,
			  catalogo_afiliado_medio_pago.DIGITOVERIFICADOR,
			  catalogo_afiliado_medio_pago.IDDOCUMENTO,
			  catalogo_afiliado_medio_pago.FECHAVENCIMIENTO
			FROM catalogo_afiliado_medio_pago
			  INNER JOIN catalogo_mediodepago
				ON catalogo_mediodepago.IDMEDIOPAGO = catalogo_afiliado_medio_pago.IDMEDIOPAGO
			WHERE catalogo_afiliado_medio_pago.IDAFILIADO = ".$_GET["idafiliado"]."
				AND catalogo_afiliado_medio_pago.ID= '".$_GET["id"]."'";
	
	$rsmediopg=$con->query($Sql_2);
	if($_GET["id"] and !$_POST["rdtipo"])	$rowmed = $rsmediopg->fetch_object();	

	if($_POST["rdtipo"])	$clasepago=$_POST["rdtipo"];	else	$clasepago=$rowmed->CLASEPAGO;
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
<style type="text/css">
<!--
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>


		<script type="text/javascript">
		function validarIngreso() {
			
			if(document.form1.cmbtipotarjeta.value==""){
					  alert("SELECCIONE EL OPERADOR.");
					  document.form1.cmbtipotarjeta.focus();
					  return (false);
			   } 	
			 else if(document.form1.txttitular.value==""){
					  alert("INGRESE EL TITULAR DE LA TARJETA.");
					  document.form1.txttitular.focus();
					  return (false);
			   }  		
			   else if(document.form1.txtiddocumento.value==""){
					  alert("INGRESE EL IDDOCUMENTO DEL TITULAR.");
					  document.form1.txtiddocumento.focus();
					  return (false);
			   }  		 
			   else if(document.form1.txtnumtarjeta.value==""){
					  alert("INGRESE EL NUMERO DE TARJETA.");
					  document.form1.txtnumtarjeta.focus();
					  return (false);
			   }
			<?			
				if($_POST["rdtipo"]!="RETAIL")
				 {
			?> 			   
			   else if(document.form1.txtdigitover.value==""){
					  alert("INGRESE EL DIGITO VERIFICADOR.");
					  document.form1.txtdigitover.focus();
					  return (false);
			   }  		

			<?		
				 }		
			?>  

			if(confirm('<?=(!$_GET["id"])?_("DESEA AGREGAR UN NUEVO REGISTRO?."):_("ESTA SEGURO QUE DESEA ACTUALIZAR LOS CAMBIOS?.") ;?>'))		  
			 {
					document.form1.action="gformapago.php" ;
					document.form1.submit();
			 }
			
				return (false);	 

           }  	
		 
		</script>
</head>
<body>
<form id="form1" name="form1" method="post" action="" onSubmit="return validarIngreso(this)">
<input name="idafiliado" type="hidden" id="idafiliado" value="<?=$_GET["idafiliado"];?>" />
<input name="id" type="hidden" id="id" value="<?=$_GET["id"];?>" />
  
  <table width="708" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#003366" style="border:1px solid #003366"  >
        <tr>
          <td><span class="style1"><?=_("FORMA DE PAGO") ;?></span></td>
        </tr>
        <tr  bgcolor="#D7D7FF">
          <td><input type="radio" name="rdtipo" id="radio" value="OPERADORTARJETA" <? if($_POST["rdtipo"]=="OPERADORTARJETA" or $rowmed->CLASEPAGO=="OPERADORTARJETA")	echo "checked ";?><? if($rowmed->CLASEPAGO=="OPERADORTARJETA" or !$_GET["id"]) echo ""; else echo "disabled";?> onclick="form1.submit()" />
            <?=_("OPERADOR TARJETA") ;?></td>
        </tr>
        <tr bgcolor="#D7D7FF">
          <td><input type="radio" name="rdtipo" id="radio2" value="TARJETACREDITO" <? if($_POST["rdtipo"]=="TARJETACREDITO" or $rowmed->CLASEPAGO=="TARJETACREDITO")	echo "checked ";?> <? if($rowmed->CLASEPAGO=="TARJETACREDITO" or !$_GET["id"]) echo ""; else echo "disabled"; ?> onclick="form1.submit()" />
            <?=_("TARJETA DE CREDITO") ;?></td>
        </tr>
        <tr bgcolor="#D7D7FF">
          <td><input type="radio" name="rdtipo" id="radio3" value="RETAIL" <? if($_POST["rdtipo"]=="RETAIL" or $rowmed->CLASEPAGO=="RETAIL")	echo "checked "; ?> <? if($rowmed->CLASEPAGO=="RETAIL" or !$_GET["id"]) echo ""; else echo "disabled"; ?>  onclick="form1.submit()"/>
           <?=_("OTRAS TARJETAS") ;?></td>
        </tr>
 
      </table></td>
      <td><table width="508" border="0" cellpadding="1" cellspacing="1" bgcolor="#E6E6FF" style="border:1px solid #003366">
        <tr  >
          <td width="97"><?=_("OPERADOR") ;?></td>
          <td colspan="3"><?
				$sql="select IDMEDIOPAGO,DESCRIPCION from catalogo_mediodepago where CLASEPAGO='$clasepago' order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtipotarjeta",$rowmed->IDMEDIOPAGO,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?></td>
          </tr>
        <tr>
          <td><?=_("NOMBRE TITULAR") ;?></td>
          <td width="150"><input name="txttitular" type="text" id="txttitular" size="25" maxlength="23" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$rowmed->NOMBRETITULAR;?>" /></td>
          <td width="78"><?=_("IDDOCUMENTO") ;?></td>
          <td width="153"><input name="txtiddocumento" type="text" id="txtiddocumento" size="15" maxlength="13" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);"  value="<?=$rowmed->IDDOCUMENTO;?>" onKeyPress="return validarnum(event)"/> <input name="txtdigitover" type="text" id="txtdigitover" size="7" maxlength="6" class="classtexto" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" value="<?=$rowmed->DIGITOVERIFICADOR;?>" onkeypress="return validarnum(event)" title="Digito Verificador"/></td>
        </tr>
	<?			
		if(($rowmed->CLASEPAGO!="" and $rowmed->CLASEPAGO!="RETAIL") or  ($_POST["rdtipo"] and $_POST["rdtipo"]!="RETAIL"))
		 {
	?>        
		<tr>
          <td>#<?=_("TARJETA");?></td>
          <td><input name="txtnumtarjeta" type="text" id="txtnumtarjeta" size="21" maxlength="16" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" value="<?=$rowmed->NUMEROTARJETA;?>" onKeyPress="return validarnum(event)"/></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
		</tr>
        <tr>
          <td><?=_("COD. SEGURIDAD") ;?></td>
          <td><input name="txtcodigoseg" type="text" class="classtexto" id="txtcodigoseg" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="10" maxlength="8" value="<?=$rowmed->CODIGOSEGURIDAD;?>"/></td>
          <td height="20"><?=_("FECHA VENC.") ;?></td>
          <td height="20"><?
				$con->cmb_array("cmbmes",$numero_mes,substr($rowmed->FECHAVENCIMIENTO,0,2),$event="class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
			?>/<?
				$con->cmb_array("cmbanio",$array_aniovalido,substr($rowmed->FECHAVENCIMIENTO,3,2),$event="class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
			?><strong>(mm/yy)</strong></td>		  
          </tr>
       
	<?		
		 }
		else
		 {
	?>  		
		<tr>
          <td>#<?=_("TARJETA") ;?></td>
          <td><input name="txtnumtarjeta" type="text" id="txtnumtarjeta" value="<?=$rowmed->NUMEROTARJETA;?>"  size="21" maxlength="16" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" /></td>
          <td height="20"><?=_("FECHA VENC.") ;?></td>
          <td height="20"><?
				$con->cmb_array("cmbmes",$numero_mes,substr($rowmed->FECHAVENCIMIENTO,0,2),$event="class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
			?>/<?
				$con->cmb_array("cmbanio",$array_aniovalido,substr($rowmed->FECHAVENCIMIENTO,3,2),$event="class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1")
			?><strong>(mm/yy)</strong></td>	
		</tr>	
	     <tr>
          <td>&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          </tr>
 
	<?		
		 }		
	?>  		

      </table></td>
    </tr>
  </table>
  <input type="submit" name="btnaceptar" id="btnaceptar" value="<?=_("GRABAR") ;?>" style="font-weight:bold;font-size:10px;" onclick="registrar_formapago()"/>
 <?	if(!$_GET["id"]) { ?> <input type="button" name="btnclose" id="btnclose" value="<?=_("CERRAR") ;?>" style="font-size:10px;" onclick="self.close();"/> <?	}else { ?>  <input type="button" name="btncancelar" id="btncancelar" value="CANCELAR" style="font-size:10px;" onClick="reDirigir('formapago.php?idafiliado=<?=$_GET["idafiliado"];?>')" /><?	} ?>
  <br />
  <p></p>  
<table width="100%" border="0" cellpadding="1" cellspacing="1" style="font-size:9px" >       
		<tr>
			<td width="74" bgcolor="#333333"><div align="center"><span class="style1"><?=_("FORMA");?></span></div></td>
			<td width="129" bgcolor="#333333"><div align="center"><span class="style1"><?=_("OPERADOR") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("NOBRETITULAR") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("#IDDOCUMENTO") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("#TARJETA") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("DIGITOVERIF.") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("COD.SEGURIDAD") ;?></span></div></td>
			<td width="115" bgcolor="#333333"><div align="center"><span class="style1"><?=_("FECHAVENCE") ;?></span></div></td>
	  </tr>
		<?
			$i=0;
			while($row=$result->fetch_object())
			 {
			 if($i%2==0) $fondo='#CCCCCC'; else $fondo='#FFFFFF';
		?>
		<tr>
			<td bgcolor="<?=$fondo; ?>" style="text-align:center" width="60%"><?=$row->nombretipo; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%"><?=$row->DESCRIPCION; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->NOMBRETITULAR; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->IDDOCUMENTO; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->NUMEROTARJETA; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->DIGITOVERIFICADOR; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->CODIGOSEGURIDAD; ?></td>
			<td bgcolor="<?=$fondo; ?>" width="20%" style="text-align:center"><?=$row->FECHAVENCIMIENTO; ?></td>
			<td width="20%" style="text-align:center"><a href="formapago.php?idafiliado=<?=$_GET["idafiliado"]; ?>&id=<?=$row->ID; ?>">Editar</a></td>
			<td width="20%" style="text-align:center"><a href="#" onclick="confirmaRespuesta('ESTAS SERGURO QUE DESEAS ELIMINAR ESTE REGISTRO?','eliminarpago.php?idafiliado=<?=$_GET["idafiliado"]; ?>&id=<?=$row->ID; ?>')">Eliminar</a></td>
		</tr>                  
        <?
				$i=$i+1;
			 }
		?>
  </table>  
	
</form>
</body>
</html>